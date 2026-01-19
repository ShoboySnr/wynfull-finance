@extends('layouts.admin')

@section('title', 'Manage Assessment - ' . $module->title)

@section('content')
<div class="admin-container">
    <div class="page-header">
        <div class="header-content">
            <a href="{{ route('admin.resources.collection.modules', $module->collection) }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Modules
            </a>
            <h1>
                {{ $module->title }}
            </h1>
            <p class="subtitle">Manage assessment questions and answers</p>
        </div>
        <div class="header-actions">
            <button class="btn-secondary editModuleBtn"
                    data-id="{{ $module->id }}"
                    data-title="{{ $module->title }}"
                    data-description="{{ $module->description }}"
                    data-type="{{ $module->type }}"
                    data-video_link="{{ $module->video_link }}"
                    data-file_name="{{ $module->file_name }}"
                    data-time_limit_minutes="{{ $module->time_limit_minutes }}"
                    data-action="{{ route('admin.resources.collection.modules.update', [$module->collection, $module]) }}">
                <i class="fas fa-edit"></i> Edit Assessment Details
            </button>
            <a href="{{ route('admin.modules.assessments.results', $module) }}" class="btn-secondary">
                <i class="fas fa-chart-bar"></i> View Results
            </a>
            <button class="btn-primary" id="addQuestionBtn">
                <i class="fas fa-plus"></i> Add Question
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="assessment-stats">
        <div class="stat-card">
            <i class="fas fa-question-circle"></i>
            <div class="stat-info">
                <span class="stat-value">{{ $questions->count() }}</span>
                <span class="stat-label">Total Questions</span>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-star"></i>
            <div class="stat-info">
                <span class="stat-value">{{ $questions->sum('points') }}</span>
                <span class="stat-label">Total Points</span>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <div class="stat-info">
                <span class="stat-value">{{ $module->assessmentSubmissions()->count() }}</span>
                <span class="stat-label">Submissions</span>
            </div>
        </div>
    </div>

    <div class="questions-section">
        <h2>Questions</h2>
        
        @if($questions->isEmpty())
            <div class="empty-state-modern">
                <div class="empty-state-content">
                    <h3>No Questions Yet</h3>
                    <p>Your assessment is empty. Start building it by adding your first question.</p>
                    <button class="btn-primary btn-lg" onclick="document.getElementById('addQuestionBtn').click()">
                        <i class="fas fa-plus-circle"></i> Create Your First Question
                    </button>
                </div>
            </div>
        @else
            <div class="questions-list" id="questionsList" data-reorder-url="{{ route('admin.modules.assessments.reorder', $module) }}">
                @foreach($questions as $index => $question)
                    <div class="question-item" data-id="{{ $question->id }}">
                        <div class="drag-handle" title="Drag to reorder">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        
                        <div class="question-content">
                            <div class="question-header">
                                <span class="question-number">Q{{ $index + 1 }}</span>
                                <span class="question-type-badge {{ $question->question_type }}">
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
                                <span class="question-points">{{ $question->points }} {{ $question->points === 1 ? 'point' : 'points' }}</span>
                            </div>
                            
                            <div class="question-text">{{ $question->question_text }}</div>
                            
                            @if($question->options->isNotEmpty())
                                <div class="question-options">
                                    @foreach($question->options as $option)
                                        <div class="option-item {{ $option->is_correct ? 'correct' : '' }}">
                                            @if($option->is_correct)
                                                <i class="fas fa-check-circle"></i>
                                            @else
                                                <i class="far fa-circle"></i>
                                            @endif
                                            {{ $option->option_text }}
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            @if($question->explanation)
                                <div class="question-explanation">
                                    <strong>Explanation:</strong> {{ $question->explanation }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="question-actions">
                            <button class="btn-icon editQuestionBtn" 
                                    data-id="{{ $question->id }}"
                                    data-text="{{ $question->question_text }}"
                                    data-type="{{ $question->question_type }}"
                                    data-points="{{ $question->points }}"
                                    data-explanation="{{ $question->explanation }}"
                                    data-options="{{ $question->options->toJson() }}"
                                    data-action="{{ route('admin.modules.assessments.update', [$module, $question]) }}"
                                    title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.modules.assessments.destroy', [$module, $question]) }}" 
                                  method="POST" 
                                  class="inline-form"
                                  onsubmit="return confirm('Are you sure you want to delete this question?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@include('admin.resources.assessments.partials._question-modal')
@include('admin.resources.modules.partials._edit-module-modal')

@endsection

@push('styles')
<style>
/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.page-header p.subtitle {
    margin-bottom: 1rem;
    color: var(--text-secondary, #6b7280);
}

.header-actions {
    display: flex;
    gap: 0.75rem;
}

/* Assessment Stats */
.assessment-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.stat-card i {
    font-size: 2rem;
    color: var(--wynfull-blue, #0E4DA4);
    opacity: 0.8;
}

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary, #111827);
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary, #6b7280);
    font-weight: 500;
}

/* Questions Section */
.questions-section {
    margin: 2rem 0;
}

.questions-section > h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary, #111827);
    margin-bottom: 1.5rem;
}

/* Questions List */
.questions-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.question-item {
    background: var(--background-primary);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    gap: 1rem;
    transition: all 0.3s ease;
}

.question-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.drag-handle {
    display: flex;
    align-items: flex-start;
    padding-top: 0.25rem;
    cursor: grab;
    color: var(--text-tertiary, #9ca3af);
    transition: color 0.2s ease;
}

.drag-handle:hover {
    color: var(--wynfull-blue, #0E4DA4);
}

.drag-handle:active {
    cursor: grabbing;
}

.question-content {
    flex: 1;
}

.question-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.question-number {
    font-weight: 700;
    font-size: 1rem;
    color: var(--wynfull-blue, #0E4DA4);
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
}

.question-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.875rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
}

.question-type-badge.multiple_choice {
    background: rgba(59, 130, 246, 0.15);
    color: #3b82f6;
}

.question-type-badge.true_false {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
}

.question-type-badge.short_answer {
    background: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
}

.question-type-badge.essay {
    background: rgba(168, 85, 247, 0.15);
    color: #a855f7;
}

[data-theme="dark"] .question-type-badge.multiple_choice {
    background: rgba(59, 130, 246, 0.2);
    color: #60a5fa;
}

[data-theme="dark"] .question-type-badge.true_false {
    background: rgba(16, 185, 129, 0.2);
    color: #34d399;
}

[data-theme="dark"] .question-type-badge.short_answer {
    background: rgba(245, 158, 11, 0.2);
    color: #fbbf24;
}

[data-theme="dark"] .question-type-badge.essay {
    background: rgba(168, 85, 247, 0.2);
    color: #c084fc;
}

.question-points {
    padding: 0.375rem 0.875rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 600;
    border: 1px solid var(--border-light);
    color: var(--text-secondary, #6b7280);
}

.question-text {
    font-size: 1rem;
    line-height: 1.6;
    color: var(--text-primary, #111827);
    margin-bottom: 1rem;
    font-weight: 500;
}

.question-options {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-top: 1rem;
}

.option-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--border-light);
    border-radius: 6px;
    font-size: 0.9375rem;
    color: var(--text-secondary, #6b7280);
}

.option-item.correct {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    font-weight: 500;
}

[data-theme="dark"] .option-item.correct {
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
}

.option-item i {
    font-size: 1rem;
}

.option-item.correct i {
    color: #10b981;
}

[data-theme="dark"] .option-item.correct i {
    color: #34d399;
}

.question-explanation {
    margin-top: 1rem;
    padding: 1rem;
    background: rgba(245, 158, 11, 0.1);
    border-radius: 6px;
    font-size: 0.9375rem;
    color: var(--text-secondary, #6b7280);
}

[data-theme="dark"] .question-explanation {
    background: rgba(245, 158, 11, 0.15);
}

.question-explanation strong {
    color: var(--text-primary, #111827);
}

.question-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-end;
}

.btn-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    border: 1px solid var(--border-light);
    color: var(--text-secondary, #6b7280);
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    background: var(--background-tertiary, #f3f4f6);
    color: var(--wynfull-blue, #0E4DA4);
    border-color: var(--wynfull-blue, #0E4DA4);
}

.btn-icon.btn-danger:hover {
    background: #fef2f2;
    color: #dc2626;
    border-color: #dc2626;
}

.inline-form {
    display: inline;
}

/* Responsive Design */
@media screen and (max-width: 1024px) {
    .assessment-stats {
        grid-template-columns: 1fr;
    }
    
    .question-item {
        flex-direction: column;
    }
    
    .question-actions {
        flex-direction: row;
        justify-content: flex-end;
    }
}

@media screen and (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .header-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .header-actions .btn-secondary,
    .header-actions .btn-primary {
        width: 100%;
        justify-content: center;
    }
}

/* Modern Empty State Styling */
.empty-state-modern {
    border-radius: 16px;
    padding: 4rem 2rem;
    text-align: center;
    margin: 2rem 0;
    border: 2px solid var(--background-primary);
    position: relative;
    overflow: hidden;
}

.empty-state-modern::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(14, 77, 164, 0.03) 0%, transparent 70%);
    animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.empty-state-icon {
    position: relative;
    z-index: 1;
    margin-bottom: 2rem;
}

.icon-circle {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    background: linear-gradient(135deg, var(--wynfull-blue, #0E4DA4) 0%, #1e5bb8 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(14, 77, 164, 0.2);
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.icon-circle i {
    font-size: 3.5rem;
    color: white;
}

.empty-state-content {
    position: relative;
    z-index: 1;
}

.empty-state-content h3 {
    font-size: 2rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 0.75rem;
}

.empty-state-content > p {
    font-size: 1.125rem;
    color: #64748b;
    margin-bottom: 2.5rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.empty-state-features {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2.5rem;
    flex-wrap: wrap;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.feature-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.feature-item i {
    color: #10b981;
    font-size: 1.125rem;
}

.feature-item span {
    color: #475569;
    font-weight: 500;
    font-size: 0.9375rem;
}

.empty-state-content .btn-lg {
    padding: 1rem 2.5rem;
    font-size: 1.125rem;
    font-weight: 600;
    border-radius: 10px;
    box-shadow: 0 4px 14px rgba(14, 77, 164, 0.3);
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
}

.empty-state-content .btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(14, 77, 164, 0.4);
}

.empty-state-hint {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 8px;
    color: #92400e;
    font-size: 0.9375rem;
    font-weight: 500;
    margin-top: 1rem;
}

.empty-state-hint i {
    color: #f59e0b;
    font-size: 1.125rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .empty-state-modern {
        padding: 3rem 1.5rem;
    }
    
    .icon-circle {
        width: 100px;
        height: 100px;
    }
    
    .icon-circle i {
        font-size: 3rem;
    }
    
    .empty-state-content h3 {
        font-size: 1.5rem;
    }
    
    .empty-state-features {
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }
    
    .feature-item {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="{{ asset('assets/js/assessment-builder.js') }}"></script>
<script src="{{ asset('assets/js/admin-modules.js') }}"></script>
<script>
// Handle edit assessment details button
document.addEventListener('DOMContentLoaded', function() {
    const editButton = document.querySelector('.editModuleBtn');
    const editModuleModal = document.getElementById('editModuleModal');
    
    if (editButton && editModuleModal) {
        editButton.addEventListener('click', function() {
            const form = document.getElementById('editModuleForm');
            const titleInput = document.getElementById('edit_module_title');
            const descriptionInput = document.getElementById('edit_module_description');
            const typeSelect = document.getElementById('edit_module_type');
            const videoLinkInput = document.getElementById('edit_module_video_link');
            const timeLimitInput = document.getElementById('edit_module_time_limit');
            const currentFileNameSpan = document.getElementById('edit_current_file_name');

            // Populate form
            titleInput.value = editButton.dataset.title || '';
            descriptionInput.value = editButton.dataset.description || '';
            typeSelect.value = editButton.dataset.type || '';
            
            // Set hidden type input (since select is disabled)
            const typeHiddenInput = document.getElementById('edit_module_type_hidden');
            if(typeHiddenInput) typeHiddenInput.value = editButton.dataset.type || '';

            // Handle Video Link population
            if(videoLinkInput) videoLinkInput.value = editButton.dataset.video_link || '';

            // Handle Time Limit population
            if(timeLimitInput) timeLimitInput.value = editButton.dataset.time_limit_minutes || '';

            // Handle File Name display
            if(currentFileNameSpan) currentFileNameSpan.textContent = editButton.dataset.file_name || 'None';

            form.action = editButton.dataset.action || '#';

            // Show time limit field for assessment type
            const timeLimitField = editModuleModal.querySelector('.time-limit-field-container');
            if (timeLimitField) {
                timeLimitField.style.display = 'block';
            }

            // Hide file and video fields for assessment
            const fileField = editModuleModal.querySelector('.file-field-container');
            const videoField = editModuleModal.querySelector('.video-link-field-container');
            if (fileField) fileField.style.display = 'none';
            if (videoField) videoField.style.display = 'none';

            // Open modal
            editModuleModal.classList.add('active');
        });
        
        // Handle cancel button
        const cancelBtn = document.getElementById('editModuleModalCancel');
        const closeBtn = document.getElementById('editModuleModalClose');
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                editModuleModal.classList.remove('active');
            });
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                editModuleModal.classList.remove('active');
            });
        }
        
        // Close modal when clicking outside
        editModuleModal.addEventListener('click', function(e) {
            if (e.target === editModuleModal) {
                editModuleModal.classList.remove('active');
            }
        });
    }
});
</script>
@endpush
