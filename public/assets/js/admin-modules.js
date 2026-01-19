document.addEventListener('DOMContentLoaded', function () {
    // --- Shared Modal/Form Utility Functions ---
    const openModal = (modal) => {
        if (modal) modal.classList.add('active');
    };
    const closeModal = (modal) => {
        if (modal) {
            modal.classList.remove('active');
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
                const fileDisplay = form.querySelectorAll('.file-name-display');
                fileDisplay.forEach(el => el.textContent = '');

                // Reset field visibility (pass empty type or trigger default)
                const typeSelect = form.querySelector('select[name="type"]');
                if(typeSelect) toggleModuleFields(modal, typeSelect.value);
            }
        }
    };

    // Function to show/hide file vs video link vs video upload fields
    const toggleModuleFields = (modalOrForm, selectedType) => {
        // Containers
        const fileField = modalOrForm.querySelector('.file-field-container');
        const videoSourceField = modalOrForm.querySelector('.video-source-field-container');
        const videoLinkField = modalOrForm.querySelector('.video-link-field-container');
        const videoUploadField = modalOrForm.querySelector('.video-upload-field-container');
        const timeLimitField = modalOrForm.querySelector('.time-limit-field-container');

        // Inputs (to manage required attribute)
        const fileInput = modalOrForm.querySelector('.file-field-container .file-input');
        const videoLinkInput = modalOrForm.querySelector('.video-link-field-container input');
        const videoUploadInput = modalOrForm.querySelector('.video-upload-field-container .file-input');
        const videoSourceRadios = modalOrForm.querySelectorAll('input[name="video_source"]');

        // 1. Reset all first
        if (fileField) fileField.style.display = 'none';
        if (videoSourceField) videoSourceField.style.display = 'none';
        if (videoLinkField) videoLinkField.style.display = 'none';
        if (videoUploadField) videoUploadField.style.display = 'none';
        if (timeLimitField) timeLimitField.style.display = 'none';

        if (fileInput) fileInput.required = false;
        if (videoLinkInput) videoLinkInput.required = false;
        if (videoUploadInput) videoUploadInput.required = false;

        // 2. Apply logic based on type
        if (selectedType === 'video') {
            // Show Source Selection
            if (videoSourceField) videoSourceField.style.display = 'block';

            // Check which source is selected (Link vs Upload)
            let source = 'link'; // Default
            videoSourceRadios.forEach(r => {
                if (r.checked) source = r.value;
            });

            if (source === 'upload') {
                if (videoUploadField) videoUploadField.style.display = 'block';
                if (videoUploadInput) videoUploadInput.required = true;
            } else {
                if (videoLinkField) videoLinkField.style.display = 'block';
                if (videoLinkInput) videoLinkInput.required = true;
            }

            // Clear standard file input value if switching to video
            if(fileInput) fileInput.value = '';

        } else if (selectedType === 'assessment') {
            // Assessment type: Show time limit field, no file upload needed
            if (timeLimitField) timeLimitField.style.display = 'block';
            
        } else if (selectedType) {
            // Template, Word, PDF, Excel -> Show Standard File Upload
            if (fileField) fileField.style.display = 'block';

            // File is required ONLY for Add Modal, optional for Edit
            const isAddModal = modalOrForm.id === 'addModuleModal' || modalOrForm.id === 'addModuleForm';
            if (fileInput && isAddModal) {
                fileInput.required = true;
            }
        }
    };

    // --- Add Module Modal ---
    const addModuleModal = document.getElementById('addModuleModal');
    const addModuleBtn = document.getElementById('addModuleBtn');
    const addModuleModalCloseBtn = document.getElementById('addModuleModalClose');
    const addModuleModalCancelBtn = document.getElementById('addModuleModalCancel');
    const addModuleTypeSelect = document.getElementById('add_module_type');
    const addVideoSourceRadios = document.querySelectorAll('#addModuleModal input[name="video_source"]');

    if (addModuleBtn && addModuleModal) {
        addModuleBtn.addEventListener('click', () => {
            openModal(addModuleModal);
            // Trigger toggle on open to set initial state correctly
            if(addModuleTypeSelect) toggleModuleFields(addModuleModal, addModuleTypeSelect.value);
        });

        addModuleModalCloseBtn?.addEventListener('click', () => closeModal(addModuleModal));
        addModuleModalCancelBtn?.addEventListener('click', () => closeModal(addModuleModal));
        addModuleModal.addEventListener('click', (event) => {
            if (event.target === addModuleModal) closeModal(addModuleModal);
        });

        // Toggle on Type Change
        addModuleTypeSelect?.addEventListener('change', (e) => {
            toggleModuleFields(addModuleModal, e.target.value);
        });

        // Toggle on Video Source Change
        addVideoSourceRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                toggleModuleFields(addModuleModal, addModuleTypeSelect.value);
            });
        });
    }

    // --- Edit Module Modal ---
    const editModuleModal = document.getElementById('editModuleModal');
    const moduleList = document.querySelector('.module-list');
    const editModuleModalCloseBtn = document.getElementById('editModuleModalClose');
    const editModuleModalCancelBtn = document.getElementById('editModuleModalCancel');
    const editModuleTypeSelect = document.getElementById('edit_module_type');

    if (moduleList && editModuleModal) {
        moduleList.addEventListener('click', (event) => {
            const editButton = event.target.closest('.editModuleBtn');
            if (editButton) {
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

                // Handle Video Link population
                if(videoLinkInput) videoLinkInput.value = editButton.dataset.video_link || '';

                // Handle Time Limit population
                if(timeLimitInput) timeLimitInput.value = editButton.dataset.time_limit_minutes || '';

                // Handle File Name display
                if(currentFileNameSpan) currentFileNameSpan.textContent = editButton.dataset.file_name || 'None';

                form.action = editButton.dataset.action || '#';

                // Set initial state of fields
                toggleModuleFields(editModuleModal, typeSelect.value);
                openModal(editModuleModal);
            }
        });

        editModuleModalCloseBtn?.addEventListener('click', () => closeModal(editModuleModal));
        editModuleModalCancelBtn?.addEventListener('click', () => closeModal(editModuleModal));
        editModuleModal.addEventListener('click', (event) => {
            if (event.target === editModuleModal) closeModal(editModuleModal);
        });

        editModuleTypeSelect?.addEventListener('change', (e) => {
            toggleModuleFields(editModuleModal, e.target.value);
        });
    }

    // --- File Input Handling (Drag & Drop, Display Name) ---
    document.querySelectorAll('.file-drop-area').forEach(dropArea => {
        const fileInput = dropArea.querySelector('.file-input');
        const fileNameDisplay = dropArea.querySelector('.file-name-display');
        const browseLink = dropArea.querySelector('.file-browse-link');

        if (!fileInput || !fileNameDisplay) return;

        if(browseLink) {
            browseLink.addEventListener('click', (e) => {
                e.preventDefault();
                fileInput.click();
            });
        }
        dropArea.addEventListener('click', (e) => {
            if (!e.target.classList.contains('file-browse-link')) {
                fileInput.click();
            }
        });

        fileInput.addEventListener('change', (event) => {
            const files = event.target.files;
            if (files.length > 0) {
                fileNameDisplay.textContent = `Selected: ${files[0].name}`;
            } else {
                // If in edit mode, revert to 'None' or empty?
                // Usually keeping empty is fine as the helper text says "Leave blank to keep..."
                fileNameDisplay.textContent = '';
            }
        });

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'));
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'));
        });
        dropArea.addEventListener('drop', (event) => {
            const dt = event.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files;
                const changeEvent = new Event('change');
                fileInput.dispatchEvent(changeEvent);
            }
        });
    });

    // --- Re-open correct modal on validation errors ---
    const addErrorInput = document.querySelector('input[name="has_add_module_errors"]');
    if (addErrorInput && addErrorInput.value === 'true' && addModuleModal) {
        openModal(addModuleModal);
        // Re-run toggle logic to ensure correct fields (Video/Upload) are shown based on old input
        const typeSelect = document.getElementById('add_module_type');
        if (typeSelect) toggleModuleFields(addModuleModal, typeSelect.value);
    }

    const editErrorInput = document.querySelector('input[name="has_edit_module_errors"]');
    if (editErrorInput && editErrorInput.value === 'true') {
        console.warn("Edit validation failed.");
    }

    // --- Drag and Drop Reordering (Admin) ---
    const sortableList = document.getElementById('moduleList');
    if (sortableList && typeof Sortable !== 'undefined') {
        new Sortable(sortableList, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: function (evt) {
                const newIndex = evt.newIndex;
                const oldIndex = evt.oldIndex;
                if (newIndex === oldIndex) return;

                const moduleIds = Array.from(sortableList.querySelectorAll('.module-item')).map(
                    item => item.dataset.id
                );
                const reorderUrl = sortableList.dataset.reorderUrl;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                fetch(reorderUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ ordered_ids: moduleIds })
                }).then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    console.log('Order updated');
                }).catch(error => {
                    console.error('Error updating order:', error);
                    alert('Failed to save new order.');
                });
            }
        });
    }
});
