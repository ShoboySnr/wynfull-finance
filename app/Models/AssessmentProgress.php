<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentProgress extends Model
{
    protected $table = 'assessment_progress';

    protected $fillable = [
        'user_id',
        'resource_module_id',
        'answers',
        'time_remaining_seconds',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(ResourceModule::class, 'resource_module_id');
    }
}
