<?php

namespace App\Services\Dashboard;

use App\Models\CoachingSession;
use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class CoachDashboardService
{
    /**
     * Count active clients assigned to the coach.
     */
    public function activeClientsCount(int $coachId): int
    {
        return User::query()
            ->where('is_active', true)
            ->whereHas('coaches', fn($q) => $q->where('coach_id', $coachId))
            ->count();
    }

    /**
     * Return [startOfWeekUTC, endOfWeekUTC]
     */
    public function weekRange(?string $anchor = null): array
    {
        $base = $anchor ? Carbon::parse($anchor) : now();
        return [
            $base->copy()->startOfWeek()->startOfDay()->utc(),
            $base->copy()->endOfWeek()->endOfDay()->utc(),
        ];
    }

    public function weeklyAdminMeetings(int $coachId, ?string $anchor = null): Collection
    {
        [$from, $to] = $this->weekRange($anchor);

        return Meeting::query()
            ->with(['organizer:id,name,email', 'attendees:id,name,email'])
            ->whereBetween('starts_at', [$from, $to])
            ->where('status', 'scheduled')
            ->where(function ($q) use ($coachId) {
                // Broadcast meetings to coaches
                $q->whereIn('audience', ['all_coaches', 'all'])
                    // Or explicitly targeted to this coach
                    ->orWhereHas('attendees', fn($a) => $a->where('users.id', $coachId));
            })
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * Sessions for a coach within the current week.
     */
    public function weeklySessions(int $coachId, ?string $anchor = null): Collection
    {
        [$from, $to] = $this->weekRange($anchor);

        return CoachingSession::query()
            ->with(['client:id,name,email'])
            ->where('coach_id', $coachId)
            ->whereBetween('starts_at', [$from, $to])
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * Count of sessions scheduled this week (not cancelled).
     */
    public function weeklySessionsCount(int $coachId, ?string $anchor = null): int
    {
        [$from, $to] = $this->weekRange($anchor);

        // Coaching sessions (existing)
        $sessionCount = CoachingSession::query()
            ->where('coach_id', $coachId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('starts_at', [$from, $to])
            ->count();

        // Admin meetings that apply to this coach
        $adminMeetingCount = Meeting::query()
            ->whereBetween('starts_at', [$from, $to])
            ->where('status', 'scheduled')
            ->where(function ($q) use ($coachId) {
                $q->whereIn('audience', ['all_coaches', 'all'])
                    ->orWhereHas('attendees', fn($a) => $a->where('users.id', $coachId));
            })
            ->count();

        return $sessionCount + $adminMeetingCount;
    }


    /**
     * Format sessions to a handy array for the dashboard schedule.
     * Converts to the app (or provided) timezone for display.
     */
    public function formatSessionsForDashboard(
        Collection $sessions,
        ?string $tz = null,
        ?Collection $adminMeetings = null
    ): array {
        $tz = $tz ?: config('app.timezone', 'UTC');

        // Coaching sessions
        $sessionItems = collect($sessions)->map(function (CoachingSession $s) use ($tz) {
            $start = $s->starts_at->clone()->setTimezone($tz);
            $end   = $s->ends_at->clone()->setTimezone($tz);

            return [
                'id'        => $s->id,
                'date'      => $start->toDateString(),
                'time'      => $start->format('g:i A'),
                'client'    => $s->client?->name ?? '—',
                'client_id' => $s->client?->id,
                'title'     => $s->title,
                'type'      => $s->type ?? 'Session',
                'status'    => $s->status,
                'join_url'  => $s->location_url,
                'starts_at' => $start->format('ga'), // e.g. 10am
                'ends_at'   => $end->format('ga'),
                'notes'     => $s->notes ?? '',
                'source'    => 'coach_session',
            ];
        });

        // Admin meetings (for this coach)
        $adminItems = collect($adminMeetings ?: [])->map(function (Meeting $m) use ($tz) {
            $start = $m->starts_at->clone()->setTimezone($tz);
            $end   = $m->ends_at->clone()->setTimezone($tz);

            $audienceLabel = match ($m->audience ?? 'single') {
                'all_clients' => 'All Clients',
                'all_coaches' => 'All Coaches',
                'all'         => 'All Clients & Coaches',
                default       => null,
            };

            $clientLabel = $audienceLabel ? "Admin ({$audienceLabel})" : 'Admin';

            return [
                'id'        => $m->id,
                'date'      => $start->toDateString(),
                'time'      => $start->format('g:i A'),
                'client'    => $clientLabel,
                'client_id' => null,
                'title'     => $m->notes
                    ? "Admin Meeting • {$m->notes}"
                    : 'Admin Meeting',
                'type'      => 'Admin Meeting',
                'status'    => $m->status,
                'join_url'  => $m->meeting_link,
                'starts_at' => $start->format('ga'),
                'ends_at'   => $end->format('ga'),
                'notes'     => $m->notes ?? '',
                'source'    => 'admin_meeting',
            ];
        });

        // Merge and sort by date & time
        return $sessionItems
            ->merge($adminItems)
            ->sortBy(fn($row) => $row['date'].' '.$row['time'])
            ->values()
            ->all();
    }


    /**
     * Recent activities by the coach (Spatie activity log).
     * Only returns activities with valid User subjects.
     */
    public function recentActivitiesForCoach(int $coachId, int $limit = 10): Collection
    {
        return Activity::query()
            ->with([
                'subject.profile:id,user_id,first_name,last_name,avatar_path,professional_title',
                'causer.profile:id,user_id,first_name,last_name,avatar_path,professional_title'
            ])
            ->where('causer_id', $coachId)
            ->where('subject_type', User::class) // Only get activities with User subjects
            ->whereNotNull('subject_id') // Ensure subject exists
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get(['id', 'description', 'subject_type', 'subject_id', 'properties', 'causer_id', 'created_at']);
    }
}
