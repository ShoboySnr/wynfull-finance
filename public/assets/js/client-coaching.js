document.addEventListener('DOMContentLoaded', function () {
    // START: Book a Session Modal Logic
    const bookSessionBtn = document.getElementById('bookSessionBtn');
    const bookSessionModal = document.getElementById('bookSessionModal');

    if (bookSessionModal) {
        const closeBtn = bookSessionModal.querySelector('.modal-close');
        const cancelBtn = bookSessionModal.querySelector('.btn-secondary');

        const openModal = () => bookSessionModal.classList.add('active');
        const closeModal = () => bookSessionModal.classList.remove('active');

        if (bookSessionBtn) {
            bookSessionBtn.addEventListener('click', openModal);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeModal);
        }

        // Optional: Close modal if background is clicked
        bookSessionModal.addEventListener('click', function(event) {
            if (event.target === bookSessionModal) {
                closeModal();
            }
        });
    }
    // END: Book a Session Modal Logic
});
