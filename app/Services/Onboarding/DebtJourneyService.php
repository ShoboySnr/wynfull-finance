<?php

namespace App\Services\Onboarding;

use App\Models\ClientOnboarding;
use App\Models\User;
use Illuminate\Support\Arr;

class DebtJourneyService
{
    /**
     * Returns:
     * [
     *   'status'   => 'no-knowledge|basics-stressful|comfortable-applying|confident-teaching',
     *   'badge'    => ['text' => 'No Knowledge', 'style' => 'danger'], // for UI
     *   'label'    => 'No Knowledge|Learning Basics|Applying Strategies|Expert Level',
     *   'score'    => 0..100,  // journey progress
     *   'source_at'=> Carbon|null (completed_at of onboarding)
     * ]
     */
    public function forUser(User $user): ?array
    {
        $co = ClientOnboarding::where('user_id', $user->id)
            ->orderByDesc('completed_at')
            ->first();

        if (!$co) return null;

        $feeling = Arr::get($co->answers, 'debt_feeling');

        // Map to small badge + dashboard label (based on debt management understanding)
        $map = [
            'no-knowledge'         => ['badge' => ['text' => 'No Knowledge',        'style' => 'danger'],  'label' => 'No Knowledge',        'score' => 10],
            'basics-stressful'     => ['badge' => ['text' => 'Learning Basics',     'style' => 'warning'], 'label' => 'Learning Basics',     'score' => 40],
            'comfortable-applying' => ['badge' => ['text' => 'Applying Strategies', 'style' => 'info'],    'label' => 'Applying Strategies', 'score' => 70],
            'confident-teaching'   => ['badge' => ['text' => 'Expert Level',        'style' => 'success'], 'label' => 'Expert Level',        'score' => 100],
        ];

        $base = $map[$feeling] ?? ['badge' => ['text' => 'Unknown', 'style' => 'secondary'], 'label' => '—', 'score' => 0];

        // Optional micro-adjustments from related answers (safe defaults)
        $savings  = Arr::get($co->answers, 'savings_amount');       // zero, under-1k, 1k-5k, 5k-20k, 20k-plus
        $invest   = Arr::get($co->answers, 'investing_status');     // not-yet, just-starting, consistently

        $delta = 0;
        $delta += match ($savings) {
            'zero' => -5, 'under-1k' => -2, '1k-5k' => +2, '5k-20k' => +4, '20k-plus' => +6, default => 0,
        };
        $delta += match ($invest) {
            'not-yet' => -2, 'just-starting' => +2, 'consistently' => +4, default => 0,
        };

        // Clamp 0..100
        $score = max(0, min(100, $base['score'] + $delta));

        return [
            'status'    => $feeling,
            'badge'     => $base['badge'],
            'label'     => $base['label'],
            'score'     => $score,
            'source_at' => $co->completed_at,
        ];
    }
}
