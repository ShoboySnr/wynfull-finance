document.addEventListener('DOMContentLoaded', function () {
    // --- START: MODAL HANDLING REFACTOR ---

    // Get all modal trigger buttons and modals
    const addResourceBtn = document.getElementById('addResourceBtn');
    const addModal = document.getElementById('addResourceModal');
    const editModal = document.getElementById('editResourceModal');

    // A single, reliable function to open a modal
    function openModal(modalElement) {
        if (modalElement) {
            modalElement.classList.add('active');
        }
    }

    // A single, reliable function to close a modal
    function closeModal(modalElement) {
        if (modalElement) {
            modalElement.classList.remove('active');
        }
    }

    // 1. Open the "Add Resource" Modal
    // This is a direct and simple event listener for the main button.
    if (addResourceBtn) {
        addResourceBtn.addEventListener('click', function() {
            openModal(addModal);
        });
    }

    // 2. Use Event Delegation for all other clicks (Edit, Close, Cancel)
    // This is more robust because it listens on the whole document.
    document.body.addEventListener('click', function(event) {

        // --- Logic for Edit Buttons ---
        const editBtn = event.target.closest('.editResourceBtn');
        if (editBtn) {
            const form = document.getElementById('editResourceForm');

            // Populate form fields from the button's data attributes
            document.getElementById('edit_title').value = editBtn.dataset.title;
            document.getElementById('edit_description').value = editBtn.dataset.description;
            document.getElementById('edit_type').value = editBtn.dataset.type;
            document.getElementById('edit_video_link').value = editBtn.dataset.video_link;

            // Set the form's action URL dynamically
            if (form) {
                form.action = editBtn.dataset.action;
            }

            // Manually trigger the 'change' event to ensure the correct fields are shown
            document.getElementById('edit_type').dispatchEvent(new Event('change'));

            openModal(editModal);
        }

        // --- Logic for Closing Modals ---
        const closeBtn = event.target.closest('.modal-close, .btn-secondary[data-modal-id]');
        if (closeBtn) {
            const modalId = closeBtn.getAttribute('data-modal-id');
            const modalToClose = document.getElementById(modalId);
            closeModal(modalToClose);
        }
    });

    // --- END: MODAL HANDLING REFACTOR ---


    // START: Logic for Type-specific Fields (File vs. Video)
    function handleResourceTypeChange(modalType) {
        const typeSelect = document.getElementById(`${modalType}_type`);
        if (!typeSelect) return;

        const modal = typeSelect.closest('.modal-content');
        const fileField = modal.querySelector('.file-field-container');
        const videoLinkField = modal.querySelector('.video-link-field-container');
        const fileInput = modal.querySelector('.file-input');
        const videoLinkInput = modal.querySelector('input[name="video_link"]');

        function toggleFields() {
            if (typeSelect.value === 'video') {
                if(fileField) fileField.style.display = 'none';
                if(videoLinkField) videoLinkField.style.display = 'block';
                if(fileInput) fileInput.removeAttribute('required');
                if(videoLinkInput) videoLinkInput.setAttribute('required', 'required');
            } else {
                if(fileField) fileField.style.display = 'block';
                if(videoLinkField) videoLinkField.style.display = 'none';
                if(fileInput) fileInput.setAttribute('required', 'required');
                if(videoLinkInput) videoLinkInput.removeAttribute('required');
            }
        }
        typeSelect.addEventListener('change', toggleFields);
        toggleFields();
    }
    handleResourceTypeChange('add');
    handleResourceTypeChange('edit');
    // END: Logic for Type-specific Fields


    // START: Drag and Drop File Upload UI
    function setupDragAndDrop(modalId) {
        const dropArea = document.querySelector(`#${modalId} .file-drop-area`);
        if (!dropArea) return;

        const fileInput = dropArea.querySelector('.file-input');
        const fileNameDisplay = dropArea.querySelector('.file-name-display');

        dropArea.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = `Selected: ${fileInput.files[0].name}`;
            }
        });

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, e => {
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

        dropArea.addEventListener('drop', e => {
            fileInput.files = e.dataTransfer.files;
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = `Selected: ${fileInput.files[0].name}`;
            }
        });
    }
    setupDragAndDrop('addResourceModal');
    setupDragAndDrop('editResourceModal');
    // END: Drag and Drop File Upload UI


    // START: Re-open modal on validation error
    const addFormErrors = document.getElementById('add_form_has_errors');
    if (addFormErrors) {
        openModal(addModal);
    }

    const editFormErrors = document.getElementById('edit_form_has_errors');
    if (editFormErrors) {
        const resourceId = editFormErrors.value;
        const editButton = document.querySelector(`.editResourceBtn[data-id='${resourceId}']`);
        if (editButton) {
            editButton.click();
        }
    }
    // END: Re-open modal on validation error
});

