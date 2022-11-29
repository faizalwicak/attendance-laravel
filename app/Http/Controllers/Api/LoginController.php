<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request) {
        $data = $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required',
        ], [
            'username.require' => 'Username tidak boleh kosong.',
            'username.exists' => 'Username tidak ditemukan.',
            'password.require' => 'Password tidak boleh kosong.'
        ]);
        if (!$token = Auth::guard('api')->attempt($data)) {
            return response()->json(['message' => 'Password salah.'], 422);
        }
        if (Auth::guard('api')->user()->role != 'USER') {
            return response()->json(['message' => 'Username tidak ditemukan.'], 422);
        }
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer'
        ]);
    }

    public function changePassword(Request $request) {
        $user = User::findOrFail(Auth::user()->id);
        
        $validateData = $request->validate([
            'old-password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, Auth::user()->password)) {
                    $fail('Password lama salah.');
                }
            },],
            'password' => 'required|min:6',
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

        return response()->json([
            'message' => 'Berhasil mengubah password.',
        ]);
    }
}
