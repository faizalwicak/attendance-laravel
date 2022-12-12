<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::where('school_id', Auth::user()->school_id)
            ->orderBy('name')
            ->get();

        return view('grade-index', ['title' => 'Daftar Kelas', 'grades' => $grades]);
    }

    public function create()
    {
        return view('grade-form', ['title' => 'Tambah Kelas', 'grade' => null]);
    }

    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name' => 'required|max:100',
            'grade' => 'required|numeric|in:10,11,12'
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'grade.required' => 'Tingkatan tidak boleh kosong.',
            'grade.numeric' => 'Tingkatan tidak valid.',
            'grade.in' => 'Tingkatan tidak valid.'
        ]);

        $requestData['school_id'] = Auth::user()->school_id;
        Grade::create($requestData);

        return redirect('/grade')
            ->with('success', 'Kelas berhasil dibuat.');
    }

    public function edit(Grade $grade)
    {
        if ($grade->school_id != Auth::user()->school_id) {
            return abort(403);
        }
        return view('grade-form', ['title' => 'Edit "' . $grade->name . '"', 'grade' => $grade]);
    }

    public function update(Request $request, Grade $grade)
    {
        if ($grade->school_id != Auth::user()->school_id) {
            return abort(403);
        }
        $requestData = $request->validate([
            'name' => 'required|max:100',
            'grade' => 'required|numeric|in:10,11,12'
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'grade.required' => 'Tingkatan tidak boleh kosong.',
            'grade.numeric' => 'Tingkatan tidak valid.',
            'grade.in' => 'Tingkatan tidak valid.'
        ]);

        $grade->update($requestData);

        return redirect('/grade')
            ->with('success', 'Kelas berhasil disimpan.');
    }

    public function destroy(Grade $grade)
    {
        if ($grade->school_id != Auth::user()->school_id) {
            return abort(403);
        }

        $grade->delete();

        return redirect('/grade')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
