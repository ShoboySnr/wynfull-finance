<?php

namespace App\Services\Schedule;

use App\Models\CoachingSession;
use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CoachScheduleService
{
    /**
     * Create a session ensuring:
     * - client is assigned to coach,
     * - no overlapping sessions for coach (and optionally client),
     * - times normalized to UTC.
     */
    public function create(array $data, int $actorId): CoachingSession
    {
        $coach  = User::findOrFail((int)$data['coach_id']);
        $client = User::findOrFail((int)$data['client_id']);

        // Check assignment (client must be assigned to coach)
        if (! $this->clientAssignedToCoach($client->id, $coach->id)) {
            throw ValidationException::withMessages([
                'client_id' => ['This client is not assigned to the selected coach.'],
            ]);
        }

        // Normalize to Carbon
        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt   = Carbon::parse($data['ends_at']);

        // Overlap check for coach (and also for the client)
        if ($this->hasOverlap($coach->id, $startsAt, $endsAt, 'coach')) {
            throw ValidationException::withMessages([
                'starts_at' => ['Coach has another session overlapping this time.'],
            ]);
        }

        if ($this->hasOverlap($client->id, $startsAt, $endsAt, 'client')) {
            throw ValidationException::withMessages([
                'starts_at' => ['Client has another session overlapping this time.'],
            ]);
        }

        return DB::transaction(function () use ($data, $actorId, $startsAt, $endsAt) {
            return CoachingSession::create([
                'coach_id'     => (int)$data['coach_id'],
                'client_id'    => (int)$data['client_id'],
                'title'        => $data['title'] ?? 'Coaching Session',
                'type'         => $data['type']  ?? null,
                'location_url' => $data['location_url'] ?? null,
                'notes'        => $data['notes'] ?? null,
                'status'       => 'scheduled',
                'starts_at'    => $startsAt->utc(),
                'ends_at'      => $endsAt->utc(),
                'created_by'   => $actorId,
            ]);
        });
    }

    public function update(CoachingSession $session, array $data): CoachingSession
    {
        $startsAt = isset($data['starts_at']) ? Carbon::parse($data['starts_at']) : $session->starts_at;
        $endsAt   = isset($data['ends_at'])   ? Carbon::parse($data['ends_at'])   : $session->ends_at;

        // If time changed, re-check overlaps
        if ($startsAt != $session->starts_at || $endsAt != $session->ends_at) {
            if ($this->hasOverlap($session->coach_id, $startsAt, $endsAt, 'coach', $session->id)) {
                throw ValidationException::withMessages(['starts_at' => ['Coach has an overlapping session.']]);
            }
            if ($this->hasOverlap($session->client_id, $startsAt, $endsAt, 'client', $session->id)) {
                throw ValidationException::withMessages(['starts_at' => ['Client has an overlapping session.']]);
            }
        }

        $session->fill([
            'title'        => $data['title'] ?? $session->title,
            'type'         => $data['type']  ?? $session->type,
            'location_url' => $data['location_url'] ?? $session->location_url,
            'notes'        => $data['notes'] ?? $session->notes,
            'status'       => $data['status'] ?? $session->status,
            'starts_at'    => $startsAt?->utc(),
            'ends_at'      => $endsAt?->utc(),
        ])->save();

        return $session->refresh();
    }

    public function cancel(CoachingSession $session): void
    {
        $session->update(['status' => 'cancelled']);
    }

    /**
     * List sessions for a coach within a time window.
     * View: 'week' | 'month' | 'day'
     */
    public function listForCoach(int $coachId, string $view = 'week', ?string $start = null): Collection
    {
        [$from, $to] = $this->rangeFor($view, $start);

        return CoachingSession::query()
            ->with(['client:id,name,email'])
            ->where('coach_id', $coachId)
            ->whereBetween('starts_at', [$from, $to])
            ->orderBy('starts_at')
            ->get();
    }

    public function listAdminMeetingsForCoachBetween(int $coachId, $from, $to): Collection
    {
        $from = Carbon::parse($from)->utc()->startOfSecond();
        $to   = Carbon::parse($to)->utc()->endOfSecond();

        return Meeting::query()
            ->with(['organizer:id,name,email', 'attendees:id,name,email'])
            ->whereBetween('starts_at', [$from, $to])
            ->where('status', 'scheduled')
            ->where(function ($q) use ($coachId) {
                $q->whereIn('audience', ['all_coaches', 'all'])  // broadcast to coaches
                ->orWhereHas('attendees', fn ($a) => $a->where('users.id', $coachId)); // single targeted
            })
            ->orderBy('starts_at')
            ->get();
    }

    public function listAdminMeetingsForCoach(int $coachId, string $view = 'week', ?string $start = null): Collection
    {
        [$from, $to] = $this->rangeFor($view, $start);

        return Meeting::query()
            ->with(['organizer:id,name,email', 'attendees:id,name,email'])
            ->whereBetween('starts_at', [$from, $to])
            ->where('status', 'scheduled')
            ->where(function ($q) use ($coachId) {
                $q->whereIn('audience', ['all_coaches', 'all'])
                    ->orWhereHas('attendees', fn ($a) => $a->where('users.id', $coachId));
            })
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * Group sessions by date string (YYYY-MM-DD) for calendar grid rendering.
     */
    public function groupByDate(Collection $sessions, string $tz = null): array
    {
        $tz = $tz ?: config('app.timezone', 'UTC');

        return $sessions->groupBy(function (CoachingSession $s) use ($tz) {
            return $s->starts_at->clone()->setTimezone($tz)->toDateString();
        })->map(function ($day) use ($tz) {
            return $day->map(function (CoachingSession $s) use ($tz) {
                return [
                    'id'         => $s->id,
                    'time'       => $s->starts_at->clone()->setTimezone($tz)->format('g:i A'),
                    'client'     => $s->client?->name,
                    'type'       => $s->type ?? 'Session',
                    'title'      => $s->title,
                    'status'     => $s->status,
                    'starts_at'  => $s->starts_at->clone()->setTimezone($tz)->toIso8601String(),
                    'ends_at'    => $s->ends_at->clone()->setTimezone($tz)->toIso8601String(),
                    'join_url'   => $s->location_url,
                ];
            })->values();
        })->toArray();
    }

    /**
     * Helpers
     */
    private function clientAssignedToCoach(int $clientId, int $coachId): bool
    {
        return DB::table('coach_client_assignments')
            ->where('client_id', $clientId)
            ->where('coach_id', $coachId)
            ->exists();
    }

    private function hasOverlap(int $userId, Carbon $start, Carbon $end, string $role, ?int $ignoreId = null): bool
    {
        $col = $role === 'coach' ? 'coach_id' : 'client_id';

        $q = CoachingSession::query()
            ->where($col, $userId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($w) use ($start, $end) {
                $w->where('starts_at', '<', $end)
                    ->where('ends_at',   '>', $start);
            });

        if ($ignoreId) $q->where('id', '!=', $ignoreId);

        return $q->exists();
    }

    private function rangeFor(string $view, ?string $start): array
    {
        $view = in_array($view, ['day','week','month'], true) ? $view : 'week';
        $base = $start ? Carbon::parse($start) : now();

        return match ($view) {
            'day' => [
                $base->copy()->startOfDay()->utc(),
                $base->copy()->endOfDay()->utc(),
            ],
            'month' => [
                $base->copy()->startOfMonth()->startOfDay()->utc(),
                $base->copy()->endOfMonth()->endOfDay()->utc(),
            ],
            default => [ // week
                $base->copy()->startOfWeek()->startOfDay()->utc(),
                $base->copy()->endOfWeek()->endOfDay()->utc(),
            ],
        };
    }

    public function formatForCalendar(
        Collection $sessions,
        ?string $tz = null,
        ?Collection $adminMeetings = null
    ): array {
        $tz = $tz ?: config('app.timezone', 'UTC');

        // Make sure we’re working with base Collections, not Eloquent\Collection
        $sessionItems = collect($sessions)->map(function (CoachingSession $s) use ($tz) {
            $start = $s->starts_at->clone()->setTimezone($tz);

            return [
                'date'   => $start->toDateString(),                // e.g. 2025-10-13
                'time'   => $start->format('g:i A'),               // e.g. 10:00 AM
                'client' => $s->client?->name ?? '—',
                'type'   => $s->type ?? 'Session',
                'title'  => $s->title,
                'source' => 'coach_session',
                'id'     => $s->id,
            ];
        });

        $adminItems = collect($adminMeetings ?: [])->map(function (Meeting $m) use ($tz) {
            $start = $m->starts_at->clone()->setTimezone($tz);

            $audienceLabel = match ($m->audience ?? 'single') {
                'all_clients' => 'All Clients',
                'all_coaches' => 'All Coaches',
                'all'         => 'All Clients & Coaches',
                default       => null,
            };

            $clientLabel = $audienceLabel ? "Admin ({$audienceLabel})" : 'Admin';

            return [
                'date'   => $start->toDateString(),
                'time'   => $start->format('g:i A'),
                'client' => $clientLabel,
                'type'   => 'Admin Meeting',
                'title'  => $m->notes
                    ? "Admin Meeting • {$m->notes}"
                    : 'Admin Meeting',
                'source'       => 'admin_meeting',
                'id'           => $m->id,
                'meeting_link' => $m->meeting_link,
            ];
        });

        // Now both are plain Collections of arrays, so merge() is safe
        return $sessionItems
            ->merge($adminItems)
            ->sortBy(fn ($i) => $i['date'].' '.$i['time'])
            ->values()
            ->all();
    }


    public function listForCoachBetween(int $coachId, $from, $to): Collection
    {
        $from = Carbon::parse($from)->utc()->startOfSecond();
        $to   = Carbon::parse($to)->utc()->endOfSecond();

        return CoachingSession::query()
            ->with(['client:id,name,email'])
            ->where('coach_id', $coachId)
            ->whereBetween('starts_at', [$from, $to])
            ->orderBy('starts_at')
            ->get();
    }
}
