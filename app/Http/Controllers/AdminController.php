<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Barryvdh\Debugbar\Facades\Debugbar;

class AdminController extends Controller
{
    public function index() {
        $users = User::where('role', 'ADMIN')->orderBy('name')->get();
        return view('admin-index', ['title' => 'Daftar Admin', 'users' => $users]);
    }

    public function create() {
        $schools = School::orderBy('name')->get();
        return view('admin-form', ['title' => 'Tambah Admin', 'user' => null, 'schools' => $schools]);
    }

    public function store(Request $request) {
        $validateData = $request->validate([
            'username' => 'required|max:100|unique:users,username',
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'school_id' => 'required|exists:schools,id',
            'password' => 'required|min:6',
            're-password' => 'required|same:password',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.max' => 'Username maksimal 100 karakter.',
            'username.unique' => 'Username telah terpakai.',
            'name.required' => 'Nama tidak boleh kosong.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email telah terpakai.',
            'school_id.required' => 'Sekolah tidak boleh kosong.',
            'school_id.exists' => 'Sekolah tidak ditemukan.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.min' => 'Password minimal 6 karakter.',
            're-password.required' => 'Konfirmasi password tidak boleh kosong.',
            're-password.same' => 'Konfirmasi password tidak sama.',
        ]);

        $validateData['username'] = preg_replace('/\s*/', '', $validateData['username']);
        $validateData['username'] = strtolower($validateData['username']);

        $validateData['role'] = 'ADMIN';
        $validateData['password'] = Hash::make($validateData['password']);

        User::create($validateData);

        return redirect('/admin')
            ->with('success','Admin berhasil dibuat.');
    }

    public function edit($id) {
        $user = User::findOrFail($id);
        $schools = School::orderBy('name')->get();
        if ($user->role != 'ADMIN') {
            return abort(403);
        }
        return view('admin-form', ['title' => 'Edit "'.$user->name.'"', 'user' => $user, 'schools' => $schools]);
    }

    public function update(Request $request, $id) {
        $user = User::findOrFail($id);
        if ($user->role != 'ADMIN') {
            return abort(403);
        }
        $validateData = $request->validate([
            'username' => 'required|max:100|unique:users,username,'.$user->id,
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'school_id' => 'required|exists:schools,id',
            'password' => 'nullable|min:6',
            're-password' => 'same:password',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.max' => 'Username maksimal 100 karakter.',
            'username.unique' => 'Username telah terpakai.',
            'name.required' => 'Nama tidak boleh kosong.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'school_id.required' => 'Sekolah tidak boleh kosong.',
            'school_id.exists' => 'Sekolah tidak ditemukan.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email telah terpakai.',
            'password.min' => 'Password minimal 6 karakter.',
            're-password.same' => 'Konfirmasi password tidak sama.',
        ]);

        $validateData['username'] = preg_replace('/\s*/', '', $validateData['username']);
        $validateData['username'] = strtolower($validateData['username']);

        if ($validateData['password'] != "") {
            $validateData['password'] = Hash::make($validateData['password']);
        } else {
            unset($validateData['password']);
        }

        $user->update($validateData);

        return redirect('/admin')
            ->with('success','Admin berhasil disimpan.');
    }

    public function destroy($id)
    {   
        $user = User::findOrFail($id);
        if ($user->role != 'ADMIN') {
            return abort(403);
        }
        $user->delete();
       
        return redirect('/admin')
            ->with('success','Admin berhasil dihapus.');
    }
}
