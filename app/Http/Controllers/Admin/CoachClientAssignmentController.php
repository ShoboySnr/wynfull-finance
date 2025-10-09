<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Assignments\AssignClientToCoachService;
use DB;
use Illuminate\Http\Request;

class CoachClientAssignmentController extends Controller
{
    public function create()
    {
        $coaches = User::role('coach')->select('id', 'name', 'email')->orderBy('name')->get();
        $clients = User::role('client')->select('id', 'name', 'email')->orderBy('name')->get();

        return view('admin.assignments.create', compact('coaches', 'clients'));
    }

    public function store(Request $request, AssignClientToCoachService $action)
    {
        $data = $request->validate([
            'coach_id'        => ['required', 'exists:users,id'],
            'client_id'       => ['required', 'exists:users,id'],
            'make_primary'    => ['sometimes', 'boolean'],
            'replace_primary' => ['sometimes', 'boolean'],
            'notes'           => ['nullable', 'string', 'max:2000'],
            'subscription_id' => ['nullable', 'exists:subscriptions,id'],
        ]);

        $admin  = $request->user();
        $coach  = User::findOrFail($data['coach_id']);
        $client = User::findOrFail($data['client_id']);

        $action->handle($admin, $client, $coach, $data);

        return back()->with('status', 'Client assigned to coach successfully.');
    }

    public function index()
    {
        // Simple listing
        $assignments = DB::table('coach_client_assignments')
            ->join('users as coaches', 'coaches.id', '=', 'coach_client_assignments.coach_id')
            ->join('users as clients', 'clients.id', '=', 'coach_client_assignments.client_id')
            ->select('coach_client_assignments.*', 'coaches.name as coach_name', 'clients.name as client_name')
            ->latest('coach_client_assignments.created_at')
            ->paginate(20);

        return view('admin.assignments.index', compact('assignments'));
    }

    public function end(Request $request, int $assignmentId)
    {
        $request->validate(['notes' => ['nullable', 'string', 'max:2000']]);

        DB::table('coach_client_assignments')
            ->where('id', $assignmentId)
            ->update(['status' => 'ended', 'ended_at' => now(), 'notes' => $request->notes]);

        activity()
            ->causedBy($request->user())
            ->withProperties(['assignment_id' => $assignmentId])
            ->event('admin.ended_assignment')
            ->log('Admin ended coach-client assignment');

        return back()->with('status', 'Assignment ended.');
    }
}
