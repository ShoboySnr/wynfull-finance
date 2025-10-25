<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        return [ new PrivateChannel('chat.assignment.'.$this->message->coach_client_assignment_id) ];
    }

    public function broadcastAs(): string { return 'message.sent'; }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->message->id,
            'sender_id'  => $this->message->sender_id,
            'body'       => $this->message->body,
            'attachment' => $this->message->attachment_path,
            'created_at' => $this->message->created_at?->toIso8601String(),
        ];
    }
}
