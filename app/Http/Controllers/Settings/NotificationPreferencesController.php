<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationPreferencesController extends Controller
{
    public function index()
    {

    }

    public function edit()
    {

    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'email_notifications_enabled' => ['required', 'boolean'],
            'goal_reminders_enabled' => ['required', 'boolean'],
        ]);

        $user = $request->user();
        $user->forceFill($data)->save();

        // Activity log
        activity()->useLog('profile')
            ->performedOn($user)
            ->causedBy($user)
            ->event('notification_prefs_updated')
            ->withProperties($data + [
                    'ip' => $request->ip(),
                    'ua' => mb_substr((string)$request->userAgent(), 0, 255),
                ])->log('Notification preferences updated');

        return back()->with('success', 'Notification preferences saved.');
    }
}
