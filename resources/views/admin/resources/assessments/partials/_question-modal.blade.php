{{-- Add/Edit Question Modal --}}
<div class="modal-overlay" id="questionModal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2 id="modalTitle">Add Question</h2>
            <button class="modal-close" id="questionModalClose">&times;</button>
        </div>
        <div class="modal-body">
            <form id="questionForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="form-group">
                    <label for="question_text">Question Text <span class="required">*</span></label>
                    <textarea id="question_text" 
                              name="question_text" 
                              class="form-textarea" 
                              rows="3" 
                              placeholder="Enter your question here..."
                              required></textarea>
                </div>

                <div class="form-group">
                    <label for="question_type">Question Type <span class="required">*</span></label>
                    <select id="question_type" name="question_type" class="form-select" required>
                        <option value="">Select Type</option>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="short_answer">Short Answer</option>
                        <option value="essay">Essay</option>
                    </select>
                </div>

                {{-- Options Section (for Multiple Choice and True/False) --}}
                <div id="optionsSection" style="display: none;">
                    <div class="options-header">
                        <label>Answer Options <span class="required">*</span></label>
                        <button type="button" class="btn-secondary btn-sm" id="addOptionBtn">
                            <i class="fas fa-plus"></i> Add Option
                        </button>
                    </div>
                    
                    <div id="optionsList" class="options-list">
                        {{-- Options will be dynamically added here --}}
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="questionModalCancel">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Option Template (hidden) --}}
<template id="optionTemplate">
    <div class="option-input-group">
        <input type="hidden" name="options[INDEX][id]" class="option-id">
        <div class="option-input-wrapper">
            <div class="option-text-row">
                <input type="text" 
                       name="options[INDEX][option_text]" 
                       class="form-input option-text" 
                       placeholder="Enter option text..." 
                       required>
                <button type="button" class="btn-icon btn-danger remove-option" title="Remove option">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<style>
/* Answer Options Styling */
.options-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.options-header label {
    margin: 0;
    font-weight: 600;
}

.options-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.option-input-group {
    background: var(--background-secondary);
    border: 1px solid var(--border-light);
    border-radius: 8px;
    padding: 1rem;
}

.option-input-wrapper {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.option-text-row {
    display: flex;
    gap: 0.5rem;
    align-items: flex-start;
}

.option-text-row .form-input {
    flex: 1;
}

.option-text-row .remove-option {
    flex-shrink: 0;
    margin-top: 0;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-weight: 500;
    color: #475569;
    margin: 0;
}

.checkbox-label input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.checkbox-label span {
    user-select: none;
}

/* Remove standalone remove button styling since it's now inline */
.option-input-group > .remove-option {
    display: none;
}
</style>
