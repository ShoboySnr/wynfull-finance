<?php

use App\Http\Controllers\Admin\UserActivationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientOnboardingController;
use App\Http\Controllers\Dashboards\AdminDashboardController;
use App\Http\Controllers\Dashboards\ClientDashboardController;
use App\Http\Controllers\Dashboards\CoachDashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');
Route::view('/login', 'auth.login')->name('auth.login');

Route::middleware('guest')->group(function () {
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::post('/register/client', [RegisterController::class,'registerClient'])->name('register.client');
Route::post('/register/coach',  [RegisterController::class,'registerCoach'])->name('register.coach');

Route::middleware(['auth', 'role:client'])
    ->get('/dashboard/client', [ClientDashboardController::class, 'index'])
    ->name('dashboard.client');

Route::middleware(['auth', 'role:coach'])
    ->get('/dashboard/coach', [CoachDashboardController::class, 'index'])
    ->name('dashboard.coach');

Route::middleware(['auth', 'role:admin'])
    ->get('/dashboard/admin', [AdminDashboardController::class, 'index'])
    ->name('dashboard.admin');

Route::post('/admin/users/{user}/activate', UserActivationController::class)
    ->name('admin.users.activate')
    ->middleware(['auth', 'role:admin']);

Route::middleware(['auth'])->group(function () {
    Route::post('/onboarding/complete', [ClientOnboardingController::class, 'store'])
        ->name('onboarding.complete')
        ->middleware('role:client');
});
