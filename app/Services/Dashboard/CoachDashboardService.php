<?php

namespace App\Services\Dashboard;

use App\Models\CoachingSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

        return CoachingSession::query()
            ->where('coach_id', $coachId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('starts_at', [$from, $to])
            ->count();
    }

    /**
     * Format sessions to a handy array for the dashboard schedule.
     * Converts to the app (or provided) timezone for display.
     */
    public function formatSessionsForDashboard(Collection $sessions, ?string $tz = null): array
    {
        $tz = $tz ?: config('app.timezone', 'UTC');

        return $sessions->map(function ($s) use ($tz) {
            return [
                'id'        => $s->id,
                'date'      => $s->starts_at->clone()->setTimezone($tz)->toDateString(),
                'time'      => $s->starts_at->clone()->setTimezone($tz)->format('g:i A'),
                'client'    => $s->client?->name ?? '—',
                'client_id' => $s->client?->id,
                'title'     => $s->title,
                'type'      => $s->type ?? 'Session',
                'status'    => $s->status,
                'join_url'  => $s->location_url,
                'starts_at' => $s->starts_at->clone()->setTimezone($tz)->toIso8601String(),
                'ends_at'   => $s->ends_at->clone()->setTimezone($tz)->toIso8601String(),
            ];
        })->values()->all();
    }

    /**
     * Recent activities by the coach (Spatie activity log).
     * Adjust the log name or filters to your tastes.
     */
    public function recentActivitiesForCoach(int $coachId, int $limit = 10): Collection
    {
        return DB::table('activity_log')
        ->where('causer_id', $coachId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get([
                'id', 'description', 'subject_type', 'subject_id', 'properties', 'created_at'
            ]);
    }
}
