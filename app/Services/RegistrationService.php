<?php

namespace App\Services;

use App\Models\ClientProfile;
use App\Models\CoachProfile;
use App\Models\User;
use App\Notifications\NewUserPendingActivation;
use App\Notifications\WelcomeNotification;
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
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active'=> true, // auto-activate on registration
                'activated_at' => now(),
                'email_verified_at' => now(), // auto-verify email
            ]);
            $user->assignRole('client');

            ClientProfile::create([
                'user_id'    => $user->id,
                'phone'      => $data['phone'] ?? null,
                'goal'       => $data['goal'] ?? null,
                'other_goal' => $data['other-goal'] ?? null,
                'community'  => $data['community'] ?? null,
                'notes'      => $data['notes'] ?? null,
            ]);

            activity()
                ->performedOn($user)->causedBy($user)
                ->withProperties([
                    'type'   => 'client',
                    'goal'   => $data['goal'] ?? null,
                    'status' => 'active'
                ])->log('client_registered');

            // Send welcome email
            $user->notify(new WelcomeNotification($user));

            activity()->causedBy($user)
                ->performedOn($user)
                ->withProperties(['notification' => 'WelcomeNotification'])
                ->log('notification_dispatched');

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
                'is_active'=> true, // auto-activate on registration
                'activated_at' => now(),
                'email_verified_at' => now(), // auto-verify email
            ]);

            $user->assignRole('coach');

            CoachProfile::create([
                'user_id'     => $user->id,
                'experience'  => $data['experience'] ?? null,
                'specialties' => $data['specialties'] ?? [],
                'linkedin'    => $data['linkedin'] ?? null,
                'website'     => $data['website'] ?? null,
            ]);

            activity()
                ->performedOn($user)->causedBy($user)
                ->withProperties([
                    'type'       => 'coach',
                    'experience' => $data['experience'] ?? null,
                    'status'     => 'active'
                ])->log('coach_registered');

            // Send welcome email
            $user->notify(new WelcomeNotification($user));

            activity()->causedBy($user)
                ->performedOn($user)
                ->withProperties(['notification' => 'WelcomeNotification'])
                ->log('notification_dispatched');

            return $user;
        });
    }
}
