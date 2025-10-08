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
            ->greeting("Hi {$notifiable->name},")
            ->line('Great news—your account has been activated by an administrator.')
            ->line('For security, please set your password now to sign in.')
            ->action('Set Password', $this->resetUrl)
            ->line('If you did not request an account, you can ignore this email.');;
    }

    public function toArray($notifiable): array
    {
        return [
            'type'     => 'account_activated',
            'resetUrl' => $this->resetUrl,
        ];
    }
}
