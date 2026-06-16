<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function showRegister()
    {
        return view('register');
    }

    public function login(Request $request)
    {
        $user = DB::table('users')
            ->where('email', $request->email)
            ->where('password', $request->password)
            ->first();

        if (!$user) {
            return back()->with('error', 'Wrong email or password.');
        }

        session(['user_id' => $user->userId]);
        session(['username' => $user->username]);

        return redirect('/dashboard');
    }

    public function register(Request $request)
    {
        DB::table('users')->insert([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'userId' => DB::table('users')->max('userId') + 1,
        ]);

        return redirect('/login')->with('success', 'Account created. You can now log in.');
    }

    public function logout()
    {
        session()->forget('user_id');
        session()->forget('username');

        return redirect('/login');
    }
}