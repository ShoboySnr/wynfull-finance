<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    protected $fillable = [
        'organizer_id','attendee_id','starts_at','ends_at',
        'status','mode','notes','scheduled_by',
        'cancelled_at','cancel_reason',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function attendee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attendee_id');
    }

    // scopes
    public function scopeUpcoming($q)
    {
        return $q->where('starts_at', '>=', now())
            ->where('status', 'scheduled');
    }
}
