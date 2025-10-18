<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'professional_title',
        'specialities', 'email', 'phone', 'bio', 'avatar_url',
    ];

    protected $casts = [
        'specialities' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Computed full name
    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')));
    }

    // Normalize strings (trim) on set
    protected function firstName(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => is_string($v) ? trim($v) : $v
        );
    }

    protected function lastName(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => is_string($v) ? trim($v) : $v
        );
    }

    protected function professionalTitle(): Attribute
    {
        return Attribute::make(
            set: fn ($v) => is_string($v) ? trim($v) : $v
        );
    }

    // Guard against null -> []
    protected function specialities(): Attribute
    {
        return Attribute::make(
            get: fn ($v) => $v ?: [],
        );
    }
}
