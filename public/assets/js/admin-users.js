document.addEventListener('DOMContentLoaded', function () {

    // --- Assign Coach Modal Logic ---
    const assignCoachBtn = document.getElementById('assignCoachBtn');
    const assignCoachModal = document.getElementById('assignCoachModal');
    const closeBtn = document.getElementById('closeAssignCoachModal');
    const cancelBtn = document.getElementById('cancelAssignCoach');

    if (assignCoachBtn) {
        assignCoachBtn.addEventListener('click', () => {
            if (assignCoachModal) assignCoachModal.classList.add('active');
        });
    }

    const closeModal = () => {
        if (assignCoachModal) assignCoachModal.classList.remove('active');
    };

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    if (assignCoachModal) {
        assignCoachModal.addEventListener('click', (event) => {
            if (event.target === assignCoachModal) {
                closeModal();
            }
        });
    }

    // START: New Tab Switching Logic
    const tabsContainer = document.querySelector('.profile-tabs');
    const tabContents = document.querySelectorAll('.profile-tab-content');
    const tabButtons = document.querySelectorAll('.profile-tab');

    if (tabsContainer) {
        tabsContainer.addEventListener('click', (event) => {
            const clickedTab = event.target.closest('.profile-tab');
            if (!clickedTab) return; // Exit if click wasn't on a tab button

            const targetTabId = clickedTab.dataset.tab;

            // 1. Deactivate all tabs and content
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // 2. Activate the clicked tab and its content
            clickedTab.classList.add('active');
            const targetContent = document.getElementById(targetTabId);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    }
    // END: New Tab Switching Logic

    // START: Add User Modal Logic
    const addUserBtn = document.getElementById('addUserBtn');
    const addUserModal = document.getElementById('addUserModal');

    if (addUserBtn && addUserModal) {
        const closeBtn = addUserModal.querySelector('.modal-close');
        const cancelBtn = addUserModal.querySelector('.btn-secondary'); // Assuming second button is cancel
        const form = document.getElementById('addUserForm');

        const openModal = () => addUserModal.classList.add('active');
        const closeModal = () => {
            addUserModal.classList.remove('active');
            if(form) form.reset(); // Clear form on close
        };

        addUserBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Optional: Close modal if background is clicked
        addUserModal.addEventListener('click', function(event) {
            if (event.target === addUserModal) {
                closeModal();
            }
        });

        // Re-open modal if validation errors occurred
        const errorInput = addUserModal.querySelector('input[name="has_add_errors"]');
        if (errorInput && errorInput.value === 'true') {
            openModal();
        }

    }
    // END: Add User Modal Logic
});


