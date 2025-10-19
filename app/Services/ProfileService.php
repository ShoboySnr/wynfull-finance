<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function updateAvatar(User $user, UploadedFile $file): Profile
    {
        $profile = $this->getFor($user);

        // delete old avatar if present
        if ($profile->avatar_path && Storage::disk('public')->exists($profile->avatar_path)) {
            Storage::disk('public')->delete($profile->avatar_path);
        }

        // store new avatar
        $ext = $file->getClientOriginalExtension() ?: $file->extension();
        $name = 'avatar_'.now()->timestamp.'_'.Str::random(6).'.'.$ext;
        $path = $file->storeAs("avatars/{$user->id}", $name, 'public');

        $profile->avatar_path = $path;
        $profile->save();

        return $profile->refresh();
    }
}
