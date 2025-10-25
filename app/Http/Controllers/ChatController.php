<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\CoachClientAssignment;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request, CoachClientAssignment $assignment)
    {
        $this->authorizeMember($request, $assignment);

        $messages = Message::query()
            ->where('coach_client_assignment_id', $assignment->id)
            ->orderByDesc('id')
            ->paginate((int)$request->query('per_page', 30));

        // log view
        activity()->useLog('chat')->causedBy($request->user())->performedOn($assignment)
            ->event('chat_messages_index_viewed')
            ->withProperties(['assignment_id'=>$assignment->id,'count'=>$messages->count()])
            ->log('Viewed chat thread');

        return response()->json([
            'ok' => true,
            'client' => $assignment->client()->with('profile')->first(),
            'data' => $messages->items(),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page'    => $messages->lastPage(),
                'total'        => $messages->total(),
            ],
        ]);
    }

    public function store(Request $request, CoachClientAssignment $assignment)
    {
        $this->authorizeMember($request, $assignment);

        $data = $request->validate([
            'body' => ['nullable','string','max:5000'],
            'attachment' => ['nullable','file','max:20480'], // 20MB
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store("chat/assignments/{$assignment->id}", 'public');
        }

        $message = Message::create([
            'coach_client_assignment_id' => $assignment->id,
            'sender_id'  => $request->user()->id,
            'body'       => trim((string)($data['body'] ?? '')) ?: null,
            'attachment_path' => $path,
        ]);

        // broadcast
        broadcast(new MessageSent($message))->toOthers();

        // activity
        activity()->useLog('chat')->causedBy($request->user())->performedOn($assignment)
            ->event('chat_message_sent')
            ->withProperties([
                'message_id'=>$message->id,
                'has_attachment'=> (bool)$path,
            ])->log('Sent message');

        return response()->json(['ok'=>true,'data'=>[
            'id'=>$message->id,'body'=>$message->body,'attachment'=>$message->attachment_path,
            'created_at'=>$message->created_at?->toIso8601String()
        ]], 201);
    }

    public function markRead(Request $request, CoachClientAssignment $assignment)
    {
        $this->authorizeMember($request, $assignment);

        // mark other party’s unread messages as read
        Message::where('coach_client_assignment_id', $assignment->id)
            ->whereNull('read_at')
            ->where('sender_id', '!=', $request->user()->id)
            ->update(['read_at' => now()]);

        activity()->useLog('chat')->causedBy($request->user())->performedOn($assignment)
            ->event('chat_thread_read')->log('Marked chat messages as read');

        return response()->json(['ok'=>true]);
    }

    private function authorizeMember(Request $request, CoachClientAssignment $assignment): void
    {
        abort_unless(
            $request->user()->id === $assignment->coach_id || $request->user()->id === $assignment->client_id,
            403
        );
    }
}
