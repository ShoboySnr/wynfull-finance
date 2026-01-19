@extends('layouts.client')

@section('title', 'Assessment Results - ' . $module->title)
@section('page-title', 'Assessment Results - ' . $module->title)

@section('content')
<div class="assessment-container">
    <div class="assessment-header">
        <div class="header-content">
            <a href="{{ route('client.assessments.show', [$resourceCollection, $module]) }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Submissions
            </a>
            <h1>
                Assessment Results
            </h1>
            <p class="assessment-description">{{ $module->title }}</p>
        </div>
    </div>

    <div class="assessment-info-card score-summary-card">
        <div class="submission-info">
            <div class="info-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="info-details">
                <h2>Assessment Submitted</h2>
                <p class="submitted-date">
                    <i class="fas fa-calendar-check"></i> {{ $submission->submitted_at->format('M d, Y \a\t g:i A') }}
                </p>
            </div>
        </div>
    </div>

    <div class="questions-container">
        <div class="section-header">
            <h2>Review Your Answers</h2>
        </div>
        
        @foreach($submission->answers as $index => $answer)
            @php
                $question = $answer->question;
                $isCorrect = $answer->is_correct;
                $needsGrading = $isCorrect === null;
            @endphp
            
            <div class="question-card answer-review-card">
                <div class="question-header">
                    <span class="question-number">Question {{ $index + 1 }} of {{ $submission->answers->count() }}</span>
                </div>
                
                <div class="question-text">{{ $question->question_text }}</div>
                
                @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                    <div class="answer-section options-list">
                        @foreach($question->options as $option)
                            @php
                                $isSelected = $answer->assessment_question_option_id == $option->id;
                            @endphp
                            
                            <div class="option-label option-review {{ $isSelected ? 'selected' : '' }}">
                                <div class="option-indicator">
                                    @if($isSelected)
                                        <i class="fas fa-check-circle"></i>
                                    @else
                                        <i class="far fa-circle"></i>
                                    @endif
                                </div>
                                <span class="option-text">{{ $option->option_text }}</span>
                                @if($isSelected)
                                    <span class="your-answer-label">Your Answer</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="answer-section text-answer-review">
                        <div class="your-answer">
                            <strong><i class="fas fa-pen"></i> Your Answer:</strong>
                            <p>{{ $answer->answer_text }}</p>
                        </div>
                    </div>
                @endif
                
            </div>
        @endforeach
    </div>

    <div class="assessment-footer">
        <a href="{{ route('client.assessments.show', [$resourceCollection, $module]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Submissions
        </a>
    </div>
</div>

@endsection

@push('styles')
<style>
.header-content h1 {
    font-size: 25px;
}

/* Score Summary Card */
.score-summary-card {
    background-color: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: 10px;
    color: var(--text-primary);
    margin: 2rem 0;
    padding: 2rem;
}

.submission-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.info-icon {
    font-size: 3rem;
    color: var(--wynfull-blue);
}

.info-details h2 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.submitted-date {
    color: var(--text-secondary);
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.submitted-date i {
    color: var(--wynfull-blue);
}

/* Section Header */
.section-header {
    margin-bottom: 2rem;
}

.section-header h2 {
    font-size: 1.125rem;
    color: var(--text-primary);
    font-weight: 600;
}

/* Answer Review Cards */
.answer-review-card {
    background: var(--white);
    border: 1px solid var(--border-light);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.question-text {
    font-size: 1rem;
    margin: 1rem 0;
    font-weight: 500;
    color: var(--text-primary);
    line-height: 1.6;
}

.question-header {
    margin-bottom: 0.5rem;
}

.question-number {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.answer-section {
    margin-top: 1rem;
}

/* Options Review */
.options-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.option-review {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: 6px;
    border: 1px solid var(--border-light);
    background: var(--white);
    transition: all 0.2s;
}

.option-review.selected {
    background: rgba(14, 77, 164, 0.05);
    border-color: var(--wynfull-blue);
}

.option-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    color: var(--text-tertiary);
}

.option-review.selected .option-indicator {
    color: var(--wynfull-blue);
}

.option-text {
    flex: 1;
    color: var(--text-primary);
    font-size: 0.9375rem;
}

.your-answer-label {
    font-size: 0.75rem;
    padding: 0.25rem 0.625rem;
    border-radius: 4px;
    font-weight: 500;
    background: var(--wynfull-blue);
    color: white;
}

/* Text Answer Review */
.text-answer-review {
    margin-top: 0;
}

.your-answer {
    background: var(--bg-secondary);
    padding: 1rem;
    border-radius: 6px;
    border: 1px solid var(--border-light);
}

.your-answer strong {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    color: var(--text-primary);
    font-size: 0.875rem;
    font-weight: 600;
}

.your-answer strong i {
    color: var(--wynfull-blue);
    font-size: 0.875rem;
}

.your-answer p {
    color: var(--text-primary);
    line-height: 1.6;
    margin: 0;
    font-size: 0.9375rem;
}


/* Footer */
.assessment-footer {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--border-color, #e5e7eb);
    display: flex;
    justify-content: center;
}

/* Responsive */
@media (max-width: 768px) {
    .header-content h1 {
        font-size: 20px;
    }
    
    .submission-info {
        flex-direction: column;
        text-align: center;
    }
    
    .answer-review-card {
        padding: 1rem;
    }
    
    .option-review {
        padding: 0.625rem;
    }
}
</style>
@endpush
