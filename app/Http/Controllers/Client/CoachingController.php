<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CoachingSession;
use App\Services\Schedule\ClientBookingService;
use Illuminate\Http\Request;

class CoachingController extends Controller
{
    public function __construct(private readonly ClientBookingService $clientBookingService)
    {
    }
    public function index(Request $request)
    {
        $client = $request->user();

        // Fetch most-recent ACTIVE coach assignment (future-proof for multi-coach)
        $coach = $client->coaches()
            ->with([
                'profile:id,user_id,avatar_path,first_name,last_name,professional_title,specialities,phone,bio',
                'settings:id,coach_id,work_start_local,work_end_local,timezone,session_duration_minutes',
            ])
            ->wherePivot('status', 'active')
            ->orderByDesc('pivot_assigned_at')
            ->first();

        // Log the view
        activity()
            ->useLog('clients')
            ->performedOn($client)
            ->causedBy($client)
            ->event('client.coach.view')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => substr((string)$request->userAgent(), 0, 255),
                'coach_id' => $coach?->id,
                'assignment_id' => $coach?->pivot?->id,
            ])
            ->log('Viewed assigned coach');

        $upcoming = CoachingSession::query()
            ->betweenCoachAndClient($coach->id, $client->id)
            ->upcoming()
            ->get();


        // Fetch slots based on coach availability settings
        $tz   = optional($coach->availabilitySetting)->timezone ?? config('app.timezone');
        $date = now($tz)->toDateString();

        $slots = $this->clientBookingService->generateSlots($coach, $date);

        activity()->useLog('clients')
            ->performedOn($request->user())
            ->causedBy($request->user())
            ->event('client.booking.view')
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => substr((string)$request->userAgent(), 0, 255),
                'coach_id' => $coach->id,
            ])->log('Viewed booking form');

        return view('client.coaching.index', [
            'coach' => $coach,
            'upcoming' => $upcoming,
            'availability' => $slots,
        ]);
    }
}
