<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Message $message,
        public User $sender
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $senderName = $this->sender->name ?? 'Someone';
        $senderRole = $this->sender->hasRole('coach') ? 'Coach' : 'Client';
        
        $messagePreview = $this->message->body 
            ? (strlen($this->message->body) > 100 ? substr($this->message->body, 0, 100) . '...' : $this->message->body)
            : 'Sent an attachment';

        $actionUrl = $this->getActionUrl($notifiable);

        return (new MailMessage)
            ->subject("New Message from {$senderName}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("You have received a new message from {$senderName} ({$senderRole}).")
            ->line("Message: \"{$messagePreview}\"")
            ->action('View Message', $actionUrl)
            ->line('Thank you for using Wynfull Finance!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'assignment_id' => $this->message->coach_client_assignment_id,
            'message_preview' => $this->message->body ? substr($this->message->body, 0, 100) : 'Attachment',
        ];
    }

    private function getActionUrl(object $notifiable): string
    {
        // Redirect to the appropriate messaging page based on user role
        // The frontend will handle opening the specific conversation
        if ($notifiable->hasRole('coach')) {
            return route('messages');
        } elseif ($notifiable->hasRole('client')) {
            return route('messages.client');
        }
        
        return route('home');
    }
}
