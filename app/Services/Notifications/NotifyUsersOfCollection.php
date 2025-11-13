<?php

namespace App\Services\Notifications;

use App\Models\ResourceCollection;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotifyUsersOfCollection
{
    public function sendToAll(ResourceCollection $collection)
    {
        // Find all client + coach users
        $users = User::query()
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['client', 'coach']))
            ->select('id', 'name', 'email')
            ->chunkById(500, function ($chunk) use ($collection) {
                foreach ($chunk as $user) {
                    $this->send($user, $collection);
                }
            });
    }

    public function send(User $user, ResourceCollection $collection)
    {
        // 1) ---- EMAIL NOTIFICATION ----
        Mail::send('emails.collection_published', [
            'user'       => $user,
            'collection' => $collection,
        ], function ($msg) use ($user, $collection) {
            $msg->to($user->email)
                ->subject("New Resource Collection: {$collection->title}");
        });

        // 2) ---- IN-APP NOTIFICATION (custom table) ----
        DB::table('notifications')->insert([
            'user_id' => $user->id,
            'type'    => 'resource_collection',
            'title'   => "New Resource Collection: {$collection->title}",
            'message' => $collection->description ?? 'A new resource collection is now available.',
            'data'    => json_encode([
                'collection_id' => $collection->id,
                'visibility'    => $collection->visibility,
                'approved_at'   => $collection->approved_at,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
