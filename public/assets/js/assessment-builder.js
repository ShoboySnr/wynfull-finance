document.addEventListener('DOMContentLoaded', function () {
    const questionModal = document.getElementById('questionModal');
    const addQuestionBtn = document.getElementById('addQuestionBtn');
    const questionModalClose = document.getElementById('questionModalClose');
    const questionModalCancel = document.getElementById('questionModalCancel');
    const questionForm = document.getElementById('questionForm');
    const questionTypeSelect = document.getElementById('question_type');
    const optionsSection = document.getElementById('optionsSection');
    const optionsList = document.getElementById('optionsList');
    const addOptionBtn = document.getElementById('addOptionBtn');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');

    let optionIndex = 0;
    let isEditMode = false;

    // Open modal for adding new question
    if (addQuestionBtn) {
        addQuestionBtn.addEventListener('click', () => {
            openModal('add');
        });
    }

    // Close modal
    if (questionModalClose) {
        questionModalClose.addEventListener('click', closeModal);
    }
    if (questionModalCancel) {
        questionModalCancel.addEventListener('click', closeModal);
    }
    if (questionModal) {
        questionModal.addEventListener('click', (e) => {
            if (e.target === questionModal) closeModal();
        });
    }

    // Question type change handler
    if (questionTypeSelect) {
        questionTypeSelect.addEventListener('change', function() {
            const type = this.value;
            handleQuestionTypeChange(type);
        });
    }

    // Add option button
    if (addOptionBtn) {
        addOptionBtn.addEventListener('click', () => {
            addOption();
        });
    }

    // Edit question buttons
    document.querySelectorAll('.editQuestionBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            const questionData = {
                id: this.dataset.id,
                text: this.dataset.text,
                type: this.dataset.type,
                points: this.dataset.points,
                explanation: this.dataset.explanation,
                options: JSON.parse(this.dataset.options || '[]'),
                action: this.dataset.action
            };
            openModal('edit', questionData);
        });
    });

    function openModal(mode, data = null) {
        isEditMode = mode === 'edit';
        
        // Reset form
        questionForm.reset();
        optionsList.innerHTML = '';
        optionIndex = 0;
        optionsSection.style.display = 'none';

        if (isEditMode && data) {
            // Edit mode
            modalTitle.textContent = 'Edit Question';
            formMethod.value = 'PUT';
            questionForm.action = data.action;

            document.getElementById('question_text').value = data.text;
            document.getElementById('question_type').value = data.type;
            document.getElementById('points').value = data.points;
            document.getElementById('explanation').value = data.explanation || '';

            handleQuestionTypeChange(data.type);

            // Populate options if any
            if (data.options && data.options.length > 0) {
                data.options.forEach(option => {
                    addOption(option);
                });
            }
        } else {
            // Add mode
            modalTitle.textContent = 'Add Question';
            formMethod.value = 'POST';
            questionForm.action = window.location.href;
        }

        questionModal.classList.add('active');
    }

    function closeModal() {
        questionModal.classList.remove('active');
        questionForm.reset();
        optionsList.innerHTML = '';
        optionIndex = 0;
        isEditMode = false;
    }

    function handleQuestionTypeChange(type) {
        if (type === 'multiple_choice' || type === 'true_false') {
            optionsSection.style.display = 'block';
            
            // For true/false, add default options if empty
            if (type === 'true_false' && optionsList.children.length === 0) {
                addOption({ option_text: 'True', is_correct: false });
                addOption({ option_text: 'False', is_correct: false });
            }
        } else {
            optionsSection.style.display = 'none';
            optionsList.innerHTML = '';
            optionIndex = 0;
        }
    }

    function addOption(data = null) {
        const template = document.getElementById('optionTemplate');
        const clone = template.content.cloneNode(true);
        
        // Replace INDEX placeholder with actual index
        const html = clone.querySelector('.option-input-group').outerHTML.replace(/INDEX/g, optionIndex);
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const optionElement = tempDiv.firstElementChild;

        // Set values if editing
        if (data) {
            if (data.id) {
                optionElement.querySelector('.option-id').value = data.id;
            }
            optionElement.querySelector('.option-text').value = data.option_text || '';
            optionElement.querySelector('.option-correct').checked = data.is_correct || false;
        }

        // Add remove handler
        optionElement.querySelector('.remove-option').addEventListener('click', function() {
            optionElement.remove();
        });

        optionsList.appendChild(optionElement);
        optionIndex++;
    }

    // Drag and drop reordering for questions
    const questionsList = document.getElementById('questionsList');
    if (questionsList && typeof Sortable !== 'undefined') {
        new Sortable(questionsList, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: function (evt) {
                const questionIds = Array.from(questionsList.querySelectorAll('.question-item')).map(
                    item => item.dataset.id
                );
                const reorderUrl = questionsList.dataset.reorderUrl;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                fetch(reorderUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ ordered_ids: questionIds })
                }).then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    console.log('Questions reordered successfully');
                }).catch(error => {
                    console.error('Error reordering questions:', error);
                    alert('Failed to save new order.');
                });
            }
        });
    }
});
