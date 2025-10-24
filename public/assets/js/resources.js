// document.addEventListener('DOMContentLoaded', function () {
//     // --- START: MODAL HANDLING REFACTOR ---
//
//     // Get all modal trigger buttons and modals
//     const addResourceBtn = document.getElementById('addResourceBtn');
//     const addModal = document.getElementById('addResourceModal');
//     const editModal = document.getElementById('editResourceModal');
//
//     // A single, reliable function to open a modal
//     function openModal(modalElement) {
//         if (modalElement) {
//             modalElement.classList.add('active');
//         }
//     }
//
//     // A single, reliable function to close a modal
//     function closeModal(modalElement) {
//         if (modalElement) {
//             modalElement.classList.remove('active');
//         }
//     }
//
//     // 1. Open the "Add Resource" Modal
//     // This is a direct and simple event listener for the main button.
//     if (addResourceBtn) {
//         addResourceBtn.addEventListener('click', function() {
//             openModal(addModal);
//         });
//     }
//
//     // 2. Use Event Delegation for all other clicks (Edit, Close, Cancel)
//     // This is more robust because it listens on the whole document.
//     document.body.addEventListener('click', function(event) {
//
//         // --- Logic for Edit Buttons ---
//         const editBtn = event.target.closest('.editResourceBtn');
//         if (editBtn) {
//             const form = document.getElementById('editResourceForm');
//
//             // Populate form fields from the button's data attributes
//             document.getElementById('edit_title').value = editBtn.dataset.title;
//             document.getElementById('edit_description').value = editBtn.dataset.description;
//             document.getElementById('edit_type').value = editBtn.dataset.type;
//             document.getElementById('edit_video_link').value = editBtn.dataset.video_link;
//
//             // Set the form's action URL dynamically
//             if (form) {
//                 form.action = editBtn.dataset.action;
//             }
//
//             // Manually trigger the 'change' event to ensure the correct fields are shown
//             document.getElementById('edit_type').dispatchEvent(new Event('change'));
//
//             openModal(editModal);
//         }
//
//         // --- Logic for Closing Modals ---
//         const closeBtn = event.target.closest('.modal-close, .btn-secondary[data-modal-id]');
//         if (closeBtn) {
//             const modalId = closeBtn.getAttribute('data-modal-id');
//             const modalToClose = document.getElementById(modalId);
//             closeModal(modalToClose);
//         }
//     });
//
//     // --- END: MODAL HANDLING REFACTOR ---
//
//
//     // START: Logic for Type-specific Fields (File vs. Video)
//     function handleResourceTypeChange(modalType) {
//         const typeSelect = document.getElementById(`${modalType}_type`);
//         if (!typeSelect) return;
//
//         const modal = typeSelect.closest('.modal-content');
//         const fileField = modal.querySelector('.file-field-container');
//         const videoLinkField = modal.querySelector('.video-link-field-container');
//         const fileInput = modal.querySelector('.file-input');
//         const videoLinkInput = modal.querySelector('input[name="video_link"]');
//
//         function toggleFields() {
//             if (typeSelect.value === 'video') {
//                 if(fileField) fileField.style.display = 'none';
//                 if(videoLinkField) videoLinkField.style.display = 'block';
//                 if(fileInput) fileInput.removeAttribute('required');
//                 if(videoLinkInput) videoLinkInput.setAttribute('required', 'required');
//             } else {
//                 if(fileField) fileField.style.display = 'block';
//                 if(videoLinkField) videoLinkField.style.display = 'none';
//                 if(fileInput) fileInput.setAttribute('required', 'required');
//                 if(videoLinkInput) videoLinkInput.removeAttribute('required');
//             }
//         }
//         typeSelect.addEventListener('change', toggleFields);
//         toggleFields();
//     }
//     handleResourceTypeChange('add');
//     handleResourceTypeChange('edit');
//     // END: Logic for Type-specific Fields
//
//
//     // START: Drag and Drop File Upload UI
//     function setupDragAndDrop(modalId) {
//         const dropArea = document.querySelector(`#${modalId} .file-drop-area`);
//         if (!dropArea) return;
//
//         const fileInput = dropArea.querySelector('.file-input');
//         const fileNameDisplay = dropArea.querySelector('.file-name-display');
//
//         dropArea.addEventListener('click', () => fileInput.click());
//         fileInput.addEventListener('change', () => {
//             if (fileInput.files.length > 0) {
//                 fileNameDisplay.textContent = `Selected: ${fileInput.files[0].name}`;
//             }
//         });
//
//         ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
//             dropArea.addEventListener(eventName, e => {
//                 e.preventDefault();
//                 e.stopPropagation();
//             });
//         });
//         ['dragenter', 'dragover'].forEach(eventName => {
//             dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'));
//         });
//         ['dragleave', 'drop'].forEach(eventName => {
//             dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'));
//         });
//
//         dropArea.addEventListener('drop', e => {
//             fileInput.files = e.dataTransfer.files;
//             if (fileInput.files.length > 0) {
//                 fileNameDisplay.textContent = `Selected: ${fileInput.files[0].name}`;
//             }
//         });
//     }
//     setupDragAndDrop('addResourceModal');
//     setupDragAndDrop('editResourceModal');
//     // END: Drag and Drop File Upload UI
//
//
//     // START: Re-open modal on validation error
//     const addFormErrors = document.getElementById('add_form_has_errors');
//     if (addFormErrors) {
//         openModal(addModal);
//     }
//
//     const editFormErrors = document.getElementById('edit_form_has_errors');
//     if (editFormErrors) {
//         const resourceId = editFormErrors.value;
//         const editButton = document.querySelector(`.editResourceBtn[data-id='${resourceId}']`);
//         if (editButton) {
//             editButton.click();
//         }
//     }
//     // END: Re-open modal on validation error
// });
//

document.addEventListener('DOMContentLoaded', function () {
    // --- Modal Handling ---
    const addModal = document.getElementById('addResourceModal');
    const editModal = document.getElementById('editResourceModal');
    const addBtn = document.getElementById('addResourceBtn');
    const addModalCloseBtn = document.getElementById('addResourceModalClose');
    const addModalCancelBtn = document.getElementById('addResourceModalCancel');
    const editModalCloseBtn = document.getElementById('editResourceModalClose');
    const editModalCancelBtn = document.getElementById('editResourceModalCancel');
    const resourceGrid = document.querySelector('.resources-grid'); // Changed from '.resources-grid' if your class name changed

    // Function to open a modal
    const openModal = (modal) => {
        if (modal) modal.classList.add('active');
    };

    // Function to close a modal
    const closeModal = (modal) => {
        if (modal) {
            modal.classList.remove('active');
            // Optionally reset forms
            const form = modal.querySelector('form');
            if (form) form.reset();
        }
    };

    // --- Add Collection Modal ---
    if (addBtn && addModal) {
        addBtn.addEventListener('click', () => openModal(addModal));
        addModalCloseBtn?.addEventListener('click', () => closeModal(addModal));
        addModalCancelBtn?.addEventListener('click', () => closeModal(addModal));
        // Close if overlay is clicked
        addModal.addEventListener('click', (event) => {
            if (event.target === addModal) closeModal(addModal);
        });
    }

    // --- Edit Collection Modal ---
    if (resourceGrid && editModal) {
        resourceGrid.addEventListener('click', (event) => {
            const editButton = event.target.closest('.editCollectionBtn'); // Target the new button class
            if (editButton) {
                const form = document.getElementById('editResourceForm');
                const titleInput = document.getElementById('edit_title');
                const descriptionInput = document.getElementById('edit_description');
                const iconInput = document.getElementById('edit_icon_class'); // New input

                // Populate the modal form
                titleInput.value = editButton.dataset.title || '';
                descriptionInput.value = editButton.dataset.description || '';
                iconInput.value = editButton.dataset.icon_class || 'fa-folder-open'; // New data attribute
                form.action = editButton.dataset.action || '#'; // Set form action URL

                openModal(editModal);
            }
        });

        editModalCloseBtn?.addEventListener('click', () => closeModal(editModal));
        editModalCancelBtn?.addEventListener('click', () => closeModal(editModal));
        // Close if overlay is clicked
        editModal.addEventListener('click', (event) => {
            if (event.target === editModal) closeModal(editModal);
        });
    }

    // --- Re-open modal on validation errors ---
    const addErrorInput = addModal?.querySelector('input[name="has_add_errors"]');
    const editErrorInput = editModal?.querySelector('input[name="has_edit_errors"]');

    if (addErrorInput && addErrorInput.value === 'true') {
        openModal(addModal);
    }
    if (editErrorInput && editErrorInput.value === 'true') {
        // Find the correct edit button based on old form data if possible,
        // otherwise just open the modal blank (less ideal).
        // For simplicity, just reopening:
        openModal(editModal);
    }


});

