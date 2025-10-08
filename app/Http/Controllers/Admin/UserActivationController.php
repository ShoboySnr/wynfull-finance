<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\YourAccountActivatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class UserActivationController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        // Only admins
        abort_unless($request->user()?->hasRole('admin'), 403);

        if ($user->is_active) {
            return back()->with('status', 'User is already active.');
        }

        $user->forceFill([
            'is_active'       => true,
            'activated_at'    => now(),
            'activated_by_id' => $request->user()->id,
        ])->save();

        activity()
            ->performedOn($user)->causedBy($request->user())
            ->withProperties(['activated_user_id' => $user->id])
            ->log('user_activated');

        $token = Password::createToken($user);
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

         $user->notify(new YourAccountActivatedNotification($resetUrl));

        activity()->causedBy($request->user())
            ->performedOn($user)
            ->withProperties(['notification' => 'YourAccountActivatedNotification'])
            ->log('notification_dispatched');

        return back()->with('status', 'User activated.');
    }
}
