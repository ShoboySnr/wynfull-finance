<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationService
{
    public function createMessageNotification(Message $message): void
    {
        $sender = $message->sender;
        $assignment = $message->assignment;
        
        if (!$sender || !$assignment) {
            return;
        }
        
        // Determine the recipient (the other person in the conversation)
        $recipientId = $message->sender_id == $assignment->coach_id 
            ? $assignment->client_id 
            : $assignment->coach_id;
            
        $recipient = User::find($recipientId);
        
        if (!$recipient) {
            return;
        }

        // Create website notification
        $this->createWebsiteNotification($message, $sender, $recipient);
        
        // Send email notification
        $this->sendEmailNotification($message, $sender, $recipient);
    }

    private function createWebsiteNotification(Message $message, User $sender, User $recipient): void
    {
        $senderName = $sender->name ?? 'Someone';
        $senderRole = $sender->hasRole('coach') ? 'Coach' : 'Client';
        
        $messagePreview = $message->body 
            ? (strlen($message->body) > 50 ? substr($message->body, 0, 50) . '...' : $message->body)
            : 'sent an attachment';

        Notification::create([
            'user_id' => $recipient->id,
            'type' => 'message',
            'title' => "New message from {$senderName}",
            'message' => "{$senderName} ({$senderRole}): {$messagePreview}",
            'data' => [
                'message_id' => $message->id,
                'sender_id' => $sender->id,
                'sender_name' => $senderName,
                'sender_role' => $senderRole,
                'assignment_id' => $message->coach_client_assignment_id,
                'message_preview' => $messagePreview,
            ]
        ]);
    }

    private function sendEmailNotification(Message $message, User $sender, User $recipient): void
    {
        // Send email notification
        NotificationFacade::send($recipient, new NewMessageNotification($message, $sender));
    }

    public function getUnreadNotifications(User $user, int $limit = 10): array
    {
        return Notification::forUser($user->id)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getUnreadCount(User $user): int
    {
        return Notification::forUser($user->id)->unread()->count();
    }

    public function markAsRead(int $notificationId, User $user): bool
    {
        $notification = Notification::forUser($user->id)->find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            return true;
        }
        
        return false;
    }

    public function markAllAsRead(User $user): void
    {
        Notification::forUser($user->id)->unread()->update(['read_at' => now()]);
    }
}
