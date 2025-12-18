<?php

namespace App\Services\Onboarding;

use App\Models\ClientOnboarding;
use App\Models\User;
use Illuminate\Support\Arr;

class EmergencyReadinessService
{
    /**
     * Compute Emergency Readiness Level from latest onboarding.
     * Based on Step 5: "How confident are you in understanding the steps involved in preparing for unexpected financial situations?"
     * 
     * @param User $user
     * @return array{score:int,level:string,badge:array}|null
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
     * Compute emergency readiness from onboarding answers.
     * 
     * @param array|string|null $answers
     * @return array{score:int,level:string,badge:array}
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

        // Use savings_amount (Step 5: Emergency preparedness confidence)
        $key = strtolower(trim((string) Arr::get($answers, 'savings_amount', '')));

        // Score mapping (0-100 scale)
        $map = [
            'not-confident'        => ['score' => 25,  'level' => 'Not Prepared',      'badge' => ['text' => 'Not Prepared',      'style' => 'danger']],
            'somewhat-confident'   => ['score' => 50,  'level' => 'Building Readiness', 'badge' => ['text' => 'Building Readiness', 'style' => 'warning']],
            'confident'            => ['score' => 75,  'level' => 'Well Prepared',     'badge' => ['text' => 'Well Prepared',     'style' => 'info']],
            'very-confident'       => ['score' => 100, 'level' => 'Fully Prepared',    'badge' => ['text' => 'Fully Prepared',    'style' => 'success']],
        ];

        $result = $map[$key] ?? ['score' => 25, 'level' => 'Not Set', 'badge' => ['text' => 'Not Set', 'style' => 'secondary']];

        return [
            'score' => $result['score'],
            'level' => $result['level'],
            'badge' => $result['badge'],
        ];
    }
}
