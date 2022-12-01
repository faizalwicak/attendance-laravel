<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Record;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $records = Record::where('user_id', auth()->user()->id)
            ->where('is_leave', 1)
            ->with('leave')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($records);
    }

    public function create(Request $request)
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

        $record = Record::where('date', $data['date'])
            ->where('user_id', auth()->user()->id)
            ->where('is_leave', 1)
            ->first();

        if ($record) {
            return response()->json(['message' => 'Izin pada tanggal ' . date('d-m-Y', strtotime($data['date'])) . ' telah ada.'], 422);
        }

        $record = Record::where('date', $data['date'])
            ->where('user_id', auth()->user()->id)
            ->first();

        if ($record) {
            $record->update([
                'is_leave' => 1
            ]);
        } else {
            $record = Record::create([
                'user_id' => auth()->user()->id,
                'date' => $data['date'],
                'is_leave' => 1,
            ]);
        }

        if ($data['file'] != null) {
            $fileName = auth()->user()->username . '_' . time() . '.' . $data['file']->extension();
            $data['file']->move(public_path('images/leave'), $fileName);
            $data['file'] = $fileName;
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

        return response()->json([
            'message' => 'izin berhasil dikirim',
        ]);
    }
}
