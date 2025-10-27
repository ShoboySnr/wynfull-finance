<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YourAccountDeactivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Wynfull account has been deactivated')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your account has been deactivated by an administrator.');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
