<?php

namespace App\Services;

use App\Models\ClientProfile;
use App\Models\CoachProfile;
use App\Models\User;
use App\Notifications\NewUserPendingActivation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class RegistrationService
{
    public function registerClient(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $randomPassword = Str::random(20);

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($randomPassword),
                'is_active'=> false, // pending admin activation
            ]);
            $user->assignRole('client');

            ClientProfile::create([
                'user_id'    => $user->id,
                'goal'       => $data['goal'],
                'other_goal' => $data['other-goal'] ?? null,
                'community'  => $data['community'] ?? null,
                'notes'      => $data['notes'] ?? null,
            ]);

            activity()
                ->performedOn($user)->causedBy($user)
                ->withProperties([
                    'type'   => 'client',
                    'goal'   => $data['goal'],
                    'status' => 'pending_activation'
                ])->log('client_registered');


             Notification::send(User::role('admin')->get(), new NewUserPendingActivation($user));

            activity()->causedBy($user)
                ->performedOn($user)
                ->withProperties(['notification' => 'NewUserPendingActivation'])
                ->log('notification_dispatched');

            return $user;
        });
    }

    public function registerCoach(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $randomPassword = \Illuminate\Support\Str::random(20);

            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($randomPassword),
                'is_active'=> false,
            ]);
            $user->assignRole('coach');

            CoachProfile::create([
                'user_id'     => $user->id,
                'experience'  => $data['experience'],
                'specialties' => $data['specialties'] ?? [],
                'linkedin'    => $data['linkedin'] ?? null,
                'website'     => $data['website'] ?? null,
            ]);

            activity()
                ->performedOn($user)->causedBy($user)
                ->withProperties([
                    'type'       => 'coach',
                    'experience' => $data['experience'],
                    'status'     => 'pending_activation'
                ])->log('coach_registered');

            Notification::send(User::role('admin')->get(), new NewUserPendingActivation($user));

            activity()->causedBy($user)
                ->performedOn($user)
                ->withProperties(['notification' => 'NewUserPendingActivation'])
                ->log('notification_dispatched');

            return $user;
        });
    }
}
