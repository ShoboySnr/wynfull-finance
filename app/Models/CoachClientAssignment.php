<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachClientAssignment extends Model
{
    protected $fillable = [
        'coach_id',
        'client_id',
        'assigned_by',
        'assigned_at',
        'status',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /** The coach side of the pairing (User with role coach). */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /** The client side of the pairing (User with role client). */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /** Who performed the assignment (often the coach or an admin). */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /** Limit to assignments where this user is either the coach or the client. */
    public function scopeForParticipant($query, int $userId)
    {
        return $query->where(function ($participantQuery) use ($userId) {
            $participantQuery->where('coach_id', $userId)
                ->orWhere('client_id', $userId);
        });
    }

    /** Filter by a status string, e.g. active|pending|suspended. */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
