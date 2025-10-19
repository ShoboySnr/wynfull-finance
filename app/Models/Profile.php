<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'professional_title',
        'specialities', 'email', 'phone', 'bio', 'avatar_path',
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

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path ? Storage::url($this->avatar_path) : null;
    }

    public function getSpecialitiesArrayAttribute(): array
    {
        if (!is_string($this->specialities) || $this->specialities === '') return [];
        return collect(explode(',', $this->specialities))
            ->map(fn($s) => trim($s))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
