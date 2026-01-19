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
                // Reset file display specifically
                const fileDisplay = form.querySelector('.file-name-display');
                if (fileDisplay) fileDisplay.textContent = '';
                toggleModuleFields(modal, ''); // Reset field visibility
            }
        }
    };

    // Function to show/hide file vs video link fields based on selected type
    const toggleModuleFields = (modalOrForm, selectedType) => {
        const fileField = modalOrForm.querySelector('.file-field-container');
        const videoField = modalOrForm.querySelector('.video-link-field-container');
        const timeLimitField = modalOrForm.querySelector('.time-limit-field-container');
        const fileInput = modalOrForm.querySelector('.file-input');
        const videoInput = modalOrForm.querySelector('.video-link-field-container input');

        // Handle time limit field for assessments
        if (timeLimitField) {
            timeLimitField.style.display = selectedType === 'assessment' ? 'block' : 'none';
        }

        if (fileField && videoField && fileInput && videoInput) {
            if (selectedType === 'video') {
                videoField.style.display = 'block';
                fileField.style.display = 'none';
                videoInput.required = true; // Make video link required
                fileInput.required = false; // Make file input not required
                fileInput.value = ''; // Clear file input if switching to video
                const fileDisplay = fileField.querySelector('.file-name-display');
                if(fileDisplay) fileDisplay.textContent = '';
            } else if (selectedType === 'assessment') {
                // Assessments don't need file or video fields
                videoField.style.display = 'none';
                fileField.style.display = 'none';
                videoInput.required = false;
                fileInput.required = false;
            } else if (selectedType) { // Any other type requires a file
                videoField.style.display = 'none';
                fileField.style.display = 'block';
                videoInput.required = false;
                videoInput.value = ''; // Clear video input if switching to file
                // Make file input required only for ADD modal initially
                if(modalOrForm.closest('#addModuleModal')) {
                    fileInput.required = true;
                } else {
                    // In EDIT modal, file is optional (only required if replacing)
                    fileInput.required = false;
                }

            } else { // No type selected or invalid
                videoField.style.display = 'none';
                fileField.style.display = 'none';
                videoInput.required = false;
                fileInput.required = false;
            }
        }
    };

    // --- Add Module Modal ---
    const addModuleModal = document.getElementById('addModuleModal');
    const addModuleBtn = document.getElementById('addModuleBtn'); // Defined here
    const addModuleModalCloseBtn = document.getElementById('addModuleModalClose');
    const addModuleModalCancelBtn = document.getElementById('addModuleModalCancel');
    const addModuleTypeSelect = document.getElementById('add_module_type');

    if (addModuleBtn && addModuleModal) {
        // START: Corrected variable name
        addModuleBtn.addEventListener('click', () => openModal(addModuleModal)); // Changed addBtn to addModuleBtn
        // END: Corrected variable name
        addModuleModalCloseBtn?.addEventListener('click', () => closeModal(addModuleModal));
        addModuleModalCancelBtn?.addEventListener('click', () => closeModal(addModuleModal));
        addModuleModal.addEventListener('click', (event) => {
            if (event.target === addModuleModal) closeModal(addModuleModal);
        });

        // Toggle fields on type change in Add Modal
        addModuleTypeSelect?.addEventListener('change', (e) => {
            toggleModuleFields(addModuleModal, e.target.value);
        });
    }

    // --- Edit Module Modal ---
    const editModuleModal = document.getElementById('editModuleModal');
    const moduleList = document.querySelector('.module-list'); // Container for modules
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

                // Populate common fields
                titleInput.value = editButton.dataset.title || '';
                descriptionInput.value = editButton.dataset.description || '';
                typeSelect.value = editButton.dataset.type || '';
                videoLinkInput.value = editButton.dataset.video_link || '';
                if (timeLimitInput) {
                    timeLimitInput.value = editButton.dataset.time_limit_minutes || '';
                }

                // Update current file name display
                currentFileNameSpan.textContent = editButton.dataset.file_name || 'None';

                // Set form action dynamically
                form.action = editButton.dataset.action || '#';

                // Toggle fields based on the CURRENT type
                toggleModuleFields(editModuleModal, typeSelect.value);

                openModal(editModuleModal);
            }
        });

        editModuleModalCloseBtn?.addEventListener('click', () => closeModal(editModuleModal));
        editModuleModalCancelBtn?.addEventListener('click', () => closeModal(editModuleModal));
        editModuleModal.addEventListener('click', (event) => {
            if (event.target === editModuleModal) closeModal(editModuleModal);
        });

        // Toggle fields on type change in Edit Modal
        editModuleTypeSelect?.addEventListener('change', (e) => {
            toggleModuleFields(editModuleModal, e.target.value);
        });
    }

    // --- File Input Handling (Drag & Drop, Display Name) ---
    // Apply to both Add and Edit modals
    document.querySelectorAll('.file-drop-area').forEach(dropArea => {
        const fileInput = dropArea.querySelector('.file-input');
        const fileNameDisplay = dropArea.querySelector('.file-name-display');
        const browseLink = dropArea.querySelector('.file-browse-link');

        if (!fileInput || !fileNameDisplay) return;

        // Trigger file input click
        if(browseLink) {
            browseLink.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent potential link navigation
                fileInput.click();
            });
        }
        // Also allow clicking the general area
        dropArea.addEventListener('click', (e) => {
            // Don't trigger if clicking the link itself again
            if (!e.target.classList.contains('file-browse-link')) {
                fileInput.click();
            }
        });


        // Handle file selection (from browse or drop)
        fileInput.addEventListener('change', (event) => {
            const files = event.target.files;
            if (files.length > 0) {
                fileNameDisplay.textContent = `Selected: ${files[0].name}`;
            } else {
                // Reset if no file selected (e.g., user cancels browse)
                const currentFileSpan = dropArea.closest('form').querySelector('#edit_current_file_name'); // Check if in edit modal
                if (currentFileSpan) {
                    fileNameDisplay.textContent = ''; // In edit, just clear selection
                } else {
                    fileNameDisplay.textContent = ''; // In add, clear selection
                }
            }
        });

        // Drag and Drop listeners
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false); // Prevent browser opening file
        });
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
        });

        dropArea.addEventListener('drop', (event) => {
            const dt = event.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInput.files = files; // Assign dropped files to the input
                // Manually trigger the 'change' event to update display
                const changeEvent = new Event('change');
                fileInput.dispatchEvent(changeEvent);
            }
        }, false);
    });


    // --- Re-open correct modal on validation errors ---
    const addErrorInput = document.querySelector('input[name="has_add_module_errors"]');
    const editErrorInput = document.querySelector('input[name="has_edit_module_errors"]');

    if (addErrorInput && addErrorInput.value === 'true') {
        openModal(addModuleModal);
        // Ensure correct fields are shown based on old input
        if (addModuleTypeSelect) {
            toggleModuleFields(addModuleModal, addModuleTypeSelect.value);
        }
    }
    if (editErrorInput && editErrorInput.value === 'true') {

        openModal(editModuleModal);
        console.warn("Edit validation failed. Modal cannot be automatically reopened without knowing which module.");
    }

    // START: Logic to handle opening Edit Collection Modal
    const editCollectionBtn = document.querySelector('.editCollectionBtn'); // Assumes only one on this page
    const editCollectionModal = document.getElementById('editResourceModal'); // The modal included via partial
    const editCollectionModalCloseBtn = document.getElementById('editResourceModalClose');
    const editCollectionModalCancelBtn = document.getElementById('editResourceModalCancel');

    if (editCollectionBtn && editCollectionModal) {
        editCollectionBtn.addEventListener('click', () => {
            const form = document.getElementById('editResourceForm');
            const titleInput = document.getElementById('edit_title');
            const descriptionInput = document.getElementById('edit_description');
            const iconInput = document.getElementById('edit_icon_class');

            // Populate the modal form
            titleInput.value = editCollectionBtn.dataset.title || '';
            descriptionInput.value = editCollectionBtn.dataset.description || '';
            iconInput.value = editCollectionBtn.dataset.icon_class || 'fa-folder-open';
            form.action = editCollectionBtn.dataset.action || '#'; // Set form action URL

            openModal(editCollectionModal);
        });

        editCollectionModalCloseBtn?.addEventListener('click', () => closeModal(editCollectionModal));
        editCollectionModalCancelBtn?.addEventListener('click', () => closeModal(editCollectionModal));
        editCollectionModal.addEventListener('click', (event) => {
            if (event.target === editCollectionModal) closeModal(editCollectionModal);
        });

        // Check for edit collection validation errors
        const editCollectionErrorInput = editCollectionModal?.querySelector('input[name="has_edit_errors"]');
        if (editCollectionErrorInput && editCollectionErrorInput.value === 'true') {
            openModal(editCollectionModal);
        }
    }
    // END: Logic to handle opening Edit Collection Modal

    // START: Drag and Drop Logic
    const sortableList = document.getElementById('moduleList');
    const isSortable = sortableList?.dataset.isSortable === 'true';

    if (sortableList && isSortable && typeof Sortable !== 'undefined') {
        new Sortable(sortableList, {
            animation: 150,
            handle: '.drag-handle', // Restrict drag to the handle icon
            ghostClass: 'sortable-ghost', // Class name for the drop placeholder
            onEnd: function (evt) {
                const itemEl = evt.item;
                const newIndex = evt.newIndex;
                const oldIndex = evt.oldIndex;

                if (newIndex === oldIndex) return; // No change

                // Get all module IDs in the new order
                const moduleIds = Array.from(sortableList.querySelectorAll('.module-item')).map(
                    item => item.dataset.id
                );

                // Send the new order to the backend
                const reorderUrl = sortableList.dataset.reorderUrl;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                fetch(reorderUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ ordered_ids: moduleIds })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        console.log('Order updated successfully');
                        // Optionally show a toast notification
                    })
                    .catch(error => {
                        console.error('Error updating order:', error);
                        alert('Failed to save new order. Please refresh the page.');
                    });
            }
        });
    }
    // END: Drag and Drop Logic
});

