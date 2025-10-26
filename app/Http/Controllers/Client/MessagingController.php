<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CoachClientAssignment;
use App\Models\Message;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    public function index(Request $request)
    {
        $authenticatedClient = $request->user();

        // Fetch only assignments where this user is the client.
        // Eager-load coach + coach profile + latest message to prevent N+1s.
        $assignments = CoachClientAssignment::query()
            ->where('client_id', $authenticatedClient->id)
            ->with([
                'coach.profile:id,user_id,avatar_path,professional_title',
                'latestMessage', // so latestMessage()->first() hits memory, not the DB
            ])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('coach_client_assignment_id', 'coach_client_assignments.id')
                    ->latest()
                    ->take(1)
            )
            ->orderByDesc('assigned_at')
            ->get();

        // Optional: log the view for audit
        activity()->useLog('chat')
            ->causedBy($authenticatedClient)
            ->event('client_conversations_list_viewed')
            ->withProperties([
                'assignments_count' => $assignments->count(),
                'ip' => $request->ip(),
                'user_agent' => mb_substr((string)$request->userAgent(), 0, 255),
            ])->log('Client viewed coach conversations list');

        return view('client.messaging.index', ['assignments' => $assignments]);
    }
}
