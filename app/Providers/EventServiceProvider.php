<?php

namespace App\Providers;

use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        Event::listen(NotificationSending::class, function (NotificationSending $event) {
            if ($event->channel !== 'mail') {
                return null; // Only check for mail notifications
            }

            $notifiable = $event->notifiable;

            if (method_exists($notifiable, 'getAttribute') && $notifiable->getAttribute('email_notifications_enabled') === false) {
                return false; // Prevent sending the notification
            }
            return null; // Proceed with sending the notification
        });
    }
}
