<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentQuestionOption extends Model
{
    protected $table = 'assessment_question_options';

    protected $fillable = [
        'assessment_question_id',
        'option_text',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AssessmentAnswer::class);
    }
}
