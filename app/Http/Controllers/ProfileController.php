<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

    public function updateProfilePage(Request $request)
    {
        $user = User::findOrFail($request->user()->id);
        return view('profile-form', ['title' => 'Edit Profile', 'user' => $user]);
    }

    public function updateProfileAction(Request $request)
    {
        $user = User::findOrFail($request->user()->id);
        $validateData = $request->validate([
            'username' => 'required|max:100|unique:users,username,' . $user->id,
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.max' => 'Username maksimal 100 karakter.',
            'username.unique' => 'Username telah terpakai.',
            'name.required' => 'Nama tidak boleh kosong.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email telah terpakai.',
            'image.image' => 'Foto tidak valid.',
            'image.mimes' => 'Foto tidak valid.',
            'image.max' => 'Foto maksimal 2 MB.'
        ]);

        $validateData['username'] = preg_replace('/\s*/', '', $validateData['username']);
        $validateData['username'] = strtolower($validateData['username']);

        if ($request->image != null) {
            $imageName = uniqid() . time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $validateData['image'] = $imageName;
        }

        $user->update($validateData);

        return redirect('/me/profile')
            ->with('success', 'Profile berhasil disimpan.');
    }

    public function changePasswordPage(Request $request)
    {
        $user = User::findOrFail($request->user()->id);
        return view('password-form', ['title' => 'Ganti Password', 'user' => $user]);
    }

    public function changePasswordAction(Request $request)
    {
        $user = User::findOrFail($request->user()->id);

        $validateData = $request->validate([
            'old-password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('Password lama salah.');
                }
            },],
            'password' => 'required|min:5',
            're-password' => 'required|same:password',
        ], [
            'old-password.required' => 'Password lama tidak boleh kosong.',
            'password.required' => 'Password baru tidak boleh kosong.',
            'password.min' => 'Password baru minimal 6 karakter.',
            're-password.required' => 'Konfirmasi password tidak boleh kosong.',
            're-password.same' => 'Konfirmasi password tidak sama.',
        ]);

        $validateData['password'] = Hash::make($validateData['password']);

        $user->update($validateData);

        return redirect('/me/password')
            ->with('success', 'Password berhasil disimpan.');
    }
}
