<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Wrong email or password.');
        }

        if (!Hash::isHashed($user->password)) {
            return back()->with('error', 'Old account password must be reset.');
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Wrong email or password.');
        }

        session([
            'user_id' => $user->userId,
            'username' => $user->username,
            'full_name' => $user->full_name,
        ]);

        return redirect('/dashboard');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|min:2',
            'username' => 'required|min:3|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'full_name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Account created successfully.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Password reset successfully.');
    }

    public function logout()
    {
        session()->flush();

        return redirect('/login');
    }
}