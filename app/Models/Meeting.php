<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Meeting extends Model
{
    protected $fillable = [
        'organizer_id','attendee_id','starts_at','ends_at',
        'status','mode', 'meeting_link', 'notes','scheduled_by',
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

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_attendees')
            ->withPivot(['status','responded_at'])
            ->withTimestamps();
    }

    // scopes
    public function scopeUpcoming($q)
    {
        return $q->where('starts_at', '>=', now())
            ->where('status', 'scheduled');
    }
}
