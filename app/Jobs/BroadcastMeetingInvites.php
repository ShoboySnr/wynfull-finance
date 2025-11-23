<?php

namespace App\Jobs;

use App\Models\Meeting;
use App\Models\User;
use App\Services\Notifications\NotifyUserOfMeeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastMeetingInvites implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200;
    public int $tries = 3;

    public function __construct(public int $meetingId) {}

    public function handle(NotifyUserOfMeeting $notifier): void
    {
        $meeting = Meeting::with('organizer')->find($this->meetingId);
        if (! $meeting) return;

        // Load recipients through pivot in chunks
        User::query()
            ->whereHas('meetingAttendees', fn (Builder $q) =>
            $q->where('meeting_id', $meeting->id)
            )
            ->select('id', 'name', 'email')
            ->chunkById(500, function ($users) use ($notifier, $meeting) {
                foreach ($users as $user) {
                    $notifier->send($user, $meeting);
                }
            });

        activity()->useLog('admin')
            ->causedBy($meeting->organizer_id)
            ->performedOn($meeting)
            ->event('admin meeting invites sent')
            ->log('Meeting invites sent to attendees');
    }
}
