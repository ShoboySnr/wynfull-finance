<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\BookSessionRequest;
use App\Models\User;
use App\Services\Schedule\ClientBookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private readonly ClientBookingService $clientBookingService)
    {
    }
    public function create(Request $request, int $coachId)
    {
        $coach = User::findOrFail($coachId);

        // default date = today in coach tz (skip if weekend, etc. — adjust as needed)
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

//        return view('client.booking.create', compact('coach','slots','date','tz'));
    }

    public function store(Request $request, int $coachId)
    {
        dd($request->all());
        $client = $request->user();

        $session = $this->clientBookingService->book([
            'coach_id'     => $coachId,
            'client_id'    => $client->id,
            'date'         => $request->string('date'),
            'start_time'   => $request->string('start_time'),
            'title'        => $request->string('title'),
            'type'         => $request->string('type'),
            'notes'        => $request->string('notes'),
            'location_url' => $request->string('location_url'),
        ], actorId: $client->id);

        activity()->useLog('clients')
            ->performedOn($client)
            ->causedBy($client)
            ->event('client.booking.created')
            ->withProperties([
                'session_id' => $session->id,
                'coach_id'   => $session->coach_id,
                'starts_at'  => $session->starts_at,
            ])->log('Client created a session');

        return redirect()->route('client.sessions.index')
            ->with('status', 'Session booked successfully.');
    }
}
