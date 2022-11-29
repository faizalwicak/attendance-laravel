<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect('/');
        } else {
            return view('login');
        }
    }

    public function actionLogin(Request $request)
    {
        $validateData = $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'username.exists' => 'Username tidak ditemukan.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);

        $user = User::where('username', $validateData['username'])->first();

        if ($user && Hash::check($validateData['password'], $user->password) && in_array($user->role, ['SUPERADMIN', 'ADMIN', 'OPERATOR'])) {
            Auth::login($user);
            if ($user->role == 'SUPERADMIN') {
                return redirect('/school');
            }
            return redirect('/overview');
        }

        Session::flash('error', 'Username atau Password Salah.');
        return redirect('login')->withInput();
    }

    public function actionLogout()
    {
        Auth::logout();
        return redirect('login');
    }
}
