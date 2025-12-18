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
     *   'experience_key' => 'not-familiar|familiar-basics|comfortable-applying|advanced-understanding',
     *   'experience_label' => 'Not Familiar|Familiar with Basics|Comfortable Applying|Advanced Understanding',
     *   'score' => 1|2|4|5,
     *   'source_at' => Carbon|null
     * ]
     */
    public function forUser(User $user): ?array
    {
        $co = ClientOnboarding::where('user_id', $user->id)
            ->orderByDesc('completed_at')
            ->first();

        if (!$co) return null;

        $raw = strtolower((string) Arr::get($co->answers, 'investing_status', ''));

        // Normalize to key
        $key = match ($raw) {
            'not-familiar'           => 'not-familiar',
            'familiar-basics'        => 'familiar-basics',
            'comfortable-applying'   => 'comfortable-applying',
            'advanced-understanding' => 'advanced-understanding',
            default                  => 'not-familiar', // safe fallback
        };

        // Map to label + score (1-5 scale for dots display)
        $map = [
            'not-familiar'           => ['label' => 'Not Familiar Yet',           'score' => 1],
            'familiar-basics'        => ['label' => 'Familiar with Basics',       'score' => 2],
            'comfortable-applying'   => ['label' => 'Comfortable Applying',       'score' => 4],
            'advanced-understanding' => ['label' => 'Advanced Understanding',     'score' => 5],
        ];

        return [
            'experience_key'   => $key,
            'experience_label' => $map[$key]['label'],
            'score'            => $map[$key]['score'],
            'source_at'        => $co->completed_at,
        ];
    }
}
