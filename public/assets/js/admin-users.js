document.addEventListener('DOMContentLoaded', function () {
    // --- Helper Functions ---
    const openModal = (modal) => {
        if (modal) modal.classList.add('active');
    };

    const closeModal = (modal) => {
        if (modal) {
            modal.classList.remove('active');
            const form = modal.querySelector('form');
            if (form) form.reset();
        }
    };

    // --- Assign Coach Modal Logic ---
    const assignCoachBtn = document.getElementById('assignCoachBtn');
    const assignCoachModal = document.getElementById('assignCoachModal');

    if (assignCoachBtn && assignCoachModal) {
        const closeBtn = document.getElementById('closeAssignCoachModal');
        const cancelBtn = document.getElementById('cancelAssignCoach');

        assignCoachBtn.addEventListener('click', () => openModal(assignCoachModal));

        if (closeBtn) closeBtn.addEventListener('click', () => closeModal(assignCoachModal));
        if (cancelBtn) cancelBtn.addEventListener('click', () => closeModal(assignCoachModal));

        assignCoachModal.addEventListener('click', (event) => {
            if (event.target === assignCoachModal) {
                closeModal(assignCoachModal);
            }
        });
    }

    // --- Tab Switching Logic ---
    const tabsContainer = document.querySelector('.profile-tabs');
    if (tabsContainer) {
        const tabContents = document.querySelectorAll('.profile-tab-content');
        const tabButtons = document.querySelectorAll('.profile-tab');

        tabsContainer.addEventListener('click', (event) => {
            const clickedTab = event.target.closest('.profile-tab');
            if (!clickedTab) return;

            const targetTabId = clickedTab.dataset.tab;

            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            clickedTab.classList.add('active');
            const targetContent = document.getElementById(targetTabId);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    }

    // --- Add User Modal Logic ---
    const addUserBtn = document.getElementById('addUserBtn');
    const addUserModal = document.getElementById('addUserModal');

    if (addUserBtn && addUserModal) {
        const addUserCloseBtn = addUserModal.querySelector('.modal-close');
        const addUserCancelBtn = document.getElementById('addUserModalCancel');

        addUserBtn.addEventListener('click', () => openModal(addUserModal));

        if(addUserCloseBtn) addUserCloseBtn.addEventListener('click', () => closeModal(addUserModal));
        if(addUserCancelBtn) addUserCancelBtn.addEventListener('click', () => closeModal(addUserModal));

        addUserModal.addEventListener('click', (event) => {
            if (event.target === addUserModal) closeModal(addUserModal);
        });

        // Re-open modal if validation errors occurred FOR ADD USER
        const errorInput = addUserModal.querySelector('input[name="has_add_errors"]');
        if (errorInput && errorInput.value === 'true') {
            openModal(addUserModal);
        }
    }

    // --- Change Password Modal Logic ---
    const changePasswordModal = document.getElementById('changePasswordModal');
    if (changePasswordModal) {
        const cpCloseBtn = document.getElementById('changePasswordModalClose');
        const cpCancelBtn = document.getElementById('changePasswordModalCancel');
        const cpForm = document.getElementById('changePasswordForm');
        const cpUserName = document.getElementById('cpUserName');
        const cpHiddenUserId = document.getElementById('cp_hidden_user_id');

        // Function to setup and open modal
        const setupAndOpenPasswordModal = (userId, userName, actionUrl) => {
            if(cpUserName) cpUserName.textContent = userName;
            if(cpForm) cpForm.action = actionUrl;
            if(cpHiddenUserId) cpHiddenUserId.value = userId;
            openModal(changePasswordModal);
        };

        // Event Delegation for "Change Password" buttons
        document.body.addEventListener('click', function(event) {
            if (event.target.classList.contains('change-password-btn')) {
                const userId = event.target.dataset.userId;
                const userName = event.target.dataset.userName;
                const actionUrl = event.target.dataset.action;

                // Close dropdown
                const dropdown = event.target.closest('.dropdown-menu');
                if(dropdown) dropdown.classList.remove('show');

                setupAndOpenPasswordModal(userId, userName, actionUrl);
            }
        });

        if(cpCloseBtn) cpCloseBtn.addEventListener('click', () => closeModal(changePasswordModal));
        if(cpCancelBtn) cpCancelBtn.addEventListener('click', () => closeModal(changePasswordModal));
        changePasswordModal.addEventListener('click', (event) => {
            if (event.target === changePasswordModal) closeModal(changePasswordModal);
        });

        // START: Re-open password modal on error
        // We check if there's a hidden input indicating which user failed
        const passwordErrorUser = document.getElementById('password_error_user_id');
        if (passwordErrorUser && passwordErrorUser.value) {
            const failedUserId = passwordErrorUser.value;
            // Find the button corresponding to this user to get the data
            const triggerBtn = document.querySelector(`.change-password-btn[data-user-id="${failedUserId}"]`);
            if (triggerBtn) {
                // Simulate a click or call setup directly
                setupAndOpenPasswordModal(
                    triggerBtn.dataset.userId,
                    triggerBtn.dataset.userName,
                    triggerBtn.dataset.action
                );
            }
        }
        // END: Re-open password modal on error
    }
});
