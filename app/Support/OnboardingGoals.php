<?php

namespace App\Support;

use App\Models\ClientOnboarding;
use Illuminate\Support\Arr;

class OnboardingGoals
{
    /** Returns a single label (or null) for the user’s latest primary goal */
    public static function primaryGoalLabelForUser(int $userId): ?string
    {
        $co = ClientOnboarding::where('user_id', $userId)
            ->orderByDesc('completed_at')
            ->first();

        if (!$co) return null;

        $key   = Arr::get($co->answers, 'primary_goal');
        $other = trim((string) Arr::get($co->answers, 'primary_goal_other', ''));

        return match ($key) {
            'pay-off-debt'      => 'Pay off or reduce debt',
            'emergency-fund'    => 'Build an emergency fund',
            'big-purchase'      => 'Save for a big purchase',
            'start-investing'   => 'Start investing or invest more',
            'wealth-retirement' => 'Grow wealth for retirement/financial independence',
            'other'             => ($other !== '' ? $other : 'Other'),
            default             => null,
        };
    }
}
