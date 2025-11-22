<?php

namespace App\Services\Schedule;

use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminMeetingService
{
    public function __construct(
        private readonly ClientBookingService $slots
    ) {}

    public function schedule(User $admin, User $attendee, Carbon $start, int $durationMins, array $meta = []): Meeting
    {
        $end = (clone $start)->addMinutes($durationMins);

        // 1) block conflicts for admin
        $adminConflict = Meeting::query()
            ->where('organizer_id', $admin->id)
            ->where('status', 'scheduled')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('starts_at', [$start, $end])
                    ->orWhereBetween('ends_at', [$start, $end])
                    ->orWhere(fn($w) => $w->where('starts_at', '<=', $start)->where('ends_at', '>=', $end));
            })->exists();

        if ($adminConflict) {
            throw ValidationException::withMessages([
                'start_time' => 'Admin already has a meeting in that time range.',
            ]);
        }

        // 2) block conflicts for attendee (coach or client)
        $attendeeConflict = Meeting::query()
            ->where('attendee_id', $attendee->id)
            ->where('status', 'scheduled')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('starts_at', [$start, $end])
                    ->orWhereBetween('ends_at', [$start, $end])
                    ->orWhere(fn($w) => $w->where('starts_at', '<=', $start)->where('ends_at', '>=', $end));
            })->exists();

        if ($attendeeConflict) {
            throw ValidationException::withMessages([
                'start_time' => 'This user already has a meeting in that time range.',
            ]);
        }

        // 3) if attendee is a coach, optionally enforce coach availability
        if ($attendee->hasRole('coach')) {
            $tz = optional($attendee->availabilitySetting)->timezone ?? config('app.timezone');
            $date = $start->clone()->setTimezone($tz)->toDateString();

            $availableSlots = $this->slots->generateSlots($attendee, $date);
            $startLocal = $start->clone()->setTimezone($tz)->format('H:i');

            $isValidSlot = collect($availableSlots)->contains(fn($s) => $s['start'] === $startLocal && $s['is_available']);
            if (! $isValidSlot) {
                throw ValidationException::withMessages([
                    'start_time' => 'Selected time is outside coach availability.',
                ]);
            }
        }

        return Meeting::create([
            'organizer_id' => $admin->id,
            'attendee_id'  => $attendee->id,
            'starts_at'    => $start->utc(),
            'ends_at'      => $end->utc(),
            'status'       => 'scheduled',
            'mode'         => $meta['mode'] ?? null,
            'meeting_link' => $meta['meeting_link'] ?? null,
            'notes'        => $meta['notes'] ?? null,
            'scheduled_by' => $admin->id,
        ]);
    }


    /**
     * @param array $audience ['type' => 'single'|'all_clients'|'all_coaches'|'all', 'user_id'? => int]
     * @throws \Throwable
     */
    public function scheduleBroadcast(
        User $admin,
        Carbon $start,
        int $durationMins,
        array $audience,
        array $meta = []
    ): Meeting {
        $end = (clone $start)->addMinutes($durationMins);

        // Admin conflict check (same as before)
        $adminConflict = Meeting::query()
            ->where('organizer_id', $admin->id)
            ->where('status', 'scheduled')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('starts_at', [$start, $end])
                    ->orWhereBetween('ends_at', [$start, $end])
                    ->orWhere(fn($w) => $w->where('starts_at', '<=', $start)->where('ends_at', '>=', $end));
            })->exists();

        if ($adminConflict) {
            throw ValidationException::withMessages([
                'start_time' => 'Admin already has a meeting in that time range.',
            ]);
        }

        return DB::transaction(function () use ($admin, $start, $end, $audience, $meta) {

            $meeting = Meeting::create([
                'organizer_id' => $admin->id,
                'starts_at'    => $start->utc(),
                'ends_at'      => $end->utc(),
                'status'       => 'scheduled',
                'mode'         => $meta['mode'] ?? null,
                'meeting_link' => $meta['meeting_link'] ?? null,
                'notes'        => $meta['notes'] ?? null,
                'scheduled_by' => $admin->id,
            ]);

            $type = $audience['type'];

            if ($type === 'single') {
                $userId = (int) ($audience['user_id'] ?? 0);
                $attendee = User::findOrFail($userId);

                $this->assertUserFree($attendee, $start, $end);

                $meeting->attendees()->attach($attendee->id, [
                    'status' => 'invited',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return $meeting;
            }

            // Bulk audiences
            $query = User::query()->select('id');

            if ($type === 'all_clients') {
                $query->whereHas('roles', fn($q) => $q->where('name', 'client'));
            } elseif ($type === 'all_coaches') {
                $query->whereHas('roles', fn($q) => $q->where('name', 'coach'));
            } elseif ($type === 'all') {
                $query->whereHas('roles', fn($q) => $q->whereIn('name', ['client', 'coach']));
            } else {
                throw ValidationException::withMessages([
                    'audience' => 'Invalid audience type.',
                ]);
            }

            $query->chunkById(500, function ($users) use ($meeting, $start, $end) {
                $rows = [];

                foreach ($users as $u) {
                    // skip users already busy in that time
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
                }

                if ($rows) {
                    DB::table('meeting_attendees')->insert($rows);
                }
            });

            return $meeting;
        });
    }

    private function assertUserFree(User $user, Carbon $start, Carbon $end): void
    {
        if ($this->userHasConflict($user->id, $start, $end)) {
            throw ValidationException::withMessages([
                'audience' => "{$user->name} already has a meeting in that time range.",
            ]);
        }
    }

    private function userHasConflict(int $userId, Carbon $start, Carbon $end): bool
    {
        return DB::table('meeting_attendees')
            ->join('meetings', 'meetings.id', '=', 'meeting_attendees.meeting_id')
            ->where('meeting_attendees.user_id', $userId)
            ->where('meetings.status', 'scheduled')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('meetings.starts_at', [$start, $end])
                    ->orWhereBetween('meetings.ends_at', [$start, $end])
                    ->orWhere(fn($w) => $w->where('meetings.starts_at', '<=', $start)->where('meetings.ends_at', '>=', $end));
            })
            ->exists();
    }
}
