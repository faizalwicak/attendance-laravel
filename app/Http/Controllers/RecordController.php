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

        $selectedGrade = $request->get('grade');
        $selectedMonth = $request->get('month', date('Y-m'));

        if (auth()->user()->role == 'ADMIN') {
            $grades = Grade::where('school_id', auth()->user()->school_id)
                ->orderBy('name')
                ->get();
        } else {
            $user = User::find(auth()->user()->id);
            $allowedGrades = $user->grades()->get();
            $allowedGradesArr = [];
            foreach ($allowedGrades as $g) {
                array_push($allowedGradesArr, $g->id);
            }

            $grades = Grade::where('school_id', auth()->user()->school_id)
                ->whereIn('id', $allowedGradesArr)
                ->orderBy('name')
                ->get();

            if ($selectedGrade != null && !in_array($selectedGrade, $allowedGradesArr)) {
                $selectedGrade = null;
            }
        }

        $currentDate = new DateTime($selectedMonth . "-01");

        $holiday = Event::where('type', 'HOLIDAY')
            ->where('school_id', auth()->user()->school_id)
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

        $users = [];
        if ($selectedGrade != null) {
            $users = User::where('school_id', auth()->user()->school_id)
                ->where('grade_id', $selectedGrade)
                ->with([
                    'records' => function ($query) use ($dateString) {
                        $query->whereIn('date', $dateString);
                    },
                    'records.attend',
                    'records.leave'
                ])
                ->get();
        }

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
            'selectedGrade' => $selectedGrade,
            'selectedMonth' => $selectedMonth
        ];

        return view('record-month', $params);
    }

    public function records_day(Request $request)
    {

        $selectedGrade = $request->get('grade');
        $selectedDay = $request->get('day', date('Y-m-d'));

        if (auth()->user()->role == 'ADMIN') {
            $grades = Grade::where('school_id', auth()->user()->school_id)
                ->orderBy('name')
                ->get();
        } else {
            $user = User::find(auth()->user()->id);
            $allowedGrades = $user->grades()->get();
            $allowedGradesArr = [];
            foreach ($allowedGrades as $g) {
                array_push($allowedGradesArr, $g->id);
            }

            $grades = Grade::where('school_id', auth()->user()->school_id)
                ->whereIn('id', $allowedGradesArr)
                ->orderBy('name')
                ->get();

            if ($selectedGrade != null && !in_array($selectedGrade, $allowedGradesArr)) {
                $selectedGrade = null;
            }
        }

        $users = [];
        if ($selectedGrade != null) {
            $users = User::where('school_id', auth()->user()->school_id)
                ->where('grade_id', $selectedGrade)
                ->with([
                    'records' => function ($query) use ($selectedDay) {
                        $query->where('date', $selectedDay);
                    },
                    'records.attend',
                    'records.leave'
                ])
                ->get();
        }

        $params = [
            'title' => "Laporan Harian",
            'users' => $users,
            'grades' => $grades,
            'selectedGrade' => $selectedGrade,
            'selectedDay' => $selectedDay
        ];

        return view('record-day', $params);
    }

    public function record_detail($id)
    {
        if (auth()->user()->role == 'ADMIN') {
            $record = Record::where('id', $id)
                ->with('user', 'leave', 'attend')
                ->whereHas('user', function ($query) {
                    $query->where('school_id', auth()->user()->school_id);
                })
                ->first();
        } else {
            $user = User::find(auth()->user()->id);
            $allowedGrades = $user->grades()->get();
            $allowedGradesArr = [];
            foreach ($allowedGrades as $g) {
                array_push($allowedGradesArr, $g->id);
            }

            $record = Record::where('id', $id)
                ->with('user', 'leave', 'attend')
                ->whereHas('user', function ($query) {
                    $query->where('school_id', auth()->user()->school_id);
                })
                ->whereHas('user', function ($query) use ($allowedGradesArr) {
                    $query->whereIn('grade_id', $allowedGradesArr);
                })
                ->first();
        }

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

        // $schoolId = auth()-
        $leaves = [];
        if (auth()->user()->role == 'ADMIN') {
            $leaves = Leave::with('record', 'record.user')
                ->whereHas('record', function ($query) use ($selectedDay) {
                    $query->where('date', $selectedDay);
                })
                ->whereHas('record.user', function ($query) {
                    $query->where('school_id', auth()->user()->school_id);
                })
                ->get();
        } else {
            $user = User::find(auth()->user()->id);
            $allowedGrades = $user->grades()->get();
            $allowedGradesArr = [];
            foreach ($allowedGrades as $g) {
                array_push($allowedGradesArr, $g->id);
            }

            $leaves = Leave::with(['record', 'record.user'])
                ->whereHas('record', function ($query) use ($selectedDay) {
                    $query->where('date', $selectedDay);
                })
                ->whereHas('record.user', function ($query) use ($allowedGradesArr) {
                    $query->where('school_id', auth()->user()->school_id)
                        ->whereIn('grade_id', $allowedGradesArr);
                })
                ->get();
        }

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

        if (auth()->user()->role == 'ADMIN') {
            $record = Record::where('id', $id)
                ->with('user', 'leave', 'attend')
                ->whereHas('user', function ($query) {
                    $query->where('school_id', auth()->user()->school_id);
                })
                ->first();
            if (!$record || !$record->is_leave) return abort(404);
        } else {
            $user = User::find(auth()->user()->id);
            $allowedGrades = $user->grades()->get();
            $allowedGradesArr = [];
            foreach ($allowedGrades as $g) {
                array_push($allowedGradesArr, $g->id);
            }

            $record = Record::where('id', $id)
                ->with('user', 'leave', 'attend')
                ->whereHas('user', function ($query) use ($allowedGradesArr) {
                    $query->where('school_id', auth()->user()->school_id)
                        ->whereIn('grade_id', $allowedGradesArr);
                })
                ->first();
        }

        if (!$record || !$record->is_leave) return abort(404);

        $record->leave()->update(['leave_status' => $validateData['accept'] ? 'ACCEPT' : 'REJECT']);
        return redirect('/record/' . $id)->with('success', 'Berhasil update data');
    }
}
