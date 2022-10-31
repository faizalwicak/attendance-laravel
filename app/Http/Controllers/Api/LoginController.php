<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Record;
use App\Models\School;
use Barryvdh\Debugbar\Facades\Debugbar;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

}
