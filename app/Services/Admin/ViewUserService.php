<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class ViewUserService
{
    /**
     * Return the user preloaded with everything the view needs and a context array.
     *
     * @return array{0:\App\Models\User,1:array}
     */
    public function getUserWithContext(int $userId): array
    {
        // Load roles, profiles, and assignment relations
        $user = User::query()
            ->with([
                'roles',
                'coachProfile',
                'clientProfile',
                'coaches' => fn($q) => $q->select('users.id','users.name','users.email'),
                'clients' => fn($q) => $q->select('users.id','users.name','users.email'),
            ])
            ->findOrFail($userId);

        [$lastActiveAt, $totalSessions] = $this->computeSessionStats($user->id);

        $assignedCoachIds = $user->relationLoaded('coaches')
            ? $user->coaches->pluck('id')->all()
            : [];

        return [
            $user,
            [
                'lastActiveAt'    => $lastActiveAt,
                'totalSessions'   => $totalSessions,
                'assignedCoachIds'=> $assignedCoachIds
            ],
        ];
    }

    /**
     * List active coaches to show in the "Assign Coach" modal.
     * Excludes coaches already assigned to this client.
     */
    public function listAssignableCoaches(User $forUser, int $limit = 50)
    {
        $exclude = [];
        if ($forUser->relationLoaded('coaches') && $forUser->roles->pluck('name')->contains('client')) {
            $exclude = $forUser->coaches->pluck('id')->all();
        }

        return User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn($q) => $q->where('name', 'coach')->where('guard_name', 'web'))
            ->when(!empty($exclude), fn($q) => $q->whereNotIn('id', $exclude))
            ->orderBy('name')
            ->limit($limit)
            ->get(['id','name','email']);
    }

    /**
     * Pulls session stats from the sessions table for a given user ID.
     * sessions.last_activity is stored as UNIX timestamp (int).
     *
     * @return array{0:\Carbon\CarbonInterface|null,1:int}
     */
    private function computeSessionStats(int $userId): array
    {
        $rows = DB::table('sessions')
            ->where('user_id', $userId)
            ->get(['last_activity']);

        $total = $rows->count();
        if ($total === 0) {
            return [null, 0];
        }

        $last = $rows->max('last_activity');
        return [now()->createFromTimestamp($last), $total];
    }
}
