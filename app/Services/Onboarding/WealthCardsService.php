<?php

namespace App\Services\Onboarding;

use App\Models\ClientOnboarding;
use App\Models\User;
use Illuminate\Support\Arr;

class WealthCardsService
{
    /**
     * Emergency Fund Card
     * Inputs:
     * - latest onboarding for user
     * - monthly expenses (pass in from profile/settings; required for a real target)
     *
     * Returns:
     * [
     *   'saved_numeric'   => float|null,
     *   'saved_display'   => string,   // e.g., "$500", "$12,500", "$20K+"
     *   'target'          => float,    // 3x or 6x monthly expenses
     *   'multiplier'      => 3|6,
     *   'progress_pct'    => int,      // 0..100
     *   'hint'            => string,   // short helper text
     *   'source_at'       => Carbon|null
     * ]
     */
    public function emergencyFundCard(User $user, float $monthlyExpenses): ?array
    {
        $co = ClientOnboarding::where('user_id', $user->id)
            ->orderByDesc('completed_at')
            ->first();

        if (!$co) return null;

        $answers   = $co->answers ?? [];
        $savings   = Arr::get($answers, 'savings_amount');         // zero, under-1k, 1k-5k, 5k-20k, 20k-plus
        $goal      = Arr::get($answers, 'primary_goal');           // emergency-fund, ...
        $multiplier= $goal === 'emergency-fund' ? 6 : 3;

        // Midpoints per spec
        $midMap = [
            'zero'      => 0,
            'under-1k'  => 500,
            '1k-5k'     => 3000,
            '5k-20k'    => 12500,
            '20k-plus'  => 20000, // for progress math only; display uses "$20K+"
        ];

        $savedNumeric = $midMap[$savings] ?? null;
        $savedDisplay = match ($savings) {
            'zero'      => '$0',
            'under-1k'  => '$1 - $1,000',
            '1k-5k'     => '$1,000 - $5,000',
            '5k-20k'    => '$5,000 - $20,000',
            '20k-plus'  => '$20,000+',
            default     => '—',
        };

        // Target & progress
        $target = max(0, round($monthlyExpenses * $multiplier, 2));
        $progressPct = 0;
        if ($savedNumeric !== null && $target > 0) {
            $progressPct = (int) min(100, round(($savedNumeric / $target) * 100));
        }

        return [
            'saved_numeric' => $savedNumeric,
            'saved_display' => $savedDisplay,
            'target'        => $target,
            'multiplier'    => $multiplier,
            'progress_pct'  => $progressPct,
            'hint'          => $multiplier === 6
                ? 'Goal boosted to 6× expenses because your primary goal is an emergency fund.'
                : 'Target is 3× your monthly expenses.',
            'source_at'     => $co->completed_at,
        ];
    }

    /**
     * Investing Card
     * Base Contribution Score per spec, then small context adjustments.
     *
     * Returns:
     * [
     *   'score'       => int (0..100),
     *   'label'       => 'Not started'|'Just starting'|'Consistent',
     *   'base'        => int,
     *   'adjustments' => array<string,int>,
     *   'source_at'   => Carbon|null
     * ]
     */
    public function investingCard(User $user): ?array
    {
        $co = ClientOnboarding::where('user_id', $user->id)
            ->orderByDesc('completed_at')
            ->first();

        if (!$co) return null;

        $a      = $co->answers ?? [];
        $status = Arr::get($a, 'investing_status');     // not-yet, just-starting, consistently
        $goal   = Arr::get($a, 'primary_goal');         // start-investing, wealth-retirement, ...
        $exp    = strtolower((string) Arr::get($a, 'investing_experience')); // beginner|intermediate|advanced
        $fin    = (array) Arr::get($a, 'financial_situation', []);

        // Base score
        $base = match ($status) {
            'not-yet'       => 20,
            'just-starting' => 50,
            'consistently'  => 80,   // “80+” target; we’ll adjust up to 90–95
            default         => 20,
        };

        // Adjustments
        $adj = 0;
        $adjustments = [];

        // Goals boost
        if (in_array($goal, ['start-investing', 'wealth-retirement'], true)) {
            $adj += 8;  $adjustments['goal_boost'] = +8;
        }

        // Experience boost
        $expKey = match ($exp) {
            'advanced', 'advance'                 => 'advanced',
            'intermediate', 'intermediary'        => 'intermediate',
            default                               => 'beginner',
        };
        if ($expKey === 'intermediate') { $adj += 5;  $adjustments['experience'] = +5; }
        if ($expKey === 'advanced')     { $adj += 10; $adjustments['experience'] = +10; }

        // Financial headwinds reduce contribution score a bit
        if (in_array('struggling-debt', $fin, true)) {
            $adj -= 10; $adjustments['fin_headwind'] = -10;
        } elseif (in_array('paycheck-to-paycheck', $fin, true)) {
            $adj -= 5;  $adjustments['fin_headwind'] = -5;
        }

        $score = (int) max(0, min(100, $base + $adj));

        // Label
        $label = match (true) {
            $score <= 25 => 'Not started',
            $score <= 74 => 'Just starting',
            default      => 'Consistent',
        };

        return [
            'score'       => $score,
            'label'       => $label,
            'base'        => $base,
            'adjustments' => $adjustments,
            'source_at'   => $co->completed_at,
        ];
    }
}
