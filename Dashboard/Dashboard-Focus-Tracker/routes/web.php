<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/password-reset', [AuthController::class, 'resetPassword']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/notifications', [DashboardController::class, 'notifications']);
Route::get('/settings', [DashboardController::class, 'settings']);

Route::get('/latest-notifications', [DashboardController::class, 'latestNotifications']);

Route::post('/session/start', [DashboardController::class, 'startSession']);
Route::post('/session/pause', [DashboardController::class, 'pauseSession']);
Route::post('/session/resume', [DashboardController::class, 'resumeSession']);
Route::post('/session/end', [DashboardController::class, 'endSession']);

Route::post('/session/focus-achieved', [DashboardController::class, 'focusAchieved']);