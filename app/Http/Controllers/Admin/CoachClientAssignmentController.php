<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignCoachToClientRequest;
use App\Http\Requests\UnassignCoachFromClientRequest;
use App\Models\User;
use App\Services\Assignments\CoachClientAssignmentService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CoachClientAssignmentController extends Controller
{

    public function __construct(private readonly CoachClientAssignmentService $service)
    {
    }

    public function create()
    {
        $coaches = User::role('coach')->select('id', 'name', 'email')->orderBy('name')->get();
        $clients = User::role('client')->select('id', 'name', 'email')->orderBy('name')->get();

        return view('admin.assignments.create', compact('coaches', 'clients'));
    }

    // POST /admin/coach-client-assignments
    public function store(AssignCoachToClientRequest $request)
    {
        try {
            $data = $this->service->assign(
                coachId: (int)$request->integer('coach_id'),
                clientId: (int)$request->integer('client_id'),
                actorUserId: $request->user()?->id
            );

            // Optional: personalize the message with names
            $coach = User::find($data['coach_id']);
            $client = User::find($data['client_id']);

            return back()->with('success',
                sprintf(
                    'Coach %s was assigned to %s successfully.',
                    $coach?->name ?? 'ID ' . $data['coach_id'],
                    $client?->name ?? 'ID ' . $data['client_id']
                )
            );

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Sorry, we could not assign the coach. Please try again.');
        }
    }

    // DELETE /admin/coach-client-assignments
    // (Body: coach_id, client_id) – simpler for now than composite URI
    public function destroy(UnassignCoachFromClientRequest $request)
    {
        $this->service->unassign(
            coachId: (int)$request->integer('coach_id'),
            clientId: (int)$request->integer('client_id')
        );

        return response()->json(['message' => 'Coach unassigned from client.']);
    }

    public function unassignCoach(User $user, Request $request)
    {
        dd($user);
        $this->service->unassign(
            coachId: (int)$user->id,
            clientId: (int)$request->user()->id
        );

        return redirect()->back()->with('success', 'coach unassigned from client.');
    }

    // GET /admin/coach-client-assignments?client_id=&coach_id=
    public function index(Request $request)
    {
        if ($request->filled('client_id')) {
            $coaches = $this->service->listCoachesForClient((int)$request->integer('client_id'));
            return response()->json(['data' => $coaches]);
        }

        if ($request->filled('coach_id')) {
            $clients = $this->service->listClientsForCoach((int)$request->integer('coach_id'));
            return response()->json(['data' => $clients]);
        }

        return response()->json([
            'message' => 'Provide client_id to list coaches or coach_id to list clients.',
        ], 422);
    }
}
