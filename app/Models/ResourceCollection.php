<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceCollection extends Model
{
    use SoftDeletes;
    protected $fillable = ['coach_id', 'icon_class', 'title', 'description', 'approved_by', 'approved_at', 'rejection_reason'];

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
}
