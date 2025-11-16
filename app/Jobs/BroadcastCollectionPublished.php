<?php

namespace App\Jobs;

use App\Models\ResourceCollection;
use App\Models\User;
use App\Services\Notifications\NotifyUsersOfCollection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastCollectionPublished implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $collectionId)
    {
    }

    public function handle(NotifyUsersOfCollection $notifier): void
    {
        $collection = ResourceCollection::find($this->collectionId);

        if (! $collection) {
            return; // collection deleted, nothing to do
        }

        // Send to all clients + coaches in chunks
        User::query()
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['client', 'coach']))
            ->select('id', 'name', 'email')
            ->chunkById(500, function ($users) use ($notifier, $collection) {
                foreach ($users as $user) {
                    $notifier->send($user, $collection);
                }
            });
    }
}
