<?php

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminResourcesController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\CoachClientAssignmentController;
use App\Http\Controllers\Admin\UserActivationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Client\BookingController;
use App\Http\Controllers\Client\ResourceLibraryController;
use App\Http\Controllers\ClientOnboardingController;
use App\Http\Controllers\Coach\CoachAvailabilityController;
use App\Http\Controllers\Coach\CoachClientsController;
use App\Http\Controllers\Coach\CoachMessagesController;
use App\Http\Controllers\Coach\CoachProfileController;
use App\Http\Controllers\Coach\CoachResourcesController;
use App\Http\Controllers\Coach\CoachScheduleController;
use App\Http\Controllers\Coach\ScheduleController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\Dashboards\AdminDashboardController;
use App\Http\Controllers\Dashboards\ClientDashboardController;
use App\Http\Controllers\Dashboards\CoachDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Settings\NotificationPreferencesController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/me/profile', [ProfileController::class, 'show']);
    Route::match(['put', 'patch'], '/me/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/me/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

});

Route::middleware(['auth', 'role:coach'])->prefix('coach')->name('coach.')->group(function () {
    Route::get('/dashboard', [CoachDashboardController::class, 'index'])->name('dashboard');
    Route::get('/clients', [CoachClientsController::class, 'index'])->name('clients');
    Route::get('/messages', [CoachMessagesController::class, 'index'])->name('messages');

    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
    Route::post('/schedule', [ScheduleController::class, 'store'])->name('schedule.store');
    Route::get('/schedule/feed', [ScheduleController::class, 'feed'])->name('schedule.feed');
    Route::post('/schedule/{session}/cancel', [ScheduleController::class, 'cancel'])->name('schedule.cancel');

    Route::get('/resources', [CoachResourcesController::class, 'index'])->name('resources');
    Route::post('/resources', [CoachResourcesController::class, 'store'])->name('resources.store');
    Route::put('/resources/{resource}', [CoachResourcesController::class, 'update'])->name('resources.update');
    Route::get('/profile', [CoachProfileController::class, 'index'])->name('profile');

    Route::post('/availability', [CoachAvailabilityController::class, 'update'])
        ->name('availability.update');
});

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/dashboard/client', [ClientDashboardController::class, 'index'])
        ->name('dashboard.client');

    Route::get('/resources/library', [ResourceLibraryController::class, 'index'])->name('resources.library');
    Route::get('/ai/client', [\App\Http\Controllers\Client\AI::class, 'index'])->name('ai.client');
    Route::get('/coaching/client', [\App\Http\Controllers\Client\CoachingController::class, 'index'])->name('coaching.client');
    Route::get('/messages/client', [\App\Http\Controllers\Client\MessagingController::class, 'index'])->name('messages.client');
    Route::get('/plans/client', [\App\Http\Controllers\Client\PlansController::class, 'index'])->name('plans.client');
    Route::get('/account/client', [\App\Http\Controllers\Client\AccountController::class, 'index'])->name('account.client');
    Route::put('/account/client', [\App\Http\Controllers\Client\AccountController::class, 'updatePassword'])->name('account.client.update.password');

    Route::post('/onboarding/complete', [ClientOnboardingController::class, 'store'])
        ->name('onboarding.complete');

    Route::post('/coach/{coach}/book', [BookingController::class, 'store'])->name('client.booking.store');

    Route::get('/settings/notifications', [NotificationPreferencesController::class, 'edit'])
        ->name('settings.notifications.edit');
    Route::patch('/settings/notifications', [NotificationPreferencesController::class, 'update'])
        ->name('settings.notifications.update');
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
