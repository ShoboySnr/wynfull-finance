<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\User;

class ProfileService
{
    public function getFor(User $user): Profile
    {
        return $user->profile ?? $user->profile()->create();
    }

    public function update(User $user, array $data): Profile
    {
        $profile = $this->getFor($user);

        // ensure array for specialities
        if (array_key_exists('specialities', $data) && is_string($data['specialities'])) {
            $data['specialities'] = collect(explode(',', $data['specialities']))
                ->map(fn ($s) => trim($s))
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $profile->fill($data)->save();

        return $profile->refresh();
    }

    public function updateAvatar(User $user, string $avatarUrl): Profile
    {
        $profile = $this->getFor($user);
        $profile->avatar_url = trim($avatarUrl);
        $profile->save();

        return $profile->refresh();
    }
}
