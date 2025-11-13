<?php

namespace App\Notifications;

use App\Models\ResourceCollection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminCollectionPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ResourceCollection $collection)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {

        $title = $this->collection->title;

        return (new MailMessage)
            ->subject("New Resource Collection: {$title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("A new resource collection is now available: {$title}.")
            ->when($this->collection->description, fn($m) => $m->line($this->collection->description))
            ->line('This collection was published by Admin and is available to all users.');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'collection_id' => $this->collection->id,
            'title'         => $this->collection->title,
            'visibility'    => $this->collection->visibility,
            'approved_at'   => optional($this->collection->approved_at)?->toIso8601String(),
        ];
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
