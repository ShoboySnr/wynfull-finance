<?php

namespace App\Services\Onboarding;

use App\Models\ClientOnboarding;
use App\Models\User;
use Illuminate\Support\Arr;

class InvestingHabitService
{
    /**
     * Compute Investing Habit / Contribution Readiness from latest onboarding.
     * Based on Step 7: "What's your investing experience?"
     * 
     * @param User $user
     * @return array{experience:string,label:string,percentage:int,badge:array}|null
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
     * Compute investing habit readiness from onboarding answers.
     * 
     * @param array|string|null $answers
     * @return array{experience:string,label:string,percentage:int,badge:array}
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

        // Use investing_experience (Step 7: Investing experience level)
        $key = strtolower(trim((string) Arr::get($answers, 'investing_experience', '')));

        // Map experience to readiness levels
        $map = [
            'beginner'     => [
                'label'      => 'Building Foundation',
                'percentage' => 33,
                'badge'      => ['text' => 'Beginner', 'style' => 'warning']
            ],
            'intermediate' => [
                'label'      => 'Growing Confidence',
                'percentage' => 66,
                'badge'      => ['text' => 'Intermediate', 'style' => 'info']
            ],
            'advanced'     => [
                'label'      => 'Experienced Investor',
                'percentage' => 100,
                'badge'      => ['text' => 'Advanced', 'style' => 'success']
            ],
        ];

        $result = $map[$key] ?? [
            'label'      => 'Not Set',
            'percentage' => 0,
            'badge'      => ['text' => 'Not Set', 'style' => 'secondary']
        ];

        return [
            'experience'  => $key !== '' ? $key : 'not-set',
            'label'       => $result['label'],
            'percentage'  => $result['percentage'],
            'badge'       => $result['badge'],
        ];
    }
}
