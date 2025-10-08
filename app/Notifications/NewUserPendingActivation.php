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
        $actionUrl = route('admin.users.activate', $this->pendingUser);

        return (new MailMessage)
            ->subject("New {$role} awaiting activation: {$this->pendingUser->name}")
            ->view('mail.notifications.new_user_pending_activation', [
                'pendingUser' => $this->pendingUser,
                'role'       => $role,
                'actionUrl'  => $actionUrl,
                'extra'      => [
                ],
            ]);
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
