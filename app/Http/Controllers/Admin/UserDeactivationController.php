<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDeactivationController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        // Only admins
        abort_unless($request->user()?->hasRole('admin'), 403);

        if (! $user->is_active) {
            return back()->with('status', 'User is already inactive.');
        }

        DB::transaction(function () use ($request, $user) {
            // 1) Flip the flag
            $user->forceFill([
                'is_active'       => false,
            ])->save();

            // 2) Kill all sessions for this user (immediate lockout)
            DB::table('sessions')->where('user_id', $user->id)->delete();

            // 3) Activity log
            activity()
                ->performedOn($user)->causedBy($request->user())
                ->withProperties(['deactivated_user_id' => $user->id])
                ->log('user_deactivated');

            try {
//                $user->notify(new YourAccountDeactivatedNotification());
                activity()->performedOn($user)->causedBy($request->user())
                    ->withProperties(['notification' => 'YourAccountDeactivatedNotification'])
                    ->log('notification_dispatched');
            } catch (\Throwable $e) {
                report($e);
            }
        });

        return back()->with('success', 'User deactivated and sessions terminated.');
    }
}
