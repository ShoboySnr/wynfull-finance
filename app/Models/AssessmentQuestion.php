<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentQuestion extends Model
{
    protected $table = 'assessment_questions';

    protected $fillable = [
        'resource_module_id',
        'question_text',
        'question_type',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(ResourceModule::class, 'resource_module_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(AssessmentQuestionOption::class)->orderBy('sort_order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }
}
