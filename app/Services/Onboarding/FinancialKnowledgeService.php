<?php

namespace App\Services\Onboarding;

use App\Models\ClientOnboarding;
use App\Models\User;
use Illuminate\Support\Arr;

class FinancialKnowledgeService
{
    /**
     * Returns:
     * [
     *   'experience_key' => 'beginner|intermediate|advanced',
     *   'experience_label' => 'Beginner|Intermediate|Advanced',
     *   'score' => 2|3|5,
     *   'source_at' => Carbon|null
     * ]
     */
    public function forUser(User $user): ?array
    {
        $co = ClientOnboarding::where('user_id', $user->id)
            ->orderByDesc('completed_at')
            ->first();

        if (!$co) return null;

        $raw = strtolower((string) Arr::get($co->answers, 'investing_experience', ''));

        // Normalize common variants
        $key = match ($raw) {
            'beginner'                          => 'beginner',
            'intermediate', 'intermediary'      => 'intermediate',
            'advanced', 'advance'               => 'advanced',
            default                             => 'beginner', // safe fallback
        };

        // Map to label + score
        $map = [
            'beginner'     => ['label' => 'Beginner',     'score' => 2],
            'intermediate' => ['label' => 'Intermediate', 'score' => 3],
            'advanced'     => ['label' => 'Advanced',     'score' => 5],
        ];

        return [
            'experience_key'   => $key,
            'experience_label' => $map[$key]['label'],
            'score'            => $map[$key]['score'],
            'source_at'        => $co->completed_at,
        ];
    }
}
