<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index()
    {
        $grade = Grade::where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->first();

        if (!$grade) {
            return redirect('/grade');
        }

        return redirect('/grade/' . $grade->id . '/student');
    }

    public function indexGrade($grade_id)
    {

        $grade = Grade::where('id', $grade_id)
            ->where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->first();

        if (!$grade) {
            return abort(403);
        }

        $users = User::where('role', 'USER')
            ->where('school_id', auth()->user()->school_id)
            ->where('grade_id', $grade_id)
            ->orderBy('username')
            ->get();

        $grades = Grade::where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->get();

        if (count($grades) == 0) {
            return redirect('/grade');
        }

        return view('student-index', ['title' => 'Daftar Siswa', 'users' => $users, 'grades' => $grades, 'selectedGrade' => $grade_id]);
    }

    public function create()
    {
        $grades = Grade::where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->get();
        return view('student-form', ['title' => 'Tambah Siswa', 'user' => null, 'grades' => $grades]);
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'username' => 'required|max:100|unique:users,username',
            'name' => 'required|max:100',
            'grade_id' => 'required|exists:grades,id',
            'password' => 'required|min:6',
            're-password' => 'required|same:password',
            'gender' => 'required|in:MALE,FEMALE',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.max' => 'Username maksimal 100 karakter.',
            'username.unique' => 'Username telah terpakai.',
            'name.required' => 'Nama tidak boleh kosong.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'grade_id.required' => 'Sekolah tidak boleh kosong.',
            'grade_id.exists' => 'Sekolah tidak ditemukan.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.min' => 'Password minimal 6 karakter.',
            're-password.required' => 'Konfirmasi password tidak boleh kosong.',
            're-password.same' => 'Konfirmasi password tidak sama.',
            'gender.required' => 'Jenis Kelamin tidak boleh kosong.',
            'gender.in' => 'Jenis Kelamin tidak valid.',
            'image.image' => 'Foto tidak valid.',
            'image.mimes' => 'Foto tidak valid.',
            'image.max' => 'Foto maksimal 2 MB.'
        ]);

        $validateData['username'] = preg_replace('/\s*/', '', $validateData['username']);
        $validateData['username'] = strtolower($validateData['username']);

        $validateData['school_id'] = Auth::user()->school_id;

        $validateData['role'] = 'USER';
        $validateData['password'] = Hash::make($validateData['password']);

        if ($request->image != null) {
            $imageName = uniqid() . time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validateData['image'] = $imageName;
        }

        User::create($validateData);

        return redirect('/grade/' . $validateData['grade_id'] . '/student')
            ->with('success', 'Siswa berhasil dibuat.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        if ($user->school_id != Auth::user()->school_id || $user->role != 'USER') {
            return abort(403);
        }

        $grades = Grade::where('school_id', auth()->user()->school_id)
            ->orderBy('name')
            ->get();

        return view('student-form', ['title' => 'Edit "' . $user->name . '"', 'user' => $user, 'grades' => $grades]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->school_id != Auth::user()->school_id || $user->role != 'USER') {
            return abort(403);
        }
        $validateData = $request->validate([
            'username' => 'required|max:100|unique:users,username,' . $user->id,
            'name' => 'required|max:100',
            'grade_id' => 'required|exists:grades,id',
            'password' => 'nullable|min:6',
            're-password' => 'same:password',
            'gender' => 'required|in:MALE,FEMALE',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.max' => 'Username maksimal 100 karakter.',
            'username.unique' => 'Username telah terpakai.',
            'name.required' => 'Nama tidak boleh kosong.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'grade_id.required' => 'Sekolah tidak boleh kosong.',
            'grade_id.exists' => 'Sekolah tidak ditemukan.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.min' => 'Password minimal 6 karakter.',
            're-password.required' => 'Konfirmasi password tidak boleh kosong.',
            're-password.same' => 'Konfirmasi password tidak sama.',
            'gender.required' => 'Jenis Kelamin tidak boleh kosong.',
            'gender.in' => 'Jenis Kelamin tidak valid.',
            'image.image' => 'Foto tidak valid.',
            'image.mimes' => 'Foto tidak valid.',
            'image.max' => 'Foto maksimal 2 MB.'
        ]);

        $validateData['username'] = preg_replace('/\s*/', '', $validateData['username']);
        $validateData['username'] = strtolower($validateData['username']);

        if ($validateData['password'] != "") {
            $validateData['password'] = Hash::make($validateData['password']);
        } else {
            unset($validateData['password']);
        }

        if ($request->image != null) {
            $imageName = uniqid() . time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validateData['image'] = $imageName;
        }

        $user->update($validateData);

        return redirect('/grade/' . $validateData['grade_id'] . '/student')
            ->with('success', 'Siswa berhasil disimpan.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->school_id != Auth::user()->school_id || $user->role != 'USER') {
            return abort(403);
        }
        $user->delete();

        return redirect('/grade/' . $user->grade_id . '/student')
            ->with('success', 'Siswa berhasil dihapus.');
    }
}
