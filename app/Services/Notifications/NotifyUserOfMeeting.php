<?php

namespace App\Services\Notifications;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotifyUserOfMeeting
{
    public function send(User $user, Meeting $meeting): void
    {
        // ---- EMAIL ----
        if ($user->email) {
            Mail::send('emails.admin_meeting_invite', [
                'user'    => $user,
                'meeting' => $meeting,
            ], function ($msg) use ($user, $meeting) {
                $msg->to($user->email)
                    ->subject("Meeting Invite: {$meeting->starts_at->format('D, M j')}");
            });
        }

        // ---- CUSTOM IN-APP NOTIFICATION ----
        DB::table('notifications')->insert([
            'user_id' => $user->id,
            'type'    => 'meeting_invite',
            'title'   => 'Admin scheduled a meeting with you',
            'message' => $meeting->notes
                ? "Meeting scheduled: {$meeting->notes}"
                : "A new meeting has been scheduled by Admin.",
            'data'    => json_encode([
                'meeting_id'    => $meeting->id,
                'starts_at'     => $meeting->starts_at?->toIso8601String(),
                'ends_at'       => $meeting->ends_at?->toIso8601String(),
                'mode'          => $meeting->mode,
                'meeting_link'  => $meeting->meeting_link,
            ]),
            'read_at'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
