<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Report;

class DashboardController extends Controller
{
    public function index()
    {
        $users = User::all();
        $reports = Report::all();

        return view('dashboard', [
            'users' => $users,
            'reports' => $reports
        ]);
    }
}