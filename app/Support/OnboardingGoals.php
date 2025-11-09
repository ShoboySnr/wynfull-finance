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

    /**
     * Returns an ordered array of labels for the user’s latest financial situations.
     * Example: ['Struggling with debt', 'Living paycheck to paycheck', ...]
     */
    public static function financialSituationsForUser(int $userId): array
    {
        $co = ClientOnboarding::where('user_id', $userId)
            ->orderByDesc('completed_at')
            ->first();

        if (!$co) return [];

        $raw = Arr::get($co->answers, 'financial_situation', []);
        $other = trim((string) Arr::get($co->answers, 'financial_situation_other', ''));

        // Accept array or comma/pipe-separated string just in case
        if (is_string($raw)) {
            $raw = preg_split('/[,\|]/', $raw) ?: [];
        }

        // Normalize keys and filter empties
        $keys = array_values(array_filter(array_map(
            fn ($v) => trim((string)$v),
            (array) $raw
        )));

        // Map to human labels
        $map = [
            'struggling-debt'       => 'struggling with debt',
            'paycheck-to-paycheck'  => 'paycheck to paycheck',
            'okay-not-saving'       => 'okay not saving',
            'saving-regularly'      => 'saving regularly',
            'confident-focused'     => 'Confident and focused',
        ];

        $labels = [];
        foreach ($keys as $k) {
            if (isset($map[$k])) {
                $labels[] = $map[$k];
            }
        }

        if ($other !== '') {
            $labels[] = $other;
        }

        // De-duplicate while preserving order
        return array_values(array_unique($labels));
    }


    /**
     * Returns ['key' => 'not-yet|just-starting|consistently', 'label' => string] or null
     */
    public static function investingStatusForUser(int $userId): ?array
    {
        $co = ClientOnboarding::where('user_id', $userId)
            ->orderByDesc('completed_at')
            ->first();

        if (!$co) {
            return null;
        }

        $status = Arr::get($co->answers ?? [], 'investing_status');

        $labels = [
            'not-yet'       => 'Not yet investing',
            'just-starting' => 'Just starting',
            'consistently'  => 'Investing consistently',
        ];

        if (!is_string($status) || !array_key_exists($status, $labels)) {
            return null;
        }

        return [
            'key'   => $status,
            'label' => $labels[$status],
        ];
    }
}
