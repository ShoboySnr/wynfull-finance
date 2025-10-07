<?php

namespace App\Services;

use App\Models\ClientProfile;
use App\Models\CoachProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegistrationService
{
    public function registerClient(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            $user->assignRole('client');

            ClientProfile::create([
                'user_id'    => $user->id,
                'goal'       => $data['goal'],
                'other_goal' => $data['other-goal'] ?? null,
                'community'  => $data['community'] ?? null,
                'notes'      => $data['notes'] ?? null,
            ]);

            activity()->performedOn($user)->causedBy($user)
                ->withProperties(['type'=>'client','goal'=>$data['goal']])
                ->log('client_registered');

            Auth::login($user);

            return $user;
        });
    }

    public function registerCoach(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);
            $user->assignRole('coach');

            CoachProfile::create([
                'user_id'    => $user->id,
                'experience' => $data['experience'],
                'specialties'=> $data['specialties'] ?? [],
                'linkedin'   => $data['linkedin'] ?? null,
                'website'    => $data['website'] ?? null,
            ]);

            activity()->performedOn($user)->causedBy($user)
                ->withProperties(['type'=>'coach','experience'=>$data['experience']])
                ->log('coach_registered');

            Auth::login($user);

            return $user;
        });
    }
}
