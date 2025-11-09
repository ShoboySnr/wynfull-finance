document.addEventListener('DOMContentLoaded', function () {
    // START: Change Password Modal Logic
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    const changePasswordModal = document.getElementById('changePasswordModal');

    if (changePasswordBtn && changePasswordModal) {
        const closeBtn = changePasswordModal.querySelector('.modal-close');
        const cancelBtn = changePasswordModal.querySelector('.btn-secondary');
        const form = document.getElementById('changePasswordForm');

        const openModal = () => changePasswordModal.classList.add('active');
        const closeModal = () => {
            changePasswordModal.classList.remove('active');
            form.reset(); // Clear the form fields when closing
        };

        changePasswordBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Optional: Re-open modal if there are password validation errors
        // This checks for a specific error bag if you set one up in your controller
        const passwordErrors = document.querySelector('.alert-danger ul li:first-child');
        if (passwordErrors && passwordErrors.textContent.toLowerCase().includes('password')) {
            openModal();
        }
    }
    // END: Change Password Modal Logic


    // START: 2FA Modal Logic
    const manage2faBtn = document.getElementById('manage2faBtn');
    const twoFactorModal = document.getElementById('twoFactorModal');

    if (manage2faBtn && twoFactorModal) {
        const closeBtn = twoFactorModal.querySelector('.modal-close');

        const openModal = () => twoFactorModal.classList.add('active');
        const closeModal = () => twoFactorModal.classList.remove('active');

        manage2faBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);

        // Optional: Close modal if background is clicked
        twoFactorModal.addEventListener('click', function(event) {
            if (event.target === twoFactorModal) {
                closeModal();
            }
        });
    }
    // END: 2FA Modal Logic


    // START: Notification Preferences Logic
    const emailToggle = document.getElementById('emailNotificationsToggle');
    const goalToggle = document.getElementById('goalRemindersToggle');
    const statusMessage = document.getElementById('notificationStatus');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // Ensure CSRF meta tag exists in layout

    async function updateNotificationPreference(toggleElement) {
        if (!toggleElement) return;

        const emailEnabled = emailToggle ? emailToggle.checked : false;
        const goalEnabled = goalToggle ? goalToggle.checked : false;
        const payload = {
            email_notifications_enabled: emailEnabled,
            goal_reminders_enabled: goalEnabled,
        };

        // Provide immediate visual feedback (optional)
        if(statusMessage) statusMessage.textContent = 'Saving...';
        toggleElement.disabled = true; // Prevent rapid toggling

        try {
            const response = await fetch('/settings/notifications', { // Using hardcoded URL, consider using named routes via Ziggy or passing from Blade
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to update preferences.');
            }

            // Success feedback
            if(statusMessage) statusMessage.textContent = 'Preferences saved!';
            setTimeout(() => { if(statusMessage) statusMessage.textContent = ''; }, 2000); // Clear message after 2 seconds

        } catch (error) {
            console.error('Error updating notification preferences:', error);
            // Error feedback and revert toggle state
            if(statusMessage) statusMessage.textContent = `Error: ${error.message}`;
            toggleElement.checked = !toggleElement.checked; // Revert visually
            setTimeout(() => { if(statusMessage) statusMessage.textContent = ''; }, 4000); // Clear error after 4 seconds

        } finally {
            toggleElement.disabled = false; // Re-enable toggle
        }
    }

    if (emailToggle) {
        emailToggle.addEventListener('change', () => updateNotificationPreference(emailToggle));
    }
    if (goalToggle) {
        goalToggle.addEventListener('change', () => updateNotificationPreference(goalToggle));
    }
    // END: Notification Preferences Logic

    // START: Avatar Upload Modal Logic
    const changePhotoBtn = document.getElementById('changePhotoBtn');
    const avatarModal = document.getElementById('avatarModal');

    if (changePhotoBtn && avatarModal) {
        const avatarModalClose = document.getElementById('avatarModalClose');
        const avatarModalCancel = document.getElementById('avatarModalCancel');
        const avatarDropArea = document.getElementById('avatarDropArea');
        const avatarInput = document.getElementById('avatarInput');
        const avatarPreview = document.getElementById('avatarPreview');

        // Function to open the modal
        changePhotoBtn.addEventListener('click', () => {
            avatarModal.classList.add('active');
        });

        // Function to close the modal
        function closeAvatarModal() {
            avatarModal.classList.remove('active');
            // Reset preview and file input
            if (avatarPreview) avatarPreview.src = document.querySelector('.profile-avatar').src; // Reset to main avatar
            if (avatarInput) avatarInput.value = ''; // Clear selected file
        }

        if (avatarModalClose) avatarModalClose.addEventListener('click', closeAvatarModal);
        if (avatarModalCancel) avatarModalCancel.addEventListener('click', closeAvatarModal);

        // --- Image Preview and Drag-and-Drop ---
        if (avatarDropArea && avatarInput && avatarPreview) {

            // Function to trigger file input
            const triggerFileInput = () => avatarInput.click();

            // Click on drop area or browse link
            avatarDropArea.addEventListener('click', (e) => {
                if (e.target.classList.contains('file-browse-link') || e.target === avatarDropArea || e.target.tagName === 'P' || e.target.tagName === 'I') {
                    triggerFileInput();
                }
            });

            // Handle file selection
            avatarInput.addEventListener('change', (event) => {
                const files = event.target.files;
                if (files.length > 0) {
                    previewFile(files[0]);
                }
            });

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                avatarDropArea.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false); // Prevent browser from opening file
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            // Highlight drop area
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
            // Ensure it's an image
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file (e.g., JPG, PNG).');
                avatarInput.value = ''; // Clear invalid file
                return;
            }

            let reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onloadend = function() {
                avatarPreview.src = reader.result;
            }
        }

        // Re-open avatar modal if validation errors occurred
        const avatarForm = avatarModal.querySelector('form');
        if (avatarForm && avatarForm.querySelector('.alert-danger')) {
            openModal();
        }
    }
    // END: Avatar Upload Modal Logic
});
