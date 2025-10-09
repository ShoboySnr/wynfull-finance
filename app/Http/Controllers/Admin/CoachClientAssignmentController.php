<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignCoachToClientRequest;
use App\Http\Requests\UnassignCoachFromClientRequest;
use App\Models\User;
use App\Services\Assignments\CoachClientAssignmentService;
use DB;
use Illuminate\Http\Request;

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
        $data = $this->service->assign(
            coachId: (int) $request->integer('coach_id'),
            clientId: (int) $request->integer('client_id'),
            actorUserId: $request->user()?->id
        );

        return response()->json([
            'message' => 'Coach assigned to client successfully.',
            'data'    => $data,
        ], 201);
    }

    // DELETE /admin/coach-client-assignments
    // (Body: coach_id, client_id) – simpler for now than composite URI
    public function destroy(UnassignCoachFromClientRequest $request)
    {
        $this->service->unassign(
            coachId: (int) $request->integer('coach_id'),
            clientId: (int) $request->integer('client_id')
        );

        return response()->json(['message' => 'Coach unassigned from client.']);
    }

    // GET /admin/coach-client-assignments?client_id=&coach_id=
    public function index(Request $request)
    {
        if ($request->filled('client_id')) {
            $coaches = $this->service->listCoachesForClient((int) $request->integer('client_id'));
            return response()->json(['data' => $coaches]);
        }

        if ($request->filled('coach_id')) {
            $clients = $this->service->listClientsForCoach((int) $request->integer('coach_id'));
            return response()->json(['data' => $clients]);
        }

        return response()->json([
            'message' => 'Provide client_id to list coaches or coach_id to list clients.',
        ], 422);
    }
}
