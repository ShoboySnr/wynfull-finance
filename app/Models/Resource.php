<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Resource extends Model
{
    protected $fillable = [
        'coach_id',
        'title',
        'description',
        'type',
        'file_path',
        'file_name',
        'video_link',
        'status',
        'approved_by_id',
        'approved_at',
        'rejection_reason',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'approved_at' => 'datetime',
    ];


    /**
     * Get the URL for the resource file.
     *
     * @return string|null
     */
    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return Storage::disk('public')->url($this->file_path);
        }
        return null;
    }

    /**
     * Get the appropriate Font Awesome icon class based on the resource type.
     *
     * @return string
     */
    public function getIconClassAttribute(): string
    {
        return match ($this->type) {
            'pdf' => 'fa-file-pdf',
            'word' => 'fa-file-word',
            'excel' => 'fa-file-excel',
            'video' => 'fa-video',
            'template' => 'fa-file-alt',
            default => 'fa-file',
        };
    }

    /**
     * Get the user (coach) who uploaded the resource.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved the resource.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    /**
     * Get the coach that owns the resource.
     */
    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }
}
