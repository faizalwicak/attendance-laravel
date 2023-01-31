<?php

namespace App\Http\Controllers;

use App\Exports\ExportReport;
use App\Models\Event;
use App\Models\Grade;
use App\Models\Leave;
use App\Models\Record;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RecordController extends Controller
{

    private function processRecordMonth(Request $request, $is_export)
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
                if ($is_export) {
                    array_push($a, "A");
                } else {
                    array_push($a, [$u->id, $d, "A"]);
                }
            }
            $total = count($dateString);
            $attend = 0;
            $sick = 0;
            $leave = 0;
            $onTime = 0;
            $late = 0;

            foreach ($u->records as $r) {
                $index = array_search($r->date, $dateString);
                $status = 'A';
                if ($r->is_leave) {
                    if ($r->leave->type == 'SICK' && $r->leave->leave_status == 'ACCEPT') {
                        $sick += 1;
                        $status = 'S';
                    } else if ($r->leave->type == 'LEAVE' && $r->leave->leave_status == 'ACCEPT') {
                        $leave += 1;
                        $status = 'I';
                    }
                } else {
                    if ($r->attend->clock_in_status == "ON_TIME") {
                        $onTime++;
                        $status = 'TW (' . date('H:i', strtotime($r->attend->clock_in_time)) . ')';
                    } else {
                        $late++;
                        $status = 'TL (' . date('H:i', strtotime($r->attend->clock_in_time)) . ')';
                    }
                    $attend += 1;
                }

                if ($is_export) {
                    $a[$index + 4] = $status;
                } else {
                    $a[$index + 4] = [$u->id, $r->date, $status];
                }
            }
            array_push($a, $total);
            array_push($a, $attend);
            array_push($a, $onTime);
            array_push($a, $late);
            array_push($a, $sick);
            array_push($a, $leave);
            array_push($a, $total - $attend - $sick - $leave);
            array_push($userArray, $a);
        }

        return [
            'title' => "Laporan Bulanan",
            'users' => $users,
            'userArray' => $userArray,
            'grades' => $grades,
            'days' => $dateString,
            'selectedGrade' => $selectedGrade,
            'selectedMonth' => $selectedMonth
        ];
    }

    public function recordMonth(Request $request)
    {

        $params = $this->processRecordMonth($request, False);

        return view('record-month', $params);
    }

    public function recordMonthExport(Request $request)
    {

        $params = $this->processRecordMonth($request, true);

        $grade = Grade::findOrFail($params['selectedGrade']);

        return Excel::download(
            new ExportReport($params['userArray'], $params['days']),
            'Laporan ' . $grade->name . ' (' . $params['selectedMonth'] . ').xlsx'
        );
    }

    public function recordDay(Request $request)
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
            'title' => "Presensi",
            'users' => $users,
            'grades' => $grades,
            'selectedGrade' => $selectedGrade,
            'selectedDay' => $selectedDay
        ];

        return view('record-day', $params);
    }

    public function detailByQuery($user_id, $day)
    {
        if ($user_id == null || $user_id == "" || $day == null || $day == "") {
            return abort(404);
        }
        $user = User::where('id', $user_id)->first();
        if ($user == null) {
            return abort(404);
        }

        if (auth()->user()->role == 'ADMIN') {
        } else {
            $admin = User::find(auth()->user()->id);
            $allowedGrades = $admin->grades()->get();
            $allowedGradesArr = [];
            foreach ($allowedGrades as $g) {
                array_push($allowedGradesArr, $g->id);
            }

            if (!in_array($user->grade_id, $allowedGradesArr)) {
                return abort(404);
            }
        }

        $record = Record::where('user_id', $user_id)
            ->where('date', $day)
            ->with('attend')
            ->with('leave')
            ->first();

        return view('record-detail', [
            'title' => 'Detail Presensi',
            'user' => $user,
            'record' => $record,
            'selectedDay' => $day,
        ]);
    }
}
