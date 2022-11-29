<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absent;
use Illuminate\Http\Request;

class AbsentController extends Controller
{

    public function index() {
        $absents = Absent::where('user_id', auth()->user()->id)->orderBy('date', 'desc')->get();
        return response()->json($absents);
    }

    public function create(Request $request) {
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

        $absent = Absent::where('date', $data['date'])->where('user_id', auth()->user()->id)->first();
        if ($absent) {
            return response()->json(['message' => 'Izin pada tanggal '.$data['date'].' telah ada.'], 422);
        }
        
        $data['user_id'] = auth()->user()->id;
        $data['status'] = 'WAITING';

        Absent::create($data);

        return response()->json([
            'message' => 'izin berhasil dikirim',
        ]);
    }

    public function destroy($id) {
        $absent = Absent::where('user_id', auth()->user()->id)->where('id', $id)->where('status', 'WAITING')->first();

        if ($absent == null) {
            return response()->json(['message'=> 'Izin tidak ditemukan.'], 404);
        }

        $absent->delete();
        return response()->json(['message'=> 'Izin berhasil dihapus.']);
    }
}
