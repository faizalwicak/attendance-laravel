<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use hisorange\BrowserDetect\Parser as Browser;

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

        if (
            $user
            && Hash::check($validateData['password'], $user->password)
        ) {
            if (in_array($user->role, ['SUPERADMIN', 'ADMIN', 'OPERATOR'])) {
                Auth::login($user);
                if ($user->role == 'SUPERADMIN') {
                    return redirect('/school');
                }

                return redirect('/overview');
            }
            if ($user->role == 'USER') {
                // if (!Browser::isMobile()) {
                //     Session::flash('error', 'User hanya bisa login dari ponsel.');
                //     return redirect('login')->withInput();
                // }

                if ($user->device_id != null && $user->device_id != Browser::userAgent()) {
                    Session::flash('error', 'Akun sudah login di perangkat lain, silahkan hubungi admin.');
                    return redirect('login')->withInput();
                }

                Auth::login($user);
                $user->update(['device_id' => Browser::userAgent()]);
                return redirect()->route('mobile.home');
            }
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
