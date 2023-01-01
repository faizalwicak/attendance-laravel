<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('school_id', auth()->user()->school_id)
            ->orderBy('updated_at', 'DESC')
            ->get();

        return view('notification-index', ['title' => 'Daftar Pengumuman', 'notifications' => $notifications]);
    }

    public function create()
    {
        return view('notification-form', ['title' => 'Tambah Pengumuman', 'notification' => null]);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'title' => 'required|max:30',
            'message' => 'required|max:255',

        ], [
            'title.required' => 'Judul tidak boleh kosong.',
            'title.max' => 'Judul maksimal 30 karakter.',
            'message.required' => 'Pesan tidak boleh kosong.',
            'message.max' => 'Pesan maksimal 255 karakter.',
        ]);

        $validateData['school_id'] = auth()->user()->school_id;
        Notification::create($validateData);

        return redirect('/notification')
            ->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit($id)
    {
        $notification = Notification::where('id', $id)
            ->where('school_id', auth()->user()->school_id)
            ->first();

        if (!$notification) {
            return abort(404);
        }
        return view('notification-form', ['title' => 'Edit Pengumuman', 'notification' => $notification]);
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::where('id', $id)
            ->where('school_id', auth()->user()->school_id)
            ->first();

        if (!$notification) {
            return abort(404);
        }

        $validateData = $request->validate([
            'title' => 'required|max:30',
            'message' => 'required|max:255',

        ], [
            'title.required' => 'Judul tidak boleh kosong.',
            'title.max' => 'Judul maksimal 30 karakter.',
            'message.required' => 'Pesan tidak boleh kosong.',
            'message.max' => 'Pesan maksimal 255 karakter.',
        ]);

        $notification->update($validateData);

        return redirect('/notification')
            ->with('success', 'Pengumuman berhasil disimpan.');
    }

    public function destroy($id)
    {
        $notification = Notification::where('id', $id)
            ->where('school_id', auth()->user()->school_id)
            ->first();

        if (!$notification) {
            return abort(404);
        }
        $notification->delete();

        return redirect('/notification')
            ->with('success', 'Notification berhasil dihapus.');
    }
}
