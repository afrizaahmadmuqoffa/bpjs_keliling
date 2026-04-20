<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nik' => 'required|digits:16',
            'password' => 'required|string',
        ]);


        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            $role = Auth::user()->role;

            if (in_array($role, ['super_admin', 'admin'])) {
                return redirect()->route('dashboard');
            }

            return redirect()->route('participants.index');
        }


        return back()->withErrors([
            'nik' => 'NIK atau password salah.'
        ])->onlyInput('nik');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}