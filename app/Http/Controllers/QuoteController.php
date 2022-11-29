<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::where('school_id', auth()->user()->school_id)->orderBy('created_at', 'DESC')->get();

        return view('quote-index', ['title' => 'Daftar Quote', 'quotes' => $quotes]);
    }

    public function create()
    {
        return view('quote-form', ['title' => 'Tambah Quote', 'quote' => null]);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'message' => 'required|max:255',
            'active' => 'required|boolean',
        ], [
            'name.required' => 'Pesan tidak boleh kosong.',
            'name.max' => 'Pesan maksimal 255 karakter.',
            'active.required' => 'Status tidak boleh kosong.',
            'active.boolean' => 'Status tidak valid.',
        ]);

        if ($validateData['active'] == '1') {
            Quote::where('active', 1)->update(['active' => 0]);
        }
        $validateData['school_id'] = auth()->user()->school_id;
        Quote::create($validateData);

        return redirect('/quote')
            ->with('success', 'Quote berhasil dibuat.');
    }

    public function edit($id)
    {
        $quote = Quote::where('id', $id)->where('school_id', auth()->user()->school_id)->first();
        if (!$quote) {
            return abort(404);
        }
        return view('quote-form', ['title' => 'Edit Quote', 'quote' => $quote]);
    }

    public function update(Request $request, $id)
    {
        $quote = Quote::where('id', $id)->where('school_id', auth()->user()->school_id)->first();
        if (!$quote) {
            return abort(404);
        }

        $validateData = $request->validate([
            'message' => 'required|max:255',
            'active' => 'required|boolean',
        ], [
            'message.required' => 'Pesan tidak boleh kosong.',
            'message.max' => 'Pesan maksimal 255 karakter.',
            'active.required' => 'Status tidak boleh kosong.',
            'active.boolean' => 'Status tidak valid.',
        ]);

        if ($validateData['active'] == '1') {
            Quote::where('active', 1)->update(['active' => 0]);
        }

        $quote->update($validateData);

        return redirect('/quote')
            ->with('success', 'Quote berhasil disimpan.');
    }

    public function destroy($id)
    {
        $quote = Quote::where('id', $id)->where('school_id', auth()->user()->school_id)->first();
        if (!$quote) {
            return abort(404);
        }
        $quote->delete();

        return redirect('/quote')
            ->with('success', 'Quote berhasil dihapus.');
    }
}
