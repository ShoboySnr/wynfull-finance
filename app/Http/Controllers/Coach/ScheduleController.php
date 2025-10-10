<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCoachingSessionRequest;
use App\Models\CoachingSession;
use App\Services\Schedule\CoachScheduleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ScheduleController extends Controller
{
    public function __construct(private readonly CoachScheduleService $service)
    {
    }

    // GET /coach/schedule?view=week&start=2025-10-10
    public function index(Request $request)
    {
        $view  = $request->query('view', 'week');
        $start = $request->query('start');

        // Determine which coach's schedule to show:
        $coachId = $request->query('coach_id') ?: Auth::id();

        $sessions = $this->service->listForCoach((int)$coachId, $view, $start);
        $grid     = $this->service->groupByDate($sessions);

        $coach   = auth()->user();
        $clients = $coach->clients()->orderBy('name')->get(['users.id','users.name','users.email']);

        return view('coach.schedule.index', [
            'view'     => $view,
            'start'    => $start,
            'grid'     => $grid,
            'sessions' => $sessions,
            'clients'  => $clients,
            'coachId'  => $coachId,
        ]);
    }

    // POST /coach/schedule
    public function store(StoreCoachingSessionRequest $request)
    {
        try {
            $session = $this->service->create(
                $request->validated(),
                actorId: (int)$request->user()->id
            );

            return back()->with('success', 'Session scheduled successfully.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Unable to schedule session. Please try again.')->withInput();
        }
    }

    // POST /coach/schedule/{session}/cancel
    public function cancel(CoachingSession $session)
    {
        // TODO: policy to ensure only owner coach/admin can cancel

        $this->service->cancel($session);

        return back()->with('success', 'Session cancelled.');
    }
}
