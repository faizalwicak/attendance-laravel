<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Grade;
use App\Models\Leave;
use App\Models\Record;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;

class RecordController extends Controller
{

    public function records_month(Request $request)
    {

        $gradeId = $request->get('grade');
        $selectedMonth = $request->get('month');

        if ($gradeId == null) {
            $grade = Grade::where('school_id', auth()->user()->school_id)
                ->orderBy('name')
                ->first();

            if (!$grade) {
                return redirect('/grade')->with('warning', 'Kelas tidak ditemukan, buat kelas terlebih dahulu.');
            }
        } else {
            $grade = Grade::where('id', $gradeId)
                ->where('school_id', auth()->user()->school_id)
                ->orderBy('name')
                ->first();
        }

        if ($selectedMonth == null) {
            $selectedMonth = date('Y-m');
        }

        $grades = Grade::where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->get();

        $currentDate = new DateTime($selectedMonth . "-01");

        $holiday = Event::where('type', 'HOLIDAY')
            ->whereYear('date', $currentDate->format('Y'))
            ->whereMonth('date', $currentDate->format('m'))
            ->get();

        $holidayArray = [];
        foreach ($holiday as $h) {
            array_push($holidayArray, date('j', strtotime($h->date)));
        }

        $dateArray = [];
        $dateString = [];

        $r = 1;
        while ($r) {

            if (in_array($currentDate->format('w'), ['1', '2', '3', '4', '5']) && !in_array($currentDate->format('j'), $holidayArray)) {
                array_push($dateString, $currentDate->format('Y-m-d'));
                $dateArray[$currentDate->format('j')] = [];
            }

            if ($currentDate->format('j') == $currentDate->format('t')) $r = 0;
            $currentDate->modify('+1 day');
        }

        $users = User::where('school_id', auth()->user()->school_id)
            ->where('grade_id', $grade->id)
            ->with([
                'records' => function ($query) use ($dateString) {
                    $query->whereIn('date', $dateString);
                },
                'records.attend',
                'records.leave'
            ])
            ->get();

        $userArray = [];
        foreach ($users as $u) {
            $a = [$u->image != null ? $u->image : "", $u->username, $u->name, $u->gender == "MALE" ? "L" : "P"];
            foreach ($dateString as $d) {
                array_push($a, "A");
            }
            $total = count($dateString);
            $attend = 0;
            $sick = 0;
            $leave = 0;

            foreach ($u->records as $r) {
                $index = array_search($r->date, $dateString);
                $status = '';
                if ($r->is_leave) {
                    if ($r->leave->type == 'SICK') {
                        $sick += 1;
                        $status = 'S';
                    } else if ($r->leave->type == 'LEAVE') {
                        $leave += 1;
                        $status = 'I';
                    }
                } else {
                    $status = $r->attend->clock_in_status == "ON_TIME" ? "TW" : "TL";
                    $attend += 1;
                }

                $a[$index + 4] = $status;
            }
            array_push($a, $total);
            array_push($a, $attend);
            array_push($a, $sick);
            array_push($a, $leave);
            array_push($a, $total - $attend - $sick - $leave);
            debugbar()->info($a);
            array_push($userArray, $a);
        }

        $params = [
            'title' => "Laporan Bulanan",
            'users' => $users,
            'userArray' => $userArray,
            'grades' => $grades,
            'days' => $dateString,
            'selectedGrade' => $grade,
            'selectedMonth' => $selectedMonth
        ];

        return view('record-month', $params);
    }

    public function records_day(Request $request)
    {

        $gradeId = $request->get('grade');
        $selectedDay = $request->get('day');

        if ($gradeId == null) {
            $grade = Grade::where('school_id', auth()->user()->school_id)
                ->orderBy('name')
                ->first();

            if (!$grade) {
                return redirect('/grade')->with('warning', 'Kelas tidak ditemukan, buat kelas terlebih dahulu.');
            }
        } else {
            $grade = Grade::where('id', $gradeId)
                ->where('school_id', auth()->user()->school_id)
                ->orderBy('name')
                ->first();
        }

        if ($selectedDay == null) {
            $selectedDay = date('Y-m-d');
        }


        $users = User::where('school_id', auth()->user()->school_id)
            ->where('grade_id', $grade->id)
            ->with([
                'records' => function ($query) use ($selectedDay) {
                    $query->where('date', $selectedDay);
                },
                'records.attend',
                'records.leave'
            ])
            ->get();

        $grades = Grade::where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->get();

        $params = [
            'title' => "Laporan Harian",
            'users' => $users,
            'grades' => $grades,
            'selectedGrade' => $grade,
            'selectedDay' => $selectedDay
        ];

        return view('record-day', $params);
    }

    public function record_detail($id)
    {
        $record = Record::where('id', $id)->with('user', 'leave', 'attend')->first();
        if (!$record) {
            return abort(404);
        }

        return view('record-detail', [
            'title' => 'Detail Presensi "' . $record->user->name . ' (' . date('d/m/Y', strtotime($record->date)) . ')"',
            'record' => $record
        ]);
    }

    public function record_leave(Request $request)
    {

        $selectedDay = $request->get('day');

        if ($selectedDay == null) {
            $selectedDay = date('Y-m-d');
        }

        $leaves = Leave::with(['record'])->whereHas('record', function ($query) use ($selectedDay) {
            $query->where('date', $selectedDay);
        })->get();

        $params = [
            'title' => 'Daftar Izin',
            'leaves' => $leaves,
            'selectedDay' => $selectedDay
        ];

        return view('record-leave', $params);
    }

    public function record_status(Request $request, $id)
    {
        $validateData = $request->validate([
            'accept' => 'required|boolean',
        ], [
            'accept.required' => 'Status tidak boleh kosong.',
            'accept.boolean' => 'Status tidak valid.',
        ]);

        $record = Record::where('id', $id)->with('user', 'leave', 'attend')->first();
        if (!$record || !$record->is_leave) return abort(404);

        $record->leave()->update(['leave_status' => $validateData['accept'] ? 'ACCEPT' : 'REJECT']);
        return redirect('/record/' . $id)->with('success', 'Berhasil update data');
    }
}
