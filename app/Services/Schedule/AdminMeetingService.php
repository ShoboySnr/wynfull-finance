<?php

namespace App\Services\Schedule;

use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
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
            'notes'        => $meta['notes'] ?? null,
            'scheduled_by' => $admin->id,
        ]);
    }
}
