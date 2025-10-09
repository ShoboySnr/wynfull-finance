<?php

namespace App\Services\Assignments;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignClientToCoachService
{
    /**
     * @param  User  $admin  // acting user
     * @param  User  $client // must have 'client' role
     * @param  User  $coach  // must have 'coach' role
     * @param  array $opts   // ['make_primary'=>bool, 'replace_primary'=>bool, 'notes'=>?, 'subscription_id'=>?]
     */
    public function handle(User $admin, User $client, User $coach, array $opts = []): void
    {
        $makePrimary     = $opts['make_primary']     ?? true;
        $replacePrimary  = $opts['replace_primary']  ?? true;
        $notes           = $opts['notes']            ?? null;
        $subscriptionId  = $opts['subscription_id']  ?? null;

        if (!$admin->hasRole('admin')) {
            throw ValidationException::withMessages(['admin' => 'Only admins can assign coaches.']);
        }
        if (!$client->hasRole('client')) {
            throw ValidationException::withMessages(['client' => 'Selected user is not a client.']);
        }
        if (!$coach->hasRole('coach')) {
            throw ValidationException::withMessages(['coach' => 'Selected user is not a coach.']);
        }

        DB::transaction(function () use ($admin, $client, $coach, $makePrimary, $replacePrimary, $notes, $subscriptionId) {
            // If making primary and client already has one…
            if ($makePrimary) {
                $existingPrimary = $client->coaches()
                    ->wherePivot('is_primary', true)
                    ->wherePivot('status', 'active')
                    ->first();

                if ($existingPrimary && $existingPrimary->id !== $coach->id) {
                    if (!$replacePrimary) {
                        throw ValidationException::withMessages([
                            'is_primary' => 'Client already has a primary coach.',
                        ]);
                    }

                    // demote/end existing primary
                    $client->coaches()->updateExistingPivot(
                        $existingPrimary->id,
                        ['status' => 'ended', 'ended_at' => now(), 'is_primary' => false]
                    );
                }
            }

            // If there is already an active row for this pair, just update it
            $existing = $client->coaches()
                ->where('users.id', $coach->id)
                ->wherePivot('status', 'active')
                ->first();

            if ($existing) {
                $client->coaches()->updateExistingPivot($coach->id, [
                    'is_primary'      => $makePrimary,
                    'assigned_at'     => now(),
                    'assigned_by'     => $admin->id,
                    'subscription_id' => $subscriptionId,
                    'notes'           => $notes,
                ]);
            } else {
                $client->coaches()->attach($coach->id, [
                    'is_primary'      => $makePrimary,
                    'status'          => 'active',
                    'assigned_at'     => now(),
                    'assigned_by'     => $admin->id,
                    'subscription_id' => $subscriptionId,
                    'notes'           => $notes,
                ]);
            }

            // activity log
            activity()
                ->causedBy($admin)
                ->performedOn($client)
                ->withProperties([
                    'coach_id'        => $coach->id,
                    'client_id'       => $client->id,
                    'is_primary'      => $makePrimary,
                    'subscription_id' => $subscriptionId,
                ])
                ->event('admin.assigned_coach')
                ->log("Admin assigned client to coach");
        });
    }
}
