<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Report;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session('user_id')) {
            return redirect('/login');
        }

        $users = User::all();
        $reports = Report::where('userId', session('user_id'))
            ->latest()
            ->get();

        $latestReport = $reports->first();

        return view('dashboard', [
            'users' => $users,
            'reports' => $reports,
            'latestReport' => $latestReport
        ]);
    }
}