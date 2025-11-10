<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceCollection extends Model
{
    use SoftDeletes;
    protected $fillable = ['coach_id', 'icon_class', 'title', 'description', 'approved_by', 'approved_at', 'rejection_reason', 'status'];

    protected $casts = [
        'approved_at' => 'datetime',
    ];
    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(ResourceModule::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at')
            ->whereNull('rejection_reason');
    }

    public function scopeRejected($query)
    {
        return $query->whereNull('approved_at')
            ->whereNotNull('rejection_reason');
    }

    /** Optional: computed status for display */
    public function getStatusAttribute(): string
    {
        if (! is_null($this->approved_at)) {
            return 'approved';
        }
        if (! is_null($this->rejection_reason)) {
            return 'rejected';
        }
        return 'pending';
    }
}
