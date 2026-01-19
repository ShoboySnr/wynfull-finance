<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentSubmission extends Model
{
    protected $table = 'assessment_submissions';

    protected $fillable = [
        'resource_module_id',
        'user_id',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(ResourceModule::class, 'resource_module_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }
}
