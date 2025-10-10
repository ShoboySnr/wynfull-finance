<?php

namespace Database\Seeders;

use App\Models\CoachingSession;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoSessionsSeeder extends Seeder
{
    public function run(): void
    {
        $coach  = User::whereHas('roles', fn($q)=>$q->where('name','coach'))->first();
        if (!$coach) return;

        $clients = $coach->clients()->take(3)->get();
        if ($clients->isEmpty()) return;

        $base = Carbon::now()->startOfWeek()->setTime(10,0);

        foreach ($clients as $i => $client) {
            CoachingSession::firstOrCreate([
                'coach_id'  => $coach->id,
                'client_id' => $client->id,
                'starts_at' => $base->copy()->addDays($i)->utc(),
                'ends_at'   => $base->copy()->addDays($i)->addHour()->utc(),
            ], [
                'title'  => 'Financial Review',
                'type'   => 'review',
                'status' => 'scheduled',
            ]);
        }
    }
}
