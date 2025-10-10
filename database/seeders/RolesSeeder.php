<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles/permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Ensure roles exist for the 'web' guard
        foreach (['admin', 'coach', 'client'] as $r) {
            Role::query()->firstOrCreate([
                'name'       => $r,
                'guard_name' => 'web',
            ]);
        }

        $now = Carbon::now();

        // --- Admin (activated + verified) ---
        /** @var User $admin */
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@wynfull.com'],
            [
                'name'                    => 'Admin User',
                'email_verified_at'       => $now,
                'password'                => Hash::make('password'), // dev only
                'remember_token'          => Str::random(10),
                'is_active'               => true,
                'activated_at'            => $now,
                'onboarding_completed'    => true,
                'onboarding_completed_at' => $now,
            ]
        );
        // set activated_by_id to self if null
        if (is_null($admin->activated_by_id)) {
            $admin->activated_by_id = $admin->id;
            $admin->save();
        }
        $admin->syncRoles(['admin']);

        // --- Coaches (3) ---
        $coaches = collect();
        foreach (range(1, 3) as $i) {
            $coach = User::query()->firstOrCreate(
                ['email' => "coach{$i}@wynfull.com"],
                [
                    'name'                    => "Coach {$i}",
                    'email_verified_at'       => $now,
                    'password'                => Hash::make('password'),
                    'remember_token'          => Str::random(10),
                    'is_active'               => true,
                    'activated_at'            => $now,
                    'activated_by_id'         => $admin->id,
                    'onboarding_completed'    => true,
                    'onboarding_completed_at' => $now,
                ]
            );
            $coach->syncRoles(['coach']);
            $coaches->push($coach);
        }

        // --- Clients (5) ---
        $clients = collect();
        foreach (range(1, 5) as $i) {
            $client = User::query()->firstOrCreate(
                ['email' => "client{$i}@wynfull.com"],
                [
                    'name'                    => "Client {$i}",
                    'email_verified_at'       => $now,
                    'password'                => Hash::make('password'),
                    'remember_token'          => Str::random(10),
                    'is_active'               => true,
                    'activated_at'            => $now,
                    'activated_by_id'         => $admin->id,
                    'onboarding_completed'    => true,
                    'onboarding_completed_at' => $now,
                ]
            );
            $client->syncRoles(['client']);
            $clients->push($client);
        }

        // --- Profiles ---
        // COACH profiles
        foreach ($coaches as $idx => $coach) {
            DB::table('coach_profiles')->updateOrInsert(
                ['user_id' => $coach->id],
                [
                    'experience'  => match ($idx) {
                        0 => '5+ years personal finance coaching',
                        1 => 'Former bank advisor, 7 years',
                        default => 'Debt recovery specialist, 4 years',
                    },
                    'specialties' => json_encode(match ($idx) {
                        0 => ['budgeting', 'emergency-fund', 'habits'],
                        1 => ['investing-basics', 'debt-snowball', 'credit-score'],
                        default => ['debt-payoff', 'income-planning'],
                    }),

                    'linkedin'    => sprintf('https://linkedin.com/in/coach%d', $idx + 1),
                    'website'     => sprintf('https://coach%d.wynfull.test', $idx + 1),
                    'updated_at'  => $now,
                    'created_at'  => $now,
                ]
            );
        }

        // CLIENT profiles
        foreach ($clients as $idx => $client) {
            DB::table('client_profiles')->updateOrInsert(
                ['user_id' => $client->id],
                [
                    'goal'        => match ($idx) {
                        0 => 'emergency-fund',
                        1 => 'debt-payoff',
                        2 => 'budget-discipline',
                        3 => 'saving-for-house',
                        default => 'investing-basics',
                    },
                    'other_goal'  => null,
                    'community'   => 'civilian',
                    'notes'       => 'Seeded client profile for testing UI and flows.',
                    'updated_at'  => $now,
                    'created_at'  => $now,
                ]
            );
        }

        // --- Sample assignments for quick UI tests (uses pivot coach_client_assignments) ---
        // Assign coach1 to clients 1–3; coach2 to clients 2–5; coach3 to client 5
        if (method_exists(User::class, 'coaches')) {
            foreach ($clients->take(3) as $client) {
                $client->coaches()->syncWithoutDetaching([
                    $coaches[0]->id => [
                        'assigned_by' => $admin->id,
                        'assigned_at' => $now,
                        'status'      => 'active',
                    ],
                ]);
            }
            foreach ($clients->slice(1, 4) as $client) {
                $client->coaches()->syncWithoutDetaching([
                    $coaches[1]->id => [
                        'assigned_by' => $admin->id,
                        'assigned_at' => $now,
                        'status'      => 'active',
                    ],
                ]);
            }
            $clients[4]->coaches()->syncWithoutDetaching([
                $coaches[2]->id => [
                    'assigned_by' => $admin->id,
                    'assigned_at' => $now,
                    'status'      => 'active',
                ],
            ]);
        }

        // Console hints
        $this->command->info('Seeded: 1 Admin, 3 Coaches, 5 Clients (activated & verified, guard:web).');
        $this->command->warn('Login with password: "password" for all seeded users (dev only).');
        $this->command->line('Admin:   admin@wynfull.com');
        $this->command->line('Coaches: coach1@wynfull.com, coach2@wynfull.com, coach3@wynfull.com');
        $this->command->line('Clients: client1@wynfull.com ... client5@wynfull.com');
    }
}
