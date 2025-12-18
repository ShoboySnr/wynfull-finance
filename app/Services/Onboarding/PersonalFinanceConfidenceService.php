<?php

namespace App\Services\Onboarding;

use App\Models\ClientOnboarding;
use App\Models\User;
use Illuminate\Support\Arr;

class PersonalFinanceConfidenceService
{
    /**
     * Compute Personal Finance Confidence Score from latest onboarding.
     * Based on Step 3: "How confident do you feel understanding core personal financial concepts?"
     * 
     * @param User $user
     * @return array{score:int,band:string,level:string}|null
     */
    public function forUser(User $user): ?array
    {
        $co = ClientOnboarding::where('user_id', $user->id)
            ->orderByDesc('completed_at')
            ->first();

        if (!$co) {
            return null;
        }

        return $this->computeFromAnswers($co->answers);
    }

    /**
     * Compute score from onboarding answers.
     * 
     * @param array|string|null $answers
     * @return array{score:int,band:string,level:string}
     */
    public function computeFromAnswers(array|string|null $answers): array
    {
        if (is_string($answers)) {
            $decoded = json_decode($answers, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $answers = $decoded;
            } else {
                $answers = [];
            }
        }

        // Use confidence_level (Step 3: Understanding core personal financial concepts)
        $key = strtolower(trim((string) Arr::get($answers, 'confidence_level', '')));

        // Score mapping (0-100 scale)
        $map = [
            'not-confident'        => 25,
            'somewhat-confident'   => 50,
            'confident'            => 75,
            'very-confident'       => 100,
        ];

        $score = $map[$key] ?? 25; // default to lowest if missing/invalid

        // Band labels
        $band = match (true) {
            $score <= 25 => 'Low',
            $score <= 50 => 'Mid',
            $score <= 75 => 'High',
            default      => 'Very High',
        };

        // Level label for display
        $levelMap = [
            'not-confident'        => 'Not confident',
            'somewhat-confident'   => 'Somewhat confident',
            'confident'            => 'Confident',
            'very-confident'       => 'Very confident',
        ];

        $level = $levelMap[$key] ?? 'Not set';

        return [
            'score' => $score,
            'band'  => $band,
            'level' => $level,
        ];
    }
}
