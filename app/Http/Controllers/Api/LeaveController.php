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
            'date' => 'required|date'
        ], [
            'type.required' => 'Jenis izin tidak boleh kosong.',
            'type.in' => 'Jenis izin tidak valid.',
            'description.required' => 'Keterangan tidak boleh kosong.',
            'description.max' => 'Keterangan maksimal 255 karaketer.',
            'date.required' => 'Tanggal tidak boleh kosong.',
            'date.date' => 'Tanggal tidak valid.',
        ]);

        $record = Record::where('date', $data['date'])
            ->where('user_id', auth()->user()->id)
            ->where('is_leave', 1)
            ->first();

        // $record->leave()->update(['description' => 'adsf']);
        // return response()->json(['message' => $record->leave()], 422);

        if ($record) {
            return response()->json(['message' => 'Izin pada tanggal ' . $data['date'] . ' telah ada.'], 422);
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

        $leave = Leave::where('record_id', $record->id)->first();

        if ($leave) {
            $leave->update([
                'type' => $data['type'],
                'description' => $data['description'],
                'leave_status' => 'WAITING',
            ]);
        } else {
            Leave::create([
                'record_id' => $record->id,
                'type' => $data['type'],
                'description' => $data['description'],
                'leave_status' => 'WAITING',
            ]);
        }

        return response()->json([
            'message' => 'izin berhasil dikirim',
        ]);
    }
}
