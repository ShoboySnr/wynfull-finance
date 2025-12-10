<?php

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminResourceCollectionController;
use App\Http\Controllers\Admin\AdminResourceCollectionModulesController;
use App\Http\Controllers\Admin\AdminResourceModuleOrderController;
use App\Http\Controllers\Admin\AdminResourcesController;
use App\Http\Controllers\Admin\AdminScheduleController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\CoachClientAssignmentController;
use App\Http\Controllers\Admin\MeetingsController;
use App\Http\Controllers\Admin\ResourceCollectionApprovalController;
use App\Http\Controllers\Admin\UserActivationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserDeactivationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Client\BookingController;
use App\Http\Controllers\Client\ModuleCompletionController;
use App\Http\Controllers\Client\ResourceLibraryController;
use App\Http\Controllers\ClientOnboardingController;
use App\Http\Controllers\Coach\CoachAvailabilityController;
use App\Http\Controllers\Coach\CoachClientsController;
use App\Http\Controllers\Coach\CoachMessagesController;
use App\Http\Controllers\Coach\CoachProfileController;
use App\Http\Controllers\Coach\CoachResourcesController;
use App\Http\Controllers\Coach\CoachScheduleController;
use App\Http\Controllers\Coach\ResourceModuleOrderController;
use App\Http\Controllers\Coach\ScheduleController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\Dashboards\AdminDashboardController;
use App\Http\Controllers\Dashboards\ClientDashboardController;
use App\Http\Controllers\Dashboards\CoachDashboardController;
use App\Http\Controllers\MeetingsCalendarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceCollectionController;
use App\Http\Controllers\ResourceModuleApprovalController;
use App\Http\Controllers\ResourceModuleController;
use App\Http\Controllers\Settings\NotificationPreferencesController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');
Route::get('/login', [LoginController::class, 'create'])->name('auth.login');

Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');

Route::post('/login', [LoginController::class, 'store'])
    ->middleware('throttle:login')
    ->name('login');


//Route::middleware('guest')->group(function () {
//    Route::post('/login', [LoginController::class, 'store'])
//        ->middleware('throttle:login')
//        ->name('login');
//
//});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::post('/register/client', [RegisterController::class,'registerClient'])->name('register.client');
Route::post('/register/coach',  [RegisterController::class,'registerCoach'])->name('register.coach');

Route::middleware(['auth'])->group(function () {
    Route::get('/me/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::match(['put', 'patch'], '/me/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/me/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

    Route::get('/chat/{assignment}', [ChatController::class,'index']);
    Route::post('/chat/{assignment}', [ChatController::class,'store']);
    Route::post('/chat/{assignment}/read', [ChatController::class,'markRead']);

    // Notification routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/notifications/count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.count');

    // Debug route to check user assignments
    Route::get('/debug/assignments', function() {
        $user = auth()->user();
        $assignments = \App\Models\CoachClientAssignment::where(function($query) use ($user) {
            $query->where('coach_id', $user->id)->orWhere('client_id', $user->id);
        })->with(['coach', 'client'])->get();

        return response()->json([
            'user_id' => $user->id,
            'user_roles' => $user->getRoleNames(),
            'assignments' => $assignments->map(function($assignment) {
                return [
                    'id' => $assignment->id,
                    'coach_id' => $assignment->coach_id,
                    'client_id' => $assignment->client_id,
                    'coach_name' => $assignment->coach->name ?? 'N/A',
                    'client_name' => $assignment->client->name ?? 'N/A',
                    'status' => $assignment->status
                ];
            })
        ]);
    });
});

Route::middleware(['auth', 'role:coach'])->prefix('coach')->name('coach.')->group(function () {
    Route::get('/dashboard', [CoachDashboardController::class, 'index'])->name('dashboard');
    Route::get('/clients', [CoachClientsController::class, 'index'])->name('clients');
    Route::get('/clients/{user}', [CoachClientsController::class, 'show'])->name('client.show');
    Route::get('/messages', [CoachMessagesController::class, 'index'])->name('messages');

    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
    Route::post('/schedule', [ScheduleController::class, 'store'])->name('schedule.store');
    Route::get('/schedule/feed', [ScheduleController::class, 'feed'])->name('schedule.feed');
    Route::post('/schedule/{session}/cancel', [ScheduleController::class, 'cancel'])->name('schedule.cancel');

    Route::get('/resources', [CoachResourcesController::class, 'index'])->name('resources');
    Route::post('/resources', [CoachResourcesController::class, 'store'])->name('resources.store');
    Route::put('/resources/{resource}', [CoachResourcesController::class, 'update'])->name('resources.update');

    Route::post('/resources/collection', [ResourceCollectionController::class, 'store'])->name('resources.collection.store');
    Route::put('/resources/{resourceCollection}/collection', [ResourceCollectionController::class, 'update'])->name('resources.collection.update');
    Route::get('/resources/{resourceCollection}/collection', [ResourceCollectionController::class, 'edit'])->name('resources.collection.edit');
    Route::post('/resources/{resourceCollection}/reorder', [ResourceModuleOrderController::class, 'reorder'])->name('resources.modules.reorder');
    Route::post('/resources/{resourceCollection}/modules', [ResourceModuleController::class, 'store'])->name('resources.modules.store');
    Route::put('/resources/{resourceCollection}/modules/{resourceModule}', [ResourceModuleController::class, 'update'])->name('resources.modules.update');
    Route::delete('/resources/{resourceCollection}/modules/{resourceModule}', [ResourceModuleController::class, 'destroy'])->name('resources.modules.destroy');


    Route::get('/profile', [CoachProfileController::class, 'index'])->name('profile');


    Route::post('/availability', [CoachAvailabilityController::class, 'update'])
        ->name('availability.update');
});

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/dashboard/client', [ClientDashboardController::class, 'index'])
        ->name('dashboard.client');

    Route::get('/resources/library', [ResourceLibraryController::class, 'index'])->name('resources.library');
    Route::get('/resources/learn/{resourceCollection}/{resourceModule?}', [ResourceLibraryController::class, 'learn'])->name('resources.learn');
    Route::get('/ai/client', [\App\Http\Controllers\Client\AI::class, 'index'])->name('ai.client');
    Route::get('/coaching/client', [\App\Http\Controllers\Client\CoachingController::class, 'index'])->name('coaching.client');
    Route::get('/messages/client', [\App\Http\Controllers\Client\MessagingController::class, 'index'])->name('messages.client');
    Route::get('/plans/client', [\App\Http\Controllers\Client\PlansController::class, 'index'])->name('plans.client');
    Route::get('/account/client', [\App\Http\Controllers\Client\AccountController::class, 'index'])->name('account.client');
    Route::put('/account/client/avatar', [\App\Http\Controllers\Client\AccountController::class, 'updateAvatar'])->name('account.client.avatar.update');
    Route::put('/account/client', [\App\Http\Controllers\Client\AccountController::class, 'updatePassword'])->name('account.client.update.password');

    Route::post('/onboarding/complete', [ClientOnboardingController::class, 'store'])
        ->name('onboarding.complete');

    Route::post('/coach/{coach}/book', [BookingController::class, 'store'])->name('client.booking.store');

    Route::get('/settings/notifications', [NotificationPreferencesController::class, 'edit'])
        ->name('settings.notifications.edit');
    Route::patch('/settings/notifications', [NotificationPreferencesController::class, 'update'])
        ->name('settings.notifications.update');


    Route::post('modules/{resourceModule}/complete', [ModuleCompletionController::class, 'store'])
        ->name('client.modules.complete');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('users', [UserController::class, 'index'])
        ->name('users');

    Route::post('users', [UserController::class, 'store'])
        ->name('users.store');

    Route::get('users/{user}', [UserController::class, 'show'])
        ->name('users.show');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.delete');
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password.update');

    Route::post('/users/{user}/activate', UserActivationController::class)->name('users.activate');
    Route::post('/users/{user}/deactivate', UserDeactivationController::class)->name('users.deactivate');

    Route::get('schedules', [AdminScheduleController::class, 'index'])->name('schedules');
    Route::get('resources', [AdminResourcesController::class, 'index'])->name('resources');
    Route::post('resources', [AdminResourceCollectionController::class, 'store'])->name('resources.collection.store');
    Route::put('resources/{resourceCollection}', [AdminResourceCollectionController::class, 'update'])->name('resources.collection.update');
    Route::patch('resources/{resourceCollection}/approve', [ResourceCollectionApprovalController::class, 'approve'])->name('resources.approve');
    Route::patch('resources/{resourceCollection}/reject', [ResourceCollectionApprovalController::class, 'reject'])->name('resources.reject');

    Route::post('resources/{resourceCollection}/reorder', [AdminResourceModuleOrderController::class, 'reorder'])->name('resources.collection.modules.reorder');
    Route::get('resources/{resourceCollection}/modules', [AdminResourceCollectionModulesController::class, 'index'])->name('resources.collection.modules');
    Route::post('resources/{resourceCollection}/modules', [AdminResourceCollectionModulesController::class, 'store'])->name('resources.collection.modules.store');
    Route::put('resources/{resourceCollection}/modules/{resourceModule}', [AdminResourceCollectionModulesController::class, 'update'])->name('resources.collection.modules.update');
    Route::delete('resources/{resourceCollection}/modules/{resourceModule}', [AdminResourceCollectionModulesController::class, 'destroy'])->name('resources.collection.modules.destroy');

    Route::post('/resource-modules/{module}/approve', [ResourceModuleApprovalController::class, 'approve'])->name('resource-modules.approve');
    Route::post('/resource-modules/{module}/reject', [ResourceModuleApprovalController::class, 'reject'])->name('resource-modules.reject');

    Route::get('profiles', [AdminProfileController::class, 'index'])->name('profiles');

    Route::get('/assignments', [CoachClientAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/create', [CoachClientAssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/assignments', [CoachClientAssignmentController::class, 'store'])->name('assignments.store');
    Route::post('/assignments/{assignmentId}/end', [CoachClientAssignmentController::class, 'destroy'])->name('assignments.end');
    Route::post('/assignments/{coach}/{client}/end', [CoachClientAssignmentController::class, 'unassignCoach'])->name('assignments.user.end');


    Route::get('/meetings', [MeetingsController::class, 'index'])->name('meetings.index');
    Route::post('/meetings', [MeetingsController::class, 'store'])->name('meetings.store');
    Route::get('/meetings/feed', [MeetingsCalendarController::class, 'feed'])->name('meetings.feed');
    Route::patch('/meetings/{meeting}/reschedule', [MeetingsController::class, 'reschedule'])->name('meetings.reschedule');
    Route::patch('/meetings/{meeting}/cancel', [MeetingsController::class, 'cancel'])->name('meetings.cancel');


});
