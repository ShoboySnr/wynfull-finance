<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceModule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'resource_collection_id', 'title', 'description', 'type',
        'file_path', 'file_name', 'video_link',
        'status', 'approved_by', 'approved_at', 'rejection_reason', 'created_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public const TYPE_FILE = 'file';
    public const TYPE_VIDEO = 'video';
    public const TYPE_LINK = 'link';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public function collection(): BelongsTo
    {
        return $this->belongsTo(ResourceCollection::class, 'resource_collection_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
