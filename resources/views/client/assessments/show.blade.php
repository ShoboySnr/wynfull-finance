@extends('layouts.client')

@section('title', $module->title . ' - Assessment')
@section('page-title', $module->title . ' - Assessment')

@section('content')
<div class="assessment-container">
    <div class="assessment-header">
        <div class="header-content">
            <a href="{{ route('resources.library') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Resources
            </a>
            <h1>
                <i class="fas fa-clipboard-question"></i>
                {{ $module->title }}
            </h1>
            @if($module->description)
                <p class="assessment-description">{{ $module->description }}</p>
            @endif
        </div>
    </div>

    <div class="assessment-info-card">
        <div class="info-item">
            <i class="fas fa-question-circle"></i>
            <div>
                <strong>{{ $questions->count() }}</strong>
                <span>Questions</span>
            </div>
        </div>
    </div>

    @if($previousSubmissions->isNotEmpty())
        <div class="retake-section" id="retakeSection">
            <div class="retake-card">
                <div class="retake-content">
                    <i class="fas fa-redo-alt"></i>
                    <div>
                        <h3>Ready to take the assessment again?</h3>
                        <p>Click the button below to start a new attempt</p>
                    </div>
                </div>
                <button type="button" class="btn-primary btn-lg" id="retakeBtn">
                    <i class="fas fa-play"></i> Retake Assessment
                </button>
            </div>
        </div>

        <div class="previous-submissions-card">
            <h3><i class="fas fa-history"></i> Previous Submissions</h3>
            <p>You have completed this assessment {{ $previousSubmissions->count() }} {{ $previousSubmissions->count() === 1 ? 'time' : 'times' }}.</p>
            <div class="submissions-list">
                @foreach($previousSubmissions as $submission)
                    <a href="{{ route('client.assessments.results', [$resourceCollection, $module, $submission]) }}" class="submission-item">
                        <i class="fas fa-file-alt"></i>
                        <span>{{ $submission->submitted_at->format('M d, Y \a\t g:i A') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <div id="assessmentFormContainer" style="{{ $previousSubmissions->isNotEmpty() ? 'display: none;' : '' }}">
        @if($questions->isEmpty())
            <div class="empty-state">
                <h3>No Questions Available</h3>
                <p>This assessment hasn't been set up yet. Please check back later.</p>
            </div>
        @else
            <form action="{{ route('client.assessments.submit', [$resourceCollection, $module]) }}" method="POST" id="assessmentForm">
                @csrf
                
                <div class="questions-container">
                    @foreach($questions as $index => $question)
                        <div class="question-card" data-question-id="{{ $question->id }}">
                            <div class="question-header">
                                <span class="question-number">Question {{ $index + 1 }} of {{ $questions->count() }}</span>
                            </div>
                            
                            <div class="question-text">{{ $question->question_text }}</div>
                            
                            <div class="answer-section">
                                @switch($question->question_type)
                                    @case('multiple_choice')
                                        <div class="options-list">
                                            @foreach($question->options as $option)
                                                <label class="option-label">
                                                    <input type="radio" 
                                                           name="answers[{{ $question->id }}]" 
                                                           value="{{ $option->id }}"
                                                           required>
                                                    <span class="option-text">{{ $option->option_text }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @break

                                    @case('true_false')
                                        <div class="options-list">
                                            @foreach($question->options as $option)
                                                <label class="option-label">
                                                    <input type="radio" 
                                                           name="answers[{{ $question->id }}]" 
                                                           value="{{ $option->id }}"
                                                           required>
                                                    <span class="option-text">{{ $option->option_text }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @break

                                    @case('short_answer')
                                        <input type="text" 
                                               name="answers[{{ $question->id }}]" 
                                               class="form-input short-answer-input"
                                               placeholder="Enter your answer..."
                                               required>
                                        @break

                                    @case('essay')
                                        <textarea name="answers[{{ $question->id }}]" 
                                                  class="form-textarea essay-textarea"
                                                  rows="6"
                                                  placeholder="Write your answer here..."
                                                  required></textarea>
                                        @break
                                @endswitch
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="assessment-footer">
                    <div class="progress-info">
                        <span id="answeredCount">0 </span>&nbsp; of {{ $questions->count() }} questions answered
                    </div>
                    <button type="submit" class="btn-primary btn-lg" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Submit Assessment
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal" id="confirmSubmitModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Submission</h3>
            <button class="modal-close" id="closeConfirmModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p id="confirmMessage">Are you sure you want to submit your assessment?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" id="cancelSubmit">Cancel</button>
            <button type="button" class="btn-primary" id="confirmSubmit">
                <i class="fas fa-paper-plane"></i> Confirm Assessment
            </button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.assessment-container {
    margin: 0 auto;
    padding: 2rem 1rem;
}

.assessment-header {
    margin-bottom: 2rem;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--wynfull-blue, #0E4DA4);
    text-decoration: none;
    font-size: 0.9375rem;
    margin-bottom: 1rem;
    transition: opacity 0.2s;
}

.back-link:hover {
    opacity: 0.8;
}

.assessment-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary, #111827);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.assessment-header h1 i {
    color: var(--wynfull-blue, #0E4DA4);
}

.assessment-description {
    color: var(--text-secondary, #6b7280);
    font-size: 1rem;
    line-height: 1.6;
}

.alert {
    padding: 1rem 1.25rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-info {
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.3);
    color: #1e40af;
}

[data-theme="dark"] .alert-info {
    background: rgba(59, 130, 246, 0.15);
    color: #60a5fa;
}

.alert-link {
    color: var(--wynfull-blue, #0E4DA4);
    font-weight: 600;
    text-decoration: underline;
}

.previous-submissions-card {
    background: var(--bg-secondary);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.previous-submissions-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.previous-submissions-card h3 i {
    color: var(--wynfull-blue);
}

.previous-submissions-card p {
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.submissions-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.submission-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background: var(--white);
    border: 1px solid var(--border-light);
    border-radius: 8px;
    text-decoration: none;
    color: var(--text-primary);
    transition: all 0.2s;
}

.submission-item:hover {
    border-color: var(--wynfull-blue);
    background: var(--bg-secondary);
}

.submission-item i:first-child {
    color: var(--wynfull-blue);
}

.submission-item span {
    flex: 1;
}

.submission-item i:last-child {
    color: var(--text-tertiary);
    font-size: 0.875rem;
}

.view-all-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--wynfull-blue);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9375rem;
}

.view-all-link:hover {
    text-decoration: underline;
}

.assessment-info-card {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: var(--white);
    border: 1px solid var(--border-light);
    border-radius: 12px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.info-item i {
    font-size: 2rem;
    color: var(--wynfull-blue, #0E4DA4);
    opacity: 0.8;
}

.info-item div {
    display: flex;
    flex-direction: column;
}

.info-item strong {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary, #111827);
}

.info-item span {
    font-size: 0.875rem;
    color: var(--text-secondary, #6b7280);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--background-secondary, #ffffff);
    border: 1px solid var(--border-light, #e5e7eb);
    border-radius: 12px;
}

.empty-state i {
    font-size: 4rem;
    color: var(--text-tertiary, #9ca3af);
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary, #111827);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--text-secondary, #6b7280);
}

.questions-container {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.question-card {
    background: var(--white);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    padding: 1.5rem;
    transition: border-color 0.2s;
}

.question-card.answered {
    border-color: var(--wynfull-blue);
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border-light);
}

.question-number {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--wynfull-blue, #0E4DA4);
}

.question-points {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-secondary, #6b7280);
    padding: 0.25rem 0.75rem;
    border: 1px solid var(--border-light);
    border-radius: 6px;
}

.question-text {
    font-size: 1.125rem;
    font-weight: 500;
    color: var(--text-primary, #111827);
    line-height: 1.6;
    margin-bottom: 1.25rem;
}

.answer-section {
    margin-top: 1rem;
}

.options-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.option-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border: 1px solid var(--border-light, #e5e7eb);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.option-label:hover {
    background: var(--wynfull-blue);
    border-color: var(--wynfull-blue);
}

.option-label input[type="radio"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.option-text {
    flex: 1;
    font-size: 1rem;
    color: var(--text-primary, #111827);
}

.option-label:hover .option-text {
    color: var(--white);
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-light, #e5e7eb);
    border-radius: 8px;
    font-size: 1rem;
    color: var(--text-primary, #111827);
    background: var(--background-primary, #ffffff);
    transition: border-color 0.2s;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--wynfull-blue, #0E4DA4);
}

.form-textarea {
    resize: vertical;
    min-height: 120px;
    font-family: inherit;
    line-height: 1.6;
}

.assessment-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: var(--white);
    border: 1px solid var(--border-light, #e5e7eb);
    border-radius: 12px;
    position: sticky;
    bottom: 1rem;
}

.progress-info {
    font-size: 0.9375rem;
    color: var(--text-secondary, #6b7280);
    font-weight: 500;
}

.progress-info #answeredCount {
    font-weight: 700;
    color: var(--wynfull-blue, #0E4DA4);
}

.btn-lg {
    padding: 0.875rem 2rem;
    font-size: 1rem;
    font-weight: 600;
}

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: var(--white);
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-light);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.modal-header h3 i {
    color: #f59e0b;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s;
}

.modal-close:hover {
    background: var(--background-tertiary);
    color: var(--text-primary);
}

.modal-body {
    padding: 1.5rem;
}

.modal-body p {
    font-size: 1rem;
    color: var(--text-secondary);
    line-height: 1.6;
    margin: 0;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid var(--border-light);
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.modal-footer .btn-secondary,
.modal-footer .btn-primary {
    padding: 0.75rem 1.5rem;
    font-size: 0.9375rem;
    font-weight: 600;
}

/* Retake Section */
.retake-section {
    margin-bottom: 2rem;
}

.retake-card {
    background: var(--white);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
    text-align: center;
}

.retake-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.retake-content i {
    font-size: 3rem;
    color: var(--wynfull-blue);
}

.retake-content h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.retake-content p {
    font-size: 0.9375rem;
    color: var(--text-secondary);
    margin: 0;
}

@media (max-width: 768px) {
    .assessment-container {
        padding: 1rem;
    }

    .assessment-header h1 {
        font-size: 1.5rem;
    }

    .assessment-info-card {
        grid-template-columns: 1fr;
    }

    .question-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .assessment-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .assessment-footer .btn-lg {
        width: 100%;
        justify-content: center;
    }

    .modal-content {
        width: 95%;
        margin: 1rem;
    }

    .modal-footer {
        flex-direction: column;
    }

    .modal-footer .btn-secondary,
    .modal-footer .btn-primary {
        width: 100%;
        justify-content: center;
    }

    .retake-card {
        padding: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('assessmentForm');
    const submitBtn = document.getElementById('submitBtn');
    const answeredCount = document.getElementById('answeredCount');
    const retakeBtn = document.getElementById('retakeBtn');
    const retakeSection = document.getElementById('retakeSection');
    const assessmentFormContainer = document.getElementById('assessmentFormContainer');
    
    // Handle retake button click
    if (retakeBtn) {
        retakeBtn.addEventListener('click', function() {
            // Hide retake section
            if (retakeSection) {
                retakeSection.style.display = 'none';
            }
            // Show assessment form
            if (assessmentFormContainer) {
                assessmentFormContainer.style.display = 'block';
            }
            // Scroll to form
            assessmentFormContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }
    
    if (form) {
        // Track answered questions
        function updateProgress() {
            const questions = document.querySelectorAll('.question-card');
            let answered = 0;
            
            questions.forEach(question => {
                const inputs = question.querySelectorAll('input[type="radio"], input[type="text"], textarea');
                let hasAnswer = false;
                
                inputs.forEach(input => {
                    if (input.type === 'radio' && input.checked) {
                        hasAnswer = true;
                    } else if ((input.type === 'text' || input.tagName === 'TEXTAREA') && input.value.trim()) {
                        hasAnswer = true;
                    }
                });
                
                if (hasAnswer) {
                    answered++;
                    question.classList.add('answered');
                } else {
                    question.classList.remove('answered');
                }
            });
            
            if (answeredCount) {
                answeredCount.textContent = answered;
            }
        }
        
        // Add event listeners to all inputs
        form.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('change', updateProgress);
            input.addEventListener('input', updateProgress);
        });
        
        // Modal elements
        const confirmModal = document.getElementById('confirmSubmitModal');
        const closeModalBtn = document.getElementById('closeConfirmModal');
        const cancelSubmitBtn = document.getElementById('cancelSubmit');
        const confirmSubmitBtn = document.getElementById('confirmSubmit');
        const confirmMessage = document.getElementById('confirmMessage');
        
        // Modal functions
        const openModal = () => confirmModal.classList.add('active');
        const closeModal = () => confirmModal.classList.remove('active');
        
        // Confirm before submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const totalQuestions = document.querySelectorAll('.question-card').length;
            const answered = parseInt(answeredCount.textContent);
            
            // Update modal message if not all questions answered
            if (answered < totalQuestions) {
                confirmMessage.textContent = `You have only answered ${answered} out of ${totalQuestions} questions. Are you sure you want to submit your assessment?`;
            } else {
                confirmMessage.textContent = 'Are you sure you want to submit your assessment?';
            }
            
            openModal();
        });
        
        // Modal event listeners
        closeModalBtn.addEventListener('click', closeModal);
        cancelSubmitBtn.addEventListener('click', closeModal);
        confirmModal.addEventListener('click', (e) => {
            if (e.target === confirmModal) closeModal();
        });
        
        // Actual form submission
        confirmSubmitBtn.addEventListener('click', function() {
            closeModal();
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            form.submit();
        });
        
        // Initial progress update
        updateProgress();
    }
});
</script>
@endpush
