document.addEventListener('DOMContentLoaded', function () {
    // START: Avatar Modal Logic
    const changePhotoBtn = document.getElementById('changePhotoBtn');
    const avatarModal = document.getElementById('avatarModal');
    const avatarModalClose = document.getElementById('avatarModalClose');
    const avatarModalCancel = document.getElementById('avatarModalCancel');

    const avatarDropArea = document.getElementById('avatarDropArea');
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');

    // Function to open the modal
    if (changePhotoBtn) {
        changePhotoBtn.addEventListener('click', () => {
            avatarModal.classList.add('active');
        });
    }

    // Function to close the modal
    function closeAvatarModal() {
        avatarModal.classList.remove('active');
    }

    if (avatarModalClose) avatarModalClose.addEventListener('click', closeAvatarModal);
    if (avatarModalCancel) avatarModalCancel.addEventListener('click', closeAvatarModal);

    // --- Image Preview and Drag-and-Drop ---
    if (avatarDropArea) {
        // Trigger file input click when the area is clicked
        avatarDropArea.addEventListener('click', () => avatarInput.click());

        // Handle file selection
        avatarInput.addEventListener('change', (event) => {
            const files = event.target.files;
            if (files.length > 0) {
                previewFile(files[0]);
            }
        });

        // Add drag-and-drop event listeners
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            avatarDropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area when item is dragged over it
        ['dragenter', 'dragover'].forEach(eventName => {
            avatarDropArea.addEventListener(eventName, () => avatarDropArea.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            avatarDropArea.addEventListener(eventName, () => avatarDropArea.classList.remove('dragover'), false);
        });

        // Handle dropped files
        avatarDropArea.addEventListener('drop', (event) => {
            const dt = event.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                avatarInput.files = files; // Assign dropped files to the input
                previewFile(files[0]);
            }
        }, false);
    }

    // Function to preview the selected file
    function previewFile(file) {
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = function() {
            avatarPreview.src = reader.result;
        }
    }
    // END: Avatar Modal Logic
});
