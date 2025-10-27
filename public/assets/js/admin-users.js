document.addEventListener('DOMContentLoaded', function () {
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


