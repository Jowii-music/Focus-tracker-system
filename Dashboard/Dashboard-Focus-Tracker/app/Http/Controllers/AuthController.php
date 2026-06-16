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

        session([
            'user_id' => $user->id,
            'username' => $user->name,
        ]);

        return redirect('/dashboard');
    }

    public function register(Request $request)
    {
        DB::table('users')->insert([
            'name' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/login')
            ->with('success', 'Account created successfully.');
    }

    public function logout()
    {
        session()->flush();

        return redirect('/login');
    }
}