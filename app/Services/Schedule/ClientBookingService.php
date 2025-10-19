<?php

namespace App\Services\Schedule;

use App\Models\CoachAvailabilitySetting;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

class ClientBookingService
{
    public function __construct(private readonly CoachScheduleService $coachSchedule) {}

    /**
     * Book a session using a local datetime from the client's UI.
     * $payload = [
     *   'coach_id'      => int,
     *   'client_id'     => int,
     *   'date'          => 'YYYY-MM-DD',            // in coach's local timezone (UI should display coach tz)
     *   'start_time'    => 'HH:MM',                 // local to coach tz
     *   'title'         => 'optional',
     *   'type'          => 'optional',
     *   'notes'         => 'optional',
     *   'location_url'  => 'optional'               // e.g. Zoom/Meet; nullable for now
     * ]
     */
    public function book(array $payload, int $actorId)
    {
        $coach  = User::findOrFail((int) $payload['coach_id']);
        $client = User::findOrFail((int) $payload['client_id']);

        // Get coach availability
        /** @var CoachAvailabilitySetting|null $avail */
        $avail = CoachAvailabilitySetting::where('user_id', $coach->id)->first();
        if (!$avail) {
            throw ValidationException::withMessages(['coach_id' => ['Coach has no availability set.']]);
        }

        $tz   = $avail->timezone;
        $dur  = (int) $avail->session_duration_minutes;

        // Build local start/end in coach tz
        $date = (string) $payload['date'];
        $time = (string) $payload['start_time']; // "HH:MM"
        $startLocal = CarbonImmutable::parse("{$date} {$time}", $tz);
        $endLocal   = $startLocal->addMinutes($dur);

        // Validate within working window (end is exclusive)
        $workStart = CarbonImmutable::parse($startLocal->toDateString().' '.$avail->work_start_local->format('H:i'), $tz);
        $workEnd   = CarbonImmutable::parse($startLocal->toDateString().' '.$avail->work_end_local->format('H:i'),   $tz);

        if ($startLocal->lt($workStart) || $endLocal->gt($workEnd)) {
            throw ValidationException::withMessages([
                'start_time' => ['Selected time is outside coach working hours.'],
            ]);
        }

        // Validate alignment to session duration (e.g., 09:00, 09:30, etc.)
        $minutesFromOpen = $workStart->diffInMinutes($startLocal);
        if ($minutesFromOpen % $dur !== 0) {
            throw ValidationException::withMessages([
                'start_time' => ["Start time must align with {$dur}-minute slots."],
            ]);
        }

        // Optional: lead/cancel windows (e.g., cannot book within next 2 hours)
        $minLeadMinutes = 120;
        if ($startLocal->lte(now($tz)->addMinutes($minLeadMinutes))) {
            throw ValidationException::withMessages([
                'start_time' => ["Please book at least {$minLeadMinutes} minutes in advance."],
            ]);
        }

        // Delegate to your existing service (it will verify assignment & overlaps)
        return $this->coachSchedule->create([
            'coach_id'     => $coach->id,
            'client_id'    => $client->id,
            'title'        => $payload['title']        ?? 'Coaching Session',
            'type'         => $payload['type']         ?? '1-on-1',
            'location_url' => $payload['location_url'] ?? null,
            'notes'        => $payload['notes']        ?? null,
            'starts_at'    => $startLocal, // CoachScheduleService::create will ->utc()
            'ends_at'      => $endLocal,   // will ->utc()
        ], $actorId);
    }

    /**
     * Generate selectable start times (labels) for a date in coach tz.
     * Useful for the booking form dropdown.
     */
    public function generateSlots(User $coach, string $localDate): array
    {
        $avail = CoachAvailabilitySetting::where('coach_id', $coach->id)->first();
        if (!$avail) return [];

        $tz  = $avail->timezone;
        $dur = (int) $avail->session_duration_minutes;

        $start = CarbonImmutable::parse("{$localDate} {$avail->work_start_local->format('H:i')}", $tz);
        $end   = CarbonImmutable::parse("{$localDate} {$avail->work_end_local->format('H:i')}",   $tz);

        $slots = [];
        for ($t = $start; $t->addMinutes($dur)->lte($end); $t = $t->addMinutes($dur)) {
            $slots[] = [
                'time'       => $t->format('H:i'),
                'label'      => $t->format('g:i A'),
                'start_utc'  => $t->utc()->toIso8601String(),
                'end_utc'    => $t->addMinutes($dur)->utc()->toIso8601String(),
            ];
        }
        return $slots;
    }
}
