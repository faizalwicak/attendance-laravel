<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Leave;
use App\Models\Record;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {

        $grade_id = $request->get('grade');

        if ($grade_id) {
            $grade = Grade::where('id', $grade_id)
                ->where('school_id', auth()->user()->school_id)
                ->orderBy('name')
                ->first();
        } else {
            $grade = Grade::where('school_id', auth()->user()->school_id)
                ->orderBy('name')
                ->first();
        }

        $users = [];
        if ($grade) {
            $users = User::where('school_id', auth()->user()->school_id)
                ->where('grade_id', $grade->id)
                ->get();
        }

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
            'selectedGrade' => $grade
        ];

        return view('home', $params);
    }

    public function accept($id)
    {
        $record = Absent::where('id', $id)
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('school_id', auth()->user()->school_id);
            })
            ->first();
        if (!$record || $record->status != 'WAITING') {
            return abort('404');
        }

        $record->update(['status' => 'ACCEPT']);

        return redirect('/home')->with('success', 'Data berhasil disimpan.');
    }

    public function decline($id)
    {
        $record = Absent::where('id', $id)
            ->with('user')
            ->whereHas('user', function ($query) {
                $query->where('school_id', auth()->user()->school_id);
            })
            ->first();
        if (!$record || $record->status != 'WAITING') {
            return abort('404');
        }

        $record->update(['status' => 'DECLINE']);

        return redirect('/home')->with('success', 'Data berhasil disimpan.');
    }
}
