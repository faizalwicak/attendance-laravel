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

        $users = [];

        $grades = Grade::where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->with([
                'users',
                'users.records' => function ($query) {
                    $query->where('date', date('Y-m-d'));
                },
            ])
            ->get();

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

        $leaves = Leave::with(['record'])->whereHas('record', function ($query) {
            $query->where('date', date('Y-m-d'));
        })->get();

        debugbar()->info($leaves);

        $params = [
            'title' => 'Beranda',
            'leaves' => $leaves,
            'users' => $users,
            'grades' => $grades,
            'grade_array' => $grade_array,
            'aggregate' => $aggregate,
        ];

        return view('overview', $params);
    }
}
