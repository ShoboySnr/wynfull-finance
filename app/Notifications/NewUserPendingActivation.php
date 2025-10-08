<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserPendingActivation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct( public User $pendingUser)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $role = $this->pendingUser->getRoleNames()->first() ?? 'user';

        return (new MailMessage)
            ->subject("New {$role} awaiting activation: {$this->pendingUser->name}")
            ->greeting('Hello Admin,')
            ->line("A new {$role} has registered and is pending activation.")
            ->line("Name: {$this->pendingUser->name}")
            ->line("Email: {$this->pendingUser->email}")
            ->line('You can review and activate this account from the admin dashboard.')
            ->action('Activate User', route('admin.users.activate', $this->pendingUser))
            ->line('Thank you for keeping the community safe and verified.');
    }

    public function toArray($notifiable): array
    {
        return [
            'pending_user_id' => $this->pendingUser->id,
            'name'            => $this->pendingUser->name,
            'email'           => $this->pendingUser->email,
            'roles'           => $this->pendingUser->getRoleNames(),
            'type'            => 'pending_activation',
        ];
    }
}
