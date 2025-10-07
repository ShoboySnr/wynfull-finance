<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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

        // $user->notify(new YourAccountActivatedNotification());

        return back()->with('status', 'User activated.');
    }
}
