<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeAccessibleViaCoaches($q, $coachIds)
    {
        return $q->whereIn('created_by', $coachIds)
            ->whereHas('collection', fn($c) => $c->approved());
    }

    public function scopeWithCompletionFor($q, int $userId)
    {
        return $q->selectSub(function ($sq) use ($userId) {
            $sq->from('resource_module_users')
                ->select('completed_at')
                ->whereColumn('resource_module_users.resource_module_id', 'resource_modules.id')
                ->where('resource_module_users.user_id', $userId)
                ->limit(1);
        }, 'completed_at');
    }

    public function completions(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'resource_module_users')
            ->withPivot(['completed_at'])
            ->withTimestamps();
    }


    public function completers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'resource_module_users')
            ->withPivot(['completed_at'])
            ->withTimestamps();
    }

    public function scopeCompletedBy($query, int $userId)
    {
        return $query->whereHas('completers', fn($q) => $q->where('users.id', $userId)->whereNotNull('resource_module_users.completed_at'));
    }

    public function directAssignees(): BelongsToMany // clients assigned by admin
    {
        return $this->belongsToMany(User::class, 'resource_module_assignments', 'resource_module_id', 'user_id')
            ->withPivot(['assigned_by','assigned_at'])
            ->withTimestamps();
    }
}
