<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Barryvdh\Debugbar\Facades\Debugbar;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role == 'SUPERADMIN') {
            $users = User::where('role', 'ADMIN')
                ->orderBy('name')
                ->get();
        } else {
            $users = User::where('school_id', $user->school_id)
                ->where(function ($q) {
                    $q->where('role', 'ADMIN')
                        ->orWhere('role', 'OPERATOR');
                })
                ->orderBy('name')
                ->get();
        }
        return view('admin-index', ['title' => 'Daftar Admin', 'users' => $users]);
    }

    public function create()
    {
        $schools = School::orderBy('name')->get();
        return view('admin-form', ['title' => 'Tambah Admin', 'user' => null, 'schools' => $schools]);
    }

    public function store(Request $request)
    {
        $validateRole = [
            'username' => 'required|max:100|unique:users,username',
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            're-password' => 'required|same:password',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
        $validateMessage = [
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
            'image.image' => 'Foto tidak valid.',
            'image.mimes' => 'Foto tidak valid.',
            'image.max' => 'Foto maksimal 2 MB.'
        ];

        $user = $request->user();
        if ($user->role == 'SUPERADMIN') {
            $validateRole['school_id'] = 'required|exists:schools,id';
            $validateMessage['school_id.required'] = 'Sekolah tidak boleh kosong.';
            $validateMessage['school_id.exists'] = 'Sekolah tidak ditemukan.';
        } else {
            $validateRole['role'] = 'required|in:ADMIN,OPERATOR';
            $validateMessage['role.required'] = 'Role tidak boleh kosong.';
            $validateMessage['role.in'] = 'Role tidak valid.';
        }

        $validateData = $request->validate($validateRole, $validateMessage);

        $validateData['username'] = preg_replace('/\s*/', '', $validateData['username']);
        $validateData['username'] = strtolower($validateData['username']);

        if ($user->role == 'SUPERADMIN') {
            $validateData['role'] = 'ADMIN';
        } else {
            $validateData['school_id'] = $user->school_id;
        }

        $validateData['password'] = Hash::make($validateData['password']);

        if ($request->image != null) {
            $imageName = uniqid() . time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validateData['image'] = $imageName;
        }

        User::create($validateData);

        return redirect('/admin')
            ->with('success', 'Admin berhasil dibuat.');
    }

    public function edit(Request $request, $id)
    {
        if ($request->user()->role == 'SUPERADMIN') {
            $user = User::findOrFail($id);
            if (!in_array($user->role, ['ADMIN'])) {
                return abort(403);
            }
        } else {
            $user = User::findOrFail($id);
            if (!in_array($user->role, ['ADMIN', 'OPERATOR']) || $user->school_id != $request->user()->school_id) {
                return abort(403);
            }
        }

        $schools = School::orderBy('name')->get();

        return view('admin-form', ['title' => 'Edit "' . $user->name . '"', 'user' => $user, 'schools' => $schools]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validateRole = [
            'username' => 'required|max:100|unique:users,username,' . $user->id,
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            're-password' => 'same:password',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
        $validateMessage = [
            'username.required' => 'Username tidak boleh kosong.',
            'username.max' => 'Username maksimal 100 karakter.',
            'username.unique' => 'Username telah terpakai.',
            'name.required' => 'Nama tidak boleh kosong.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email telah terpakai.',
            'password.min' => 'Password minimal 6 karakter.',
            're-password.same' => 'Konfirmasi password tidak sama.',
            'image.image' => 'Foto tidak valid.',
            'image.mimes' => 'Foto tidak valid.',
            'image.max' => 'Foto maksimal 2 MB.'
        ];

        if ($request->user()->role == 'SUPERADMIN') {
            if (!in_array($user->role, ['ADMIN'])) {
                return abort(403);
            }
            $validateRole['school_id'] = 'required|exists:schools,id';
            $validateMessage['school_id.required'] = 'Sekolah tidak boleh kosong.';
            $validateMessage['school_id.exists'] = 'Sekolah tidak ditemukan.';
        } else {
            if (!in_array($user->role, ['ADMIN', 'OPERATOR']) || $user->school_id != $request->user()->school_id) {
                return abort(403);
            }
            $validateRole['role'] = 'required|in:ADMIN,OPERATOR';
            $validateMessage['role.required'] = 'Role tidak boleh kosong.';
            $validateMessage['role.in'] = 'Role tidak valid.';
        }

        $validateData = $request->validate($validateRole, $validateMessage);

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

        return redirect('/admin')
            ->with('success', 'Admin berhasil disimpan.');
    }

    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->user()->role == 'SUPERADMIN') {
            if (!in_array($user->role, ['ADMIN'])) {
                return abort(403);
            }
        } else {
            if (!in_array($user->role, ['ADMIN', 'OPERATOR']) || $user->school_id != $request->user()->school_id) {
                return abort(403);
            }
        }
        $user->delete();

        return redirect('/admin')
            ->with('success', 'Admin berhasil dihapus.');
    }

    public function operatorAccess($id)
    {
        $user = User::where('id', $id)->where('school_id', auth()->user()->school_id)->with('grades')->first();
        if (!$user) return abort(404);
        $selectedGrades = [];
        foreach ($user->grades as $u) {
            array_push($selectedGrades, $u->id);
        }
        $school = School::where('id', auth()->user()->school_id)->with('grades')->first();
        return view('admin-form-access', ['title' => 'Akses Operator', 'user' => $user, 'grades' => $school->grades, 'selectedGrades' => $selectedGrades]);
    }

    public function operatorAccessAction(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->role != 'OPERATOR') return abort(403);

        $validateRole = [
            'access' => 'nullable',
            'access.*' => 'required|int|exists:grades,id',
        ];
        $validateMessage = [
            'access.array' => 'Kelas tidak valid.',
            'access.*.required' => 'Kelas tidak boleh kosong.',
            'access.*.int' => 'Kelas tidak valid.',
            'access.*.exists' => 'Kelas tidak ditemukan.',
        ];

        $validateData = $request->validate($validateRole, $validateMessage);

        $user->grades()->sync(isset($validateData['access']) ? $validateData['access'] : []);
        // return response()->json($validateData);

        return redirect('/admin')
            ->with('success', 'Akses berhasil disimpan.');
    }
}
