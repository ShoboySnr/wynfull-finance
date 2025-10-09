<?php

use App\Http\Controllers\Admin\UserActivationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientOnboardingController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');
Route::view('/login', 'auth.login')->name('login');

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

Route::view('/dashboard/client', 'dashboards.client')->name('dashboard.client')->middleware(['auth', 'role:client']);
Route::view('/dashboard/coach',  'dashboards.coach')->name('dashboard.coach')->middleware(['auth', 'role:coach']);
Route::view('/dashboard/admin',  'dashboards.admin')->name('dashboard.admin')->middleware(['auth', 'role:admin']);

Route::post('/admin/users/{user}/activate', UserActivationController::class)
    ->name('admin.users.activate')
    ->middleware(['auth', 'role:admin']);

Route::middleware(['auth'])->group(function () {
    Route::post('/onboarding/complete', [ClientOnboardingController::class, 'store'])
        ->name('onboarding.complete')
        ->middleware('role:client');
});
