<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YourAccountActivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $resetUrl)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Wynfull account is now active')
            ->view('mail.notifications.account_activated', [
                'user'     => $notifiable,
                'resetUrl' => $this->resetUrl,
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'type'     => 'account_activated',
            'resetUrl' => $this->resetUrl,
        ];
    }
}
