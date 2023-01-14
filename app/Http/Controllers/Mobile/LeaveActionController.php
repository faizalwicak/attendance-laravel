<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Record;
use App\Models\User;
use Illuminate\Http\Request;

class LeaveActionController extends Controller
{
    public function index()
    {
        $records = Record::where('user_id', auth()->user()->id)
            ->where('is_leave', 1)
            ->with('leave')
            ->orderBy('date', 'desc')
            ->get();

        return view('mobile/leave-list', ['title' => 'Daftar Izin', 'records' => $records]);
    }

    public function create()
    {
        return view('mobile/leave-form', ['title' => 'Tambah Izin']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:SICK,LEAVE',
            'description' => 'required|max:255',
            'date' => 'required|date',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'type.required' => 'Jenis izin tidak boleh kosong.',
            'type.in' => 'Jenis izin tidak valid.',
            'description.required' => 'Keterangan tidak boleh kosong.',
            'description.max' => 'Keterangan maksimal 255 karaketer.',
            'date.required' => 'Tanggal tidak boleh kosong.',
            'date.date' => 'Tanggal tidak valid.',
            'file.required' => 'Foto tidak boleh kosong.',
            'file.image' => 'Foto tidak valid.',
            'file.mimes' => 'Foto tidak valid.',
            'file.max' => 'Foto maksimal 2 MB.'
        ]);

        $user = User::find(auth()->user()->id);

        $record = Record::where('date', $data['date'])
            ->where('user_id', $user->id)
            ->where('is_leave', 1)
            ->first();

        if ($record) {
            return back()->with('error', 'Izin sudah pernah dibuat');
        }

        $record = Record::where('date', $data['date'])
            ->where('user_id', $user->id)
            ->first();

        if ($record) {
            $record->update([
                'is_leave' => 1
            ]);
        } else {
            $record = Record::create([
                'user_id' => $user->id,
                'date' => $data['date'],
                'is_leave' => 1,
            ]);
        }

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
                'leave_status' => 'WAITING',
                'file' => $data['file'],
            ]);
        } else {
            Leave::create([
                'record_id' => $record->id,
                'type' => $data['type'],
                'description' => $data['description'],
                'leave_status' => 'WAITING',
                'file' => $data['file'],
            ]);
        }

        return redirect('/mobile/home')
            ->with('success', 'Izin berhasil disimpan.');
    }
}
