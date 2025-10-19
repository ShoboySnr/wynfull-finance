<?php

namespace App\Services\CoachAvailability;

use App\Models\CoachAvailabilitySetting;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class AvailabilitySettingsService
{
    public function upsertFor(User $user, array $data): CoachAvailabilitySetting
    {
        return DB::transaction(function () use ($user, $data) {
            return CoachAvailabilitySetting::query()
                ->updateOrCreate(
                    ['coach_id' => $user->id],
                    [
                        'work_start_local' => $data['work_start_local'],
                        'work_end_local'   => $data['work_end_local'],
                        'timezone'         => $data['timezone'],
                        'session_duration_minutes' => (int) $data['session_duration_minutes'],
                    ]
                );
        });
    }


    /**
     * Generate bookable start times for a given local date string (YYYY-MM-DD).
     * End time is exclusive. DST is respected via timezone.
     */
    public function generateSlotsForDate(User $coach, string $localDate): array
    {
        /** @var CoachAvailabilitySetting|null $s */
        $s = CoachAvailabilitySetting::query()->where('user_id', $coach->id)->first();
        if (!$s) return [];

        $tz = $s->timezone;

        // Build start/end on the requested date in the coach's timezone
        $start = CarbonImmutable::parse("{$localDate} {$s->work_start_local->format('H:i')}", $tz);
        $end   = CarbonImmutable::parse("{$localDate} {$s->work_end_local->format('H:i')}", $tz);

        $dur   = (int) $s->session_duration_minutes;

        $slots = [];
        for ($t = $start; $t->lt($end); $t = $t->addMinutes($dur)) {
            // Only include slots that fit entirely before end
            if ($t->addMinutes($dur)->lte($end)) {
                $slots[] = [
                    'start_local' => $t->format('Y-m-d H:i'),
                    'end_local'   => $t->addMinutes($dur)->format('Y-m-d H:i'),
                    'timezone'    => $tz,
                    'start_utc'   => $t->utc()->format('Y-m-d H:i'),
                    'end_utc'     => $t->addMinutes($dur)->utc()->format('Y-m-d H:i'),
                ];
            }
        }

        return $slots;
    }
}
