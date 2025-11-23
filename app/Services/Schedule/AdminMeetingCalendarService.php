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
        return $meetings->map(function (Meeting $m) use ($tz) {
            $start = $m->starts_at->clone();
            $end   = $m->ends_at->clone();

            if ($tz) {
                $start->setTimezone($tz);
                $end->setTimezone($tz);
            }

            // Build a friendly title:
            // - If broadcast, show audience label.
            // - Else show attendee name(s).
            $audienceLabel = match ($m->audience ?? 'single') {
                'all_clients' => 'All Clients',
                'all_coaches' => 'All Coaches',
                'all'         => 'All Clients & Coaches',
                default       => null,
            };

            $attendeeNames = $m->attendees
                ? $m->attendees->pluck('name')->take(3)->join(', ')
                : '';

            $title = $audienceLabel
                ? "Admin Meeting • {$audienceLabel}"
                : "Admin Meeting • {$attendeeNames}";

            return [
                'id'    => $m->id,
                'title' => $title,
                'start' => $start->toIso8601String(),
                'end'   => $end->toIso8601String(),
                'allDay'=> false,

                // Extended props for your UI popovers/modals
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
