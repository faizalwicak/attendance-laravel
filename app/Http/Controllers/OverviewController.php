<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function index()
    {

        $grades = [];

        if (auth()->user()->role == 'ADMIN') {
            $grades = Grade::where('school_id', auth()->user()->school_id)
                ->orderBy('name')
                ->with([
                    'users',
                    'users.records' => function ($query) {
                        $query->where('date', date('Y-m-d'));
                    },
                ])
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
                ->with([
                    'users',
                    'users.records' => function ($query) {
                        $query->where('date', date('Y-m-d'));
                    },
                ])
                ->get();
        }


        $aggregate = [
            'count' => 0,
            'attend' => 0,
            'leave' => 0,
        ];
        $grade_array = [];
        foreach ($grades as $g) {
            $attend = 0;
            $leave = 0;
            foreach ($g->users as $u) {
                if (count($u->records)) {
                    if ($u->records[0]->is_leave == 0) {
                        $attend += 1;
                        $aggregate['attend'] += 1;
                    } else {
                        $leave += 1;
                        $aggregate['leave'] += 1;
                    }
                }
            }
            array_push($grade_array, [
                'id' => $g->id,
                'name' => $g->name,
                'attend' => $attend,
                'leave' => $leave,
                'count' => count($g->users)
            ]);
            $aggregate['count'] += count($g->users);
        }

        $params = [
            'title' => 'Beranda',
            'grade_array' => $grade_array,
            'aggregate' => $aggregate,
        ];

        return view('overview', $params);
    }
}
