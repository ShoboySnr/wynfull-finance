<?php

namespace App\Services\Assignments;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CoachClientAssignmentService
{
    public function assign(int $coachId, int $clientId, ?int $actorUserId = null): array
    {
        if ($coachId === $clientId) {
            throw ValidationException::withMessages([
                'coach_id' => ['Coach and client cannot be the same user.'],
            ]);
        }

        /** @var User $coach */
        $coach = User::query()->findOrFail($coachId);
        /** @var User $client */
        $client = User::query()->findOrFail($clientId);

        // Optional role checks (auto-skip if Spatie not installed)
        if (method_exists($coach, 'hasRole') && !$coach->hasRole('coach')) {
            throw ValidationException::withMessages(['coach_id' => ['Selected user is not a coach.']]);
        }
        if (method_exists($client, 'hasRole') && !$client->hasRole('client')) {
            throw ValidationException::withMessages(['client_id' => ['Selected user is not a client.']]);
        }

        return DB::transaction(function () use ($client, $coach, $actorUserId) {
            // syncWithoutDetaching prevents duplicates; DB unique index is a hard guard.
            $client->coaches()->syncWithoutDetaching([
                $coach->id => [
                    'assigned_by' => $actorUserId,
                    'assigned_at' => now(),
                    'status'      => 'active',
                ],
            ]);

            // Load the fresh pivot row to return
            $pivot = $client->coaches()
                ->where('users.id', $coach->id)
                ->first()
                ?->pivot;

            return [
                'assignment_id' => $pivot?->id,
                'coach_id'      => $coach->id,
                'client_id'     => $client->id,
                'assigned_by'   => $pivot?->assigned_by,
                'assigned_at'   => $pivot?->assigned_at,
                'status'        => $pivot?->status,
            ];
        });
    }

    public function unassign(int $coachId, int $clientId): void
    {
        $client = User::query()->findOrFail($clientId);
        $client->coaches()->detach($coachId);
    }

    public function listCoachesForClient(int $clientId)
    {
        $client = User::query()->findOrFail($clientId);
        return $client->coaches()->get();
    }

    public function listClientsForCoach(int $coachId)
    {
        $coach = User::query()->findOrFail($coachId);
        return $coach->clients()->get();
    }
}
