@extends('layouts.admin')

@section('title', 'View Submission - ' . $submission->user->name)

@section('content')
<div class="admin-container">
    <div class="page-header">
        <div class="header-content">
            <a href="{{ route('admin.modules.assessments.results', $module) }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Results
            </a>
            <h1>
                <i class="fas fa-file-alt"></i>
                Submission Details
            </h1>
            <h3 class="subtitle">{{ $module->title }}</h3>
        </div>
    </div>

    <div class="submission-header-card">
        <div class="client-info">
            @if($submission->user->clientProfile && $submission->user->clientProfile->avatar_path)
                <img src="{{ Storage::url($submission->user->clientProfile->avatar_path) }}" 
                     alt="{{ $submission->user->name }}" 
                     class="client-avatar">
            @else
                <div class="client-avatar-placeholder">
                    {{ substr($submission->user->name, 0, 1) }}
                </div>
            @endif
            <div class="client-details">
                <h2>{{ $submission->user->name }}</h2>
                <p>{{ $submission->user->email }}</p>
                <p class="submission-date">
                    <i class="fas fa-calendar"></i>
                    Submitted on {{ $submission->submitted_at->format('F d, Y \a\t g:i A') }}
                </p>
            </div>
        </div>

        <div class="score-summary">
            <div class="score-circle-large">
                <svg viewBox="0 0 160 160" class="score-svg">
                    <circle cx="80" cy="80" r="70" class="score-bg"></circle>
                    <circle cx="80" cy="80" r="70" class="score-progress" 
                            style="stroke-dasharray: {{ 439.823 * ($submission->percentage / 100) }} 439.823"></circle>
                </svg>
            </div>
        </div>
    </div>

    <div class="answers-section">
        <h2>Answer Review</h2>

        @foreach($submission->answers as $index => $answer)
            @php
                $question = $answer->question;
                $isCorrect = $answer->is_correct;
                $needsGrading = $isCorrect === null;
            @endphp

            <div class="answer-card {{ $isCorrect ? 'correct' : ($needsGrading ? 'pending' : 'incorrect') }}">
                <div class="answer-header">
                    <div class="question-info">
                        <span class="question-number">Q{{ $index + 1 }}</span>
                        <span class="question-type-badge">
                            @switch($question->question_type)
                                @case('multiple_choice')
                                    <i class="fas fa-list-ul"></i> Multiple Choice
                                    @break
                                @case('true_false')
                                    <i class="fas fa-check-double"></i> True/False
                                    @break
                                @case('short_answer')
                                    <i class="fas fa-font"></i> Short Answer
                                    @break
                                @case('essay')
                                    <i class="fas fa-align-left"></i> Essay
                                    @break
                            @endswitch
                        </span>
                    </div>
                </div>

                <div class="question-text">{{ $question->question_text }}</div>

                @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                    <div class="options-review">
                        @foreach($question->options as $option)
                            @php
                                $isSelected = $answer->assessment_question_option_id == $option->id;
                                $isCorrectOption = $option->is_correct;
                            @endphp

                            <div class="option-item {{ $isSelected ? 'selected' : '' }} {{ $isCorrectOption ? 'correct-option' : '' }}">
                                <div class="option-indicator">
                                    @if($isSelected && $isCorrectOption)
                                        <i class="fas fa-check-circle text-success"></i>
                                    @elseif($isSelected && !$isCorrectOption)
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @elseif($isCorrectOption)
                                        <i class="fas fa-check text-success"></i>
                                    @else
                                        <i class="far fa-circle"></i>
                                    @endif
                                </div>
                                <span class="option-text">{{ $option->option_text }}</span>
                                @if($isSelected)
                                    <span class="label-badge client-answer">Client's Answer</span>
                                @endif
                                @if($isCorrectOption && !$isSelected)
                                    <span class="label-badge correct-answer">Correct Answer</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-answer-section">
                        <div class="client-answer-box">
                            <strong><i class="fas fa-user"></i> Client's Answer:</strong>
                            <p>{{ $answer->answer_text ?: 'No answer provided' }}</p>
                        </div>
                        @if($needsGrading)
                            <div class="grading-note">
                                <i class="fas fa-info-circle"></i>
                                This answer requires manual grading. Review the response and assign points accordingly.
                            </div>
                        @endif
                    </div>
                @endif

                @if($question->explanation && !$needsGrading)
                    <div class="explanation-section">
                        <strong><i class="fas fa-lightbulb"></i> Explanation:</strong>
                        <p>{{ $question->explanation }}</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

@endsection

@push('styles')
<style>
.page-header h3 {
    font-size: 1.5rem;
}

/* Submission Header Card */
.submission-header-card {
    background: var(--white);
    border: 1px solid var(--border-light);
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.client-info {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.client-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
}

.client-avatar-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--wynfull-blue);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
}

.client-details h2 {
    margin: 0 0 0.5rem 0;
    color: var(--text-primary);
    font-size: 1.5rem;
}

.client-details .client-email {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.9375rem;
}

.submission-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.submission-info i {
    font-size: 1.5rem;
    color: var(--wynfull-blue);
}

.submission-info > div {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.submission-info strong {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--text-secondary);
    letter-spacing: 0.05em;
}

.submission-info span {
    font-size: 0.9375rem;
    color: var(--text-primary);
    font-weight: 500;
}

/* Answers Section */
.answers-section {
    margin-top: 2rem;
}

.answers-section h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
}

.answer-card {
    background: var(--white);
    border: 1px solid var(--border-light);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.answer-header {
    margin-bottom: 0.5rem;
}

.answer-header .question-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.answer-header .question-info .question-type-badge {
    background: rgba(59, 130, 246, 0.2);
    color: #60a5fa;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
}

.question-number {
    font-weight: 700;
    font-size: 1rem;
    color: var(--wynfull-blue, #0E4DA4);
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
}

.question-text {
    font-size: 1rem;
    font-weight: 500;
    color: var(--text-primary);
    margin: 1rem 0;
    line-height: 1.6;
}

/* Options Review */
.options-review {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-top: 1rem;
}

.option-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: var(--white);
    border: 1px solid var(--border-light);
    border-radius: 6px;
    transition: all 0.2s;
}

.option-item.selected {
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

.option-item.selected .option-indicator {
    color: var(--wynfull-blue);
}

.option-text {
    flex: 1;
    color: var(--text-primary);
    font-size: 0.9375rem;
}

.label-badge {
    padding: 0.25rem 0.625rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
    background: var(--wynfull-blue);
    color: white;
}

/* Text Answer Section */
.text-answer-section {
    margin-top: 1rem;
}

.client-answer-box {
    background: var(--bg-secondary);
    padding: 1rem;
    border-radius: 6px;
    border: 1px solid var(--border-light);
}

.client-answer-box strong {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    color: var(--text-primary);
    font-size: 0.875rem;
    font-weight: 600;
}

.client-answer-box strong i {
    color: var(--wynfull-blue);
    font-size: 0.875rem;
}

.client-answer-box p {
    margin: 0;
    color: var(--text-primary);
    line-height: 1.6;
    font-size: 0.9375rem;
}

/* Responsive */
@media (max-width: 768px) {
    .submission-header-card {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .client-info {
        width: 100%;
    }
    
    .submission-info {
        width: 100%;
    }
    
    .answer-card {
        padding: 1rem;
    }
}
</style>
@endpush
