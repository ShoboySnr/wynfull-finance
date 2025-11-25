<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CoachingSession;
use App\Models\Meeting;
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

        // --- Admin upcoming meetings for this client ---
        $adminUpcoming = Meeting::query()
            ->with(['organizer:id,name,email'])
            ->where('status', 'scheduled')
            ->where('starts_at', '>=', now()->utc())
            ->where(function ($q) use ($client) {
                $q->whereIn('audience', ['all_clients', 'all'])   // broadcast to clients
                ->orWhereHas('attendees', fn($a) => $a->where('users.id', $client->id)); // explicitly invited
            })
            ->orderBy('starts_at')
            ->limit(10)
            ->get();

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

        // If no coach, return early with safe defaults
        if (! $coach) {
            return view('client.coaching.index', [
                'coach'        => null,
                'upcoming'     => collect(),
                'adminUpcoming'  => $adminUpcoming,
                'availability' => collect(),
                'notice'       => 'No active coach assigned yet. Please contact support or wait for an assignment.',
            ]);
        }

        $upcoming =  CoachingSession::query()
            ->betweenCoachAndClient($coach->id, $client->id)
            ->upcoming()
            ->get();


        // Availability + slots (use coach->settings; avoid the old availabilitySetting name)
        $settings = $coach->settings;
        $tz = $settings->timezone ?? config('app.timezone');
        $date = now($tz)->toDateString();

        $slots = $settings
            ? $this->clientBookingService->generateSlots($coach, $date)
            : collect();

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
            'adminUpcoming' => $adminUpcoming,
            'availability' => $slots,
        ]);
    }
}
