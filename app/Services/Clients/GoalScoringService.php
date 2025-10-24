<?php

namespace App\Services\Clients;

use App\Models\User;
use App\Services\Onboarding\WealthCardsService;
use App\Support\OnboardingGoals;

class GoalScoringService
{
    public function __construct(private readonly WealthCardsService $wealth)
    {
    }

    /**
     * Returns an array:
     * [
     *   'primary_goal_label' => string|null,
     *   'goal_score'         => int|null,   // 0..100
     *   'goal_metric_label'  => string|null // e.g., 'Emergency fund progress' or 'Investing score'
     * ]
     */
    public function forUser(User $user, float $monthlyExpenses = 0.0): array
    {
        $label = OnboardingGoals::primaryGoalLabelForUser($user->id);

        // Default
        $goalScore = null;
        $metricLabel = null;

        // If user’s primary goal is emergency fund → use progress %
        $ef = $this->wealth->emergencyFundCard($user, $monthlyExpenses);
        if ($ef && $ef['progress_pct'] !== null && $ef['progress_pct'] >= 0) {
            $goalScore   = (int) $ef['progress_pct'];
            $metricLabel = 'Emergency fund progress';
        }

        // If investing-related goals → use investing score (overrides EF if more relevant)
        $inv = $this->wealth->investingCard($user);
        if ($inv && in_array($inv['label'], ['Not started','Just starting','Consistent'], true)) {
            // If the primary goal label hints investing/wealth, prefer investing score
            $isInvestingGoal = $label && preg_match('/invest|retire|wealth/i', $label);
            if ($isInvestingGoal || $goalScore === null) {
                $goalScore   = (int) $inv['score'];
                $metricLabel = 'Investing score';
            }
        }

        return [
            'primary_goal_label' => $label,
            'goal_score'         => $goalScore,
            'goal_metric_label'  => $metricLabel,
        ];
    }
}
