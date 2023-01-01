<?php

namespace App\Http\Controllers;

use App\Models\ImportantLink;
use Illuminate\Http\Request;

class ImportantLinkController extends Controller
{
    public function index()
    {
        $links = ImportantLink::where('school_id', auth()->user()->school_id)
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('important-link-index', ['title' => 'Daftar Link Penting', 'links' => $links]);
    }

    public function create()
    {
        return view('important-link-form', ['title' => 'Tambah Link Penting', 'link' => null]);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'title' => 'required|max:100',
            'link' => 'required|max:100',
        ], [
            'title.required' => 'Judul tidak boleh kosong.',
            'title.max' => 'Judul maksimal 100 karakter.',
            'link.required' => 'Link tidak boleh kosong.',
            'link.max' => 'Link maksimal 100 karakter.',
        ]);

        $validateData['school_id'] = auth()->user()->school_id;
        ImportantLink::create($validateData);

        return redirect('/important-link')->with('success', 'Link berhasil disimpan.');
    }

    public function edit($id)
    {
        $link = ImportantLink::where('id', $id)
            ->where('school_id', auth()->user()->school_id)
            ->firstOrFail();

        return view('important-link-form', ['title' => 'Edit Link', 'link' => $link]);
    }

    public function update(Request $request, $id)
    {
        $link = ImportantLink::where('id', $id)
            ->where('school_id', auth()->user()->school_id)
            ->firstOrFail();

        $validateData = $request->validate([
            'title' => 'required|max:100',
            'link' => 'required|max:100',
        ], [
            'title.required' => 'Judul tidak boleh kosong.',
            'title.max' => 'Judul maksimal 100 karakter.',
            'link.required' => 'Link tidak boleh kosong.',
            'link.max' => 'Link maksimal 100 karakter.',
        ]);

        $link->update($validateData);

        return redirect('/important-link')
            ->with('success', 'Link berhasil disimpan.');
    }

    public function destroy($id)
    {
        $link = ImportantLink::where('id', $id)
            ->where('school_id', auth()->user()->school_id)
            ->firstOrFail();

        $link->delete();

        return redirect('/important-link')
            ->with('success', 'Link berhasil dihapus.');
    }
}
