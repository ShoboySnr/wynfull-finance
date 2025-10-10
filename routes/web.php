<?php

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminResourcesController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\CoachClientAssignmentController;
use App\Http\Controllers\Admin\UserActivationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClientOnboardingController;
use App\Http\Controllers\Coach\CoachClientsController;
use App\Http\Controllers\Coach\CoachMessagesController;
use App\Http\Controllers\Coach\CoachProfileController;
use App\Http\Controllers\Coach\CoachResourcesController;
use App\Http\Controllers\Coach\CoachScheduleController;
use App\Http\Controllers\CoachController;
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


Route::middleware(['auth', 'role:coach'])->prefix('coach')->name('coach.')->group(function () {
    Route::get('/dashboard', [CoachDashboardController::class, 'index'])->name('dashboard');
    Route::get('/clients', [CoachClientsController::class, 'index'])->name('clients');
    Route::get('/messages', [CoachMessagesController::class, 'index'])->name('messages');
    Route::get('/schedule', [CoachScheduleController::class, 'index'])->name('schedule');
    Route::get('/resources', [CoachResourcesController::class, 'index'])->name('resources');
    Route::get('/profile', [CoachProfileController::class, 'index'])->name('profile');
});

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/dashboard/client', [ClientDashboardController::class, 'index'])
        ->name('dashboard.client');

    Route::post('/onboarding/complete', [ClientOnboardingController::class, 'store'])
        ->name('onboarding.complete');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('users', [UserController::class, 'index'])
        ->name('users');

    Route::get('users/{user}', [UserController::class, 'show'])
        ->name('users.show');

    Route::post('/users/{user}/activate', UserActivationController::class)
        ->name('users.activate');

    Route::get('resources', [AdminResourcesController::class, 'index'])->name('resources');

    Route::get('profiles', [AdminProfileController::class, 'index'])->name('profiles');

    Route::get('/assignments', [CoachClientAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/create', [CoachClientAssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/assignments', [CoachClientAssignmentController::class, 'store'])->name('assignments.store');
    Route::post('/assignments/{assignmentId}/end', [CoachClientAssignmentController::class, 'destroy'])->name('assignments.end');
});
