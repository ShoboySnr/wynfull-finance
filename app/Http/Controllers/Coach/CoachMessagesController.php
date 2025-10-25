<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\CoachClientAssignment;
use App\Models\Message;
use Illuminate\Http\Request;

class CoachMessagesController extends Controller
{
    public function index(Request $request)
    {
        $authenticatedUser = $request->user();

        // Eager-load client + avatar + latest message to prevent N+1s
        $assignments = CoachClientAssignment::query()
            ->forParticipant($authenticatedUser->id)
            ->with([
                'client.profile:id,user_id,avatar_path',
                'latestMessage',
            ])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('coach_client_assignment_id', 'coach_client_assignments.id')
                    ->latest()
                    ->take(1)
            )
            ->orderByDesc('assigned_at')
            ->get();

        //  activity log
        activity()->useLog('chat')
            ->causedBy($authenticatedUser)
            ->event('chat_conversations_list_viewed')
            ->withProperties(['count' => $assignments->count(), 'ip' => $request->ip()])
            ->log('Viewed chat conversations list');

        return view('coach.messages.index', ['assignments' => $assignments]);
    }
}
