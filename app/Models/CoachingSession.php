<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachingSession extends Model
{
    protected $fillable = [
        'coach_id', 'client_id', 'starts_at', 'ends_at',
        'title', 'type', 'location_url', 'status', 'notes', 'created_by'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeBetweenCoachAndClient($q, int $coachId, int $clientId)
    {
        return $q->where('coach_id', $coachId)->where('client_id', $clientId);
    }

    public function scopeUpcoming($q)
    {
        return $q->where('starts_at', '>=', now())->orderBy('starts_at');
    }

    public function scopePast($q)
    {
        return $q->where('starts_at', '<', now())->orderByDesc('starts_at');
    }

    public function scopeType($q, string $type)
    {
        return $q->where('type', $type);
    }

}
