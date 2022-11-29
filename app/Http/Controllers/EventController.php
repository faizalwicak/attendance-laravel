<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->get('month');

        if ($selectedMonth == null) {
            $selectedMonth = date('Y-m');
        }

        $selectedMonthDate = strtotime($selectedMonth);


        $year = date('Y', $selectedMonthDate);
        $month = date('n', $selectedMonthDate);

        debugbar()->info(date('n', $month));

        $events = Event::where('school_id', auth()->user()->school_id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();

        return view('event-index', ['title' => 'Event', 'events' => $events, 'selectedMonth' => $selectedMonth]);
    }

    public function create()
    {
        return view('event-form', ['title' => 'Tambah Event', 'event' => null]);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'date' => 'required|date',
            'description' => 'required|max:255',
            'type' => 'required|in:HOLIDAY,EVENT',
        ], [
            'date.required' => 'Tanggal tidak boleh kosong.',
            'date.date' => 'Tanggal tidak valid.',
            'description.required' => 'Deskripsi tidak boleh kosong.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
            'type.required' => 'Tipe tidak boleh kosong.',
            'type.in' => 'Tipe tidak valid.'
        ]);

        $validateData['school_id'] = auth()->user()->school_id;
        Event::create($validateData);

        return redirect('/event?month=' . date('Y-m', strtotime($validateData['date'])))
            ->with('success', 'Event berhasil dibuat.');
    }

    public function edit(Event $event)
    {
        return view('event-form', ['title' => 'Edit Event', 'event' => $event]);
    }

    public function update(Request $request, Event $event)
    {
        $validateData = $request->validate([
            'date' => 'required|date',
            'description' => 'required|max:255',
            'type' => 'required|in:HOLIDAY,EVENT',
        ], [
            'date.required' => 'Tanggal tidak boleh kosong.',
            'date.date' => 'Tanggal tidak valid.',
            'description.required' => 'Deskripsi tidak boleh kosong.',
            'description.max' => 'Deskripsi maksimal 255 karakter.',
            'type.required' => 'Tipe tidak boleh kosong.',
            'type.in' => 'Tipe tidak valid.'
        ]);

        $event->update($validateData);

        return redirect('/event?month=' . date('Y-m', strtotime($validateData['date'])))
            ->with('success', 'Event berhasil disimpan.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()
            ->back()
            ->with('success', 'Event berhasil dihapus.');
    }
}
