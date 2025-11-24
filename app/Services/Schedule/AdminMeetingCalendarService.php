<?php

namespace App\Services\Schedule;

use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminMeetingCalendarService
{
    public function listForAdminBetween(int $adminId, $from, $to): Collection
    {
        $from = Carbon::parse($from)->utc()->startOfSecond();
        $to   = Carbon::parse($to)->utc()->endOfSecond();

        return Meeting::query()
            ->with([
                'organizer:id,name,email',
                'attendees:id,name,email',
            ])
            ->where('organizer_id', $adminId)
            ->whereBetween('starts_at', [$from, $to])
            ->orderBy('starts_at')
            ->get();
    }

    public function listForAdmin(int $adminId, string $view = 'week', ?string $start = null): Collection
    {
        [$from, $to] = $this->rangeFor($view, $start);

        return Meeting::query()
            ->with([
                'organizer:id,name,email',
                'attendees:id,name,email',
            ])
            ->where('organizer_id', $adminId)
            ->whereBetween('starts_at', [$from, $to])
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * Determine date range from view type + base date.
     * Matches your coaching calendar behavior.
     */
    protected function rangeFor(string $view, ?string $start = null): array
    {
        $base = $start ? Carbon::parse($start)->utc() : now()->utc();

        return match (strtolower($view)) {
            'day'   => [$base->clone()->startOfDay(),  $base->clone()->endOfDay()],
            'month' => [$base->clone()->startOfMonth(),$base->clone()->endOfMonth()],
            default => [$base->clone()->startOfWeek(), $base->clone()->endOfWeek()], // week
        };
    }

    /**
     * Format for calendar UI (FullCalendar style).
     * $tz optional; if passed, convert start/end to that tz for display.
     */
    public function formatForCalendar(Collection $meetings, ?string $tz = null): array
    {
        $tz = $tz ?: config('app.timezone', 'UTC');

        return $meetings->map(function (Meeting $m) use ($tz) {
            $start = $m->starts_at->clone()->setTimezone($tz);
            $end   = $m->ends_at->clone()->setTimezone($tz);

            // Audience label (broadcast)
            $audienceLabel = match ($m->audience ?? 'single') {
                'all_clients' => 'All Clients',
                'all_coaches' => 'All Coaches',
                'all'         => 'All Clients & Coaches',
                default       => null,
            };

            // Attendee names (single or small preview)
            $attendeeNames = $m->attendees
                ? $m->attendees->pluck('name')->take(3)->join(', ')
                : '—';

            // For coach-style "client" field, we’ll call it "user"
            // If broadcast, user label becomes the audience label.
            $userLabel = $audienceLabel ?: $attendeeNames;

            // Type label similar to coaching sessions
            $typeLabel = $m->mode
                ? ucfirst($m->mode) . ' Meeting'
                : 'Meeting';

            // Title shown on calendar
            $title = $audienceLabel
                ? "Admin Meeting • {$audienceLabel}"
                : "Admin Meeting • {$attendeeNames}";

            return [
                // ===== Coach-style fields (list UI) =====
                'date'  => $start->toDateString(),        // e.g. 2025-10-13
                'time'  => $start->format('g:i A'),       // e.g. 10:00 AM
                'user'  => $userLabel,                    // single user name OR broadcast label
                'type'  => $typeLabel,                    // e.g. "Video Meeting"
                'title' => $title,
                'client' => $audienceLabel,

                // ===== Calendar/Event fields (FullCalendar UI) =====
                'id'     => $m->id,
                'start'  => $start->toIso8601String(),
                'end'    => $end->toIso8601String(),
                'allDay' => false,

                // Extra metadata for modals/popovers
                'extendedProps' => [
                    'status'       => $m->status,
                    'mode'         => $m->mode,
                    'meeting_link' => $m->meeting_link,
                    'notes'        => $m->notes,
                    'audience'     => $m->audience ?? 'single',
                    'attendees'    => $m->attendees?->map(fn($u) => [
                        'id'    => $u->id,
                        'name'  => $u->name,
                        'email' => $u->email,
                        'pivot' => [
                            'status' => $u->pivot->status ?? null,
                        ],
                    ])->values(),
                ],
            ];
        })->values()->all();
    }

}
