<?php

namespace App\Jobs;

use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AttachBroadcastMeetingAttendees implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1200; // 20 mins
    public int $tries   = 3;
    public function __construct(
        public int $meetingId,
        public string $audienceType, // all_clients | all_coaches | al
    )
    {
    }

    public function handle(): void
    {
        $meeting = Meeting::find($this->meetingId);
        if (! $meeting) return;

        $start = Carbon::parse($meeting->starts_at);
        $end   = Carbon::parse($meeting->ends_at);

        // Build users query for audience
        $users = User::query()->select('id');

        if ($this->audienceType === 'all_clients') {
            $users->whereHas('roles', fn($q) => $q->where('name', 'client'));
        } elseif ($this->audienceType === 'all_coaches') {
            $users->whereHas('roles', fn($q) => $q->where('name', 'coach'));
        } else { // 'all'
            $users->whereHas('roles', fn($q) => $q->whereIn('name', ['client','coach']));
        }

        $users->chunkById(1000, function ($chunk) use ($meeting, $start, $end) {
            $rows = [];

            foreach ($chunk as $u) {
                // skip if this user already has a scheduled meeting overlapping
                if ($this->userHasConflict($u->id, $start, $end)) {
                    continue;
                }

                $rows[] = [
                    'meeting_id' => $meeting->id,
                    'user_id'    => $u->id,
                    'status'     => 'invited',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                BroadcastMeetingInvites::dispatch($meeting->id);
            }

            if (! empty($rows)) {
                DB::table('meeting_attendees')->insertOrIgnore($rows);
            }
        });

        activity()->useLog('admin')
            ->causedBy($meeting->organizer_id)
            ->performedOn($meeting)
            ->event('admin.meeting.broadcast_attached')
            ->withProperties(['audience' => $this->audienceType])
            ->log('Broadcast attendees attached to meeting');
    }

    private function userHasConflict(int $userId, Carbon $start, Carbon $end): bool
    {
        return DB::table('meeting_attendees')
            ->join('meetings', 'meetings.id', '=', 'meeting_attendees.meeting_id')
            ->where('meeting_attendees.user_id', $userId)
            ->where('meetings.status', 'scheduled')
            ->where(function (Builder $q) use ($start, $end) {
                $q->whereBetween('meetings.starts_at', [$start, $end])
                    ->orWhereBetween('meetings.ends_at', [$start, $end])
                    ->orWhere(fn($w) => $w->where('meetings.starts_at', '<=', $start)
                        ->where('meetings.ends_at', '>=', $end));
            })
            ->exists();
    }
}
