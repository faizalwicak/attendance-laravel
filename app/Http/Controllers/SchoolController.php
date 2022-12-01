<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::orderBy('name')->get();
        return view('school-index', ['title' => 'Daftar Sekolah', 'schools' => $schools]);
    }

    public function create()
    {
        return view('school-form', ['title' => 'Tambah Sekolah', 'school' => null]);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'name' => 'required|max:100',
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'distance' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'clock_in.required' => 'Waktu masuk tidak boleh kosong.',
            'clock_in.date_format' => 'Format waktu masuk tidak valid.',
            'clock_out.required' => 'Waktu pulang tidak boleh kosong.',
            'clock_out.date_format' => 'Format waktu pulang tidak valid.',
            'lat.required' => 'Latitude tidak boleh kosong.',
            'lat.numeric' => 'Latitude tidak valid.',
            'lng.required' => 'Longitude tidak boleh kosong.',
            'lng.numeric' => 'Longitude tidak valid.',
            'distance.required' => 'Jarak tidak boleh kosong.',
            'distance.numeric' => 'Jarak tidak valid.',
            'image.image' => 'Logo tidak valid.',
            'image.mimes' => 'File bukan gambar.',
            'image.max' => 'Logo maksimal 2 MB.'
        ]);

        if ($request->image != null) {
            $imageName = uniqid() . time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validateData['image'] = $imageName;
        }

        School::create($validateData);

        return redirect()
            ->route('school.index')
            ->with('success', 'Sekolah berhasil dibuat.');
    }

    public function edit(School $school)
    {
        return view('school-form', ['title' => 'Edit "' . $school->name . '"', 'school' => $school]);
    }

    public function update(Request $request, School $school)
    {
        $validateData = $request->validate([
            'name' => 'required|max:100',
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'distance' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'clock_in.required' => 'Waktu masuk tidak boleh kosong.',
            'clock_in.date_format' => 'Format waktu masuk tidak valid.',
            'clock_out.required' => 'Waktu pulang tidak boleh kosong.',
            'clock_out.date_format' => 'Format waktu pulang tidak valid.',
            'lat.required' => 'Latitude tidak boleh kosong.',
            'lat.numeric' => 'Latitude tidak valid.',
            'lng.required' => 'Longitude tidak boleh kosong.',
            'lng.numeric' => 'Longitude tidak valid.',
            'distance.required' => 'Jarak tidak boleh kosong.',
            'distance.numeric' => 'Jarak tidak valid.',
            'image.image' => 'Logo tidak valid.',
            'image.mimes' => 'File bukan gambar.',
            'image.max' => 'Logo maksimal 2 MB.'
        ]);

        if ($request->image != null) {
            $imageName = uniqid() . time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validateData['image'] = $imageName;
        }

        $school->update($validateData);

        return redirect()
            ->route('school.index')
            ->with('success', 'Sekolah berhasil disimpan.');
    }

    public function destroy(School $school)
    {
        $school->delete();

        return redirect()
            ->route('school.index')
            ->with('success', 'Sekolah berhasil dihapus.');
    }


    public function updateSchoolPage(Request $request)
    {
        $school = School::find($request->user()->school_id)->first();
        return view('school-me-form', ['title' => 'Edit Sekolah', 'school' => $school]);
    }

    public function updateSchoolAction(Request $request)
    {
        $school = School::find($request->user()->school_id)->first();
        $validateData = $request->validate([
            'name' => 'required|max:100',
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'distance' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'name.required' => 'Nama tidak boleh kosong.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'clock_in.required' => 'Waktu masuk tidak boleh kosong.',
            'clock_in.date_format' => 'Format waktu masuk tidak valid.',
            'clock_out.required' => 'Waktu pulang tidak boleh kosong.',
            'clock_out.date_format' => 'Format waktu pulang tidak valid.',
            'lat.required' => 'Latitude tidak boleh kosong.',
            'lat.numeric' => 'Latitude tidak valid.',
            'lng.required' => 'Longitude tidak boleh kosong.',
            'lng.numeric' => 'Longitude tidak valid.',
            'distance.required' => 'Jarak tidak boleh kosong.',
            'distance.numeric' => 'Jarak tidak valid.',
            'image.image' => 'Logo tidak valid.',
            'image.mimes' => 'File bukan gambar.',
            'image.max' => 'Logo maksimal 2 MB.'
        ]);

        if ($request->image != null) {
            $imageName = uniqid() . time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validateData['image'] = $imageName;
        }

        $school->update($validateData);

        return redirect('/me/school')
            ->with('success', 'Sekolah berhasil disimpan.');
    }
}
