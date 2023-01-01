<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Record;
use App\Models\User;
use Illuminate\Http\Request;

class LeaveController extends Controller
{

    public function index(Request $request)
    {

        $selectedDay = $request->get('day');

        if ($selectedDay == null) {
            $selectedDay = date('Y-m-d');
        }

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

    public function create($user_id, $day)
    {
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

        return view('record-leave-form', [
            'title' => 'Tambah Izin',
            'selectedDay' => $day,
            'selectedUser' => $user_id,
            'user' => $user
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:SICK,LEAVE',
            'description' => 'required|max:255',
            'date' => 'required|date',
            'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'user_id.required' => 'Siswa tidak boleh kosong.',
            'user_id.exists' => 'Siswa tidak ditemukan.',
            'type.required' => 'Jenis izin tidak boleh kosong.',
            'type.in' => 'Jenis izin tidak valid.',
            'description.required' => 'Keterangan tidak boleh kosong.',
            'description.max' => 'Keterangan maksimal 255 karaketer.',
            'date.required' => 'Tanggal tidak boleh kosong.',
            'date.date' => 'Tanggal tidak valid.',
            'file.image' => 'Foto tidak valid.',
            'file.mimes' => 'Foto tidak valid.',
            'file.max' => 'Foto maksimal 2 MB.'
        ]);

        $user = User::where('id', $data['user_id'])->first();
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

        $record = Record::where('date', $data['date'])
            ->where('user_id', $data['user_id'])
            ->where('is_leave', 1)
            ->first();

        if ($record) {
            return back()->with('error', 'Izin sudah pernah dibuat');
        }

        $record = Record::where('date', $data['date'])
            ->where('user_id', $data['user_id'])
            ->first();

        if ($record) {
            $record->update([
                'is_leave' => 1
            ]);
        } else {
            $record = Record::create([
                'user_id' => $data['user_id'],
                'date' => $data['date'],
                'is_leave' => 1,
            ]);
        }

        $user = User::find($data['user_id']);

        if (isset($data['file']) && $data['file'] != null) {
            $fileName = $user->username . '_' . time() . '.' . $data['file']->extension();
            $data['file']->move(public_path('images/leave'), $fileName);
            $data['file'] = $fileName;
        } else {
            $data['file'] = null;
        }

        $leave = Leave::where('record_id', $record->id)->first();

        if ($leave) {
            $leave->update([
                'type' => $data['type'],
                'description' => $data['description'],
                'leave_status' => 'ACCEPT',
                'file' => $data['file'],
            ]);
        } else {
            Leave::create([
                'record_id' => $record->id,
                'type' => $data['type'],
                'description' => $data['description'],
                'leave_status' => 'ACCEPT',
                'file' => $data['file'],
            ]);
        }

        return redirect('/record/day?grade=' . $user->grade_id . '&day=' . $data['date'])
            ->with('success', 'Izin berhasil disimpan.');
    }

    public function leaveStatus(Request $request)
    {
        $validateData = $request->validate([
            'id' => 'required',
            'accept' => 'required|boolean',
        ], [
            'id' => 'Id tidak boleh kosong.',
            'accept.required' => 'Status tidak boleh kosong.',
            'accept.boolean' => 'Status tidak valid.',
        ]);

        if (auth()->user()->role == 'ADMIN') {
            $record = Record::where('id', $validateData['id'])
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

            $record = Record::where('id', $validateData['id'])
                ->with('user', 'leave', 'attend')
                ->whereHas('user', function ($query) use ($allowedGradesArr) {
                    $query->where('school_id', auth()->user()->school_id)
                        ->whereIn('grade_id', $allowedGradesArr);
                })
                ->first();
        }

        if (!$record || !$record->is_leave) return abort(404);

        $record->leave()->update(['leave_status' => $validateData['accept'] ? 'ACCEPT' : 'REJECT']);
        return back()->with('success', 'Berhasil update data');
    }
}
