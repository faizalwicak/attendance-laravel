<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index(Request $request) {
        $validator = Validator::make($request->all(),[
            'username' => 'required|exists:users,username',
            'password' => 'required',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.exists' => 'Username tidak ditemukan.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Userrname atau password salah',
            ], 400);
        }

        $credentials = $request->only('username', 'password');
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Username atau password salah',
            ], 400);
        }

        if (Auth::guard('api')->user()->role != "USER") {
            return response()->json([
                'message' => 'Username atau password salah',
            ], 400);
        }
        
        return response()->json([
            "access_token" => $token
        ], 200);
    }

    public function profile() {
        return response()->json(auth()->user());
    }

}
