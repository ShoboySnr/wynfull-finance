<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentAnswer extends Model
{
    protected $table = 'assessment_answers';

    protected $fillable = [
        'assessment_submission_id',
        'assessment_question_id',
        'assessment_question_option_id',
        'answer_text',
    ];

    protected $casts = [
        //
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AssessmentSubmission::class, 'assessment_submission_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestion::class, 'assessment_question_id');
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(AssessmentQuestionOption::class, 'assessment_question_option_id');
    }
}
