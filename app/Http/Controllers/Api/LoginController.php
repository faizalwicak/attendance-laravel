<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required',
            'device_id' => 'required'
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.exists' => 'Username tidak ditemukan.',
            'password.required' => 'Password tidak boleh kosong.',
            'device_id.required' => 'Device Id tidak boleh kosong.'
        ]);

        $user = User::where('username', $data['username'])
            ->where('role', 'USER')
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 422);
        }

        if ($user->device_id != null && $user->device_id != "" && $user->device_id != $data['device_id']) {
            return response()->json(['message' => 'User sudah login di perangkat lain.'], 422);
        }

        // $userDevice = User::where('device_id', $data['device_id'])->first();

        // if ($userDevice != null && $userDevice->id != $user->id) {
        //     return response()->json(['message' => 'Perangkat sudah digunakan oleh user lain.'], 422);
        // }

        if (!Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Password salah.'], 422);
        }

        $token = Auth::guard('api')->login($user);
        $user->update(['device_id' => $data['device_id']]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer'
        ]);
    }

    public function changePassword(Request $request)
    {
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
