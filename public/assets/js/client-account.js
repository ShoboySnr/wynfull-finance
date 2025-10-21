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
});
