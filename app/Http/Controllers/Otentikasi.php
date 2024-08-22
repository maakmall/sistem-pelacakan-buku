<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Otentikasi extends Controller
{
    public function formLogin()
    {
        return view('otentikasi.index', ['title' => 'Login']);
    }

    public function login(Request $request)
    {
        $isValid = Auth::attempt([
            'username' => $request->username,
            'password' => $request->password
        ]);

        if ($isValid) {
            $request->session()->regenerate();

            return response(status: 200);
        } else {
            return response([
                'errors' => ['password' => 'Username or password invalid']
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
