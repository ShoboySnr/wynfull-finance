<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class ClientOnboarding extends Model
{
    protected $fillable = ['user_id', 'answers', 'completed_at'];
    protected $casts = [
        'answers' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /** The raw key stored in JSON (e.g., pay-off-debt, emergency-fund, ...) */
    public function getPrimaryGoalKeyAttribute(): ?string
    {
        return Arr::get($this->answers, 'primary_goal');
    }

    /** Human label for UI (maps to your card text) */
    public function getPrimaryGoalLabelAttribute(): ?string
    {
        $key = $this->primary_goal_key;
        $other = trim((string) Arr::get($this->answers, 'primary_goal_other', ''));

        return match ($key) {
            'pay-off-debt'     => 'Pay off or reduce debt',
            'emergency-fund'   => 'Build an emergency fund',
            'big-purchase'     => 'Save for a big purchase',
            'start-investing'  => 'Start investing or invest more',
            'wealth-retirement'=> 'Grow wealth for retirement/financial independence',
            'other'            => ($other !== '' ? $other : 'Other'),
            default            => null,
        };
    }

    /** Scope: latest onboarding per user (by completed_at) */
    public function scopeLatestPerUser($query)
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('client_onboardings as co2')
                ->whereColumn('co2.user_id', 'client_onboardings.user_id');
        });
    }

    /** Optional: if you prefer MAX(completed_at) instead of MAX(id) */
    public function scopeLatestPerUserByCompletedAt($query)
    {
        return $query->joinSub(
            fn ($q) => $q->from('client_onboardings')
                ->selectRaw('user_id, MAX(completed_at) as max_completed')
                ->groupBy('user_id'),
            'latest',
            fn ($join) => $join
                ->on('client_onboardings.user_id', '=', 'latest.user_id')
                ->on('client_onboardings.completed_at', '=', 'latest.max_completed')
        );
    }

}
