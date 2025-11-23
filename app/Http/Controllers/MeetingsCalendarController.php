<?php

namespace App\Http\Controllers;

use App\Services\Schedule\AdminMeetingCalendarService;
use Illuminate\Http\Request;

class MeetingsCalendarController extends Controller
{
    public function __construct(private readonly AdminMeetingCalendarService $service) {}

    public function feed(Request $request)
    {
        $adminId = (int) ($request->query('admin_id') ?: auth()->id());
        $tz      = $request->query('tz');

        $start = $request->query('start'); // ISO date or datetime
        $end   = $request->query('end');   // ISO date or datetime

        try {
            if ($start && $end) {
                $meetings = $this->service->listForAdminBetween($adminId, $start, $end);
            } else {
                // Back-compat: view + optional base start
                $view  = $request->query('view', 'week');
                $base  = $request->query('start');
                $meetings = $this->service->listForAdmin($adminId, $view, $base);
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid date parameters.'], 422);
        }

        $payload = $this->service->formatForCalendar($meetings, $tz);

        return response()->json($payload);
    }
}
