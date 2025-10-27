document.addEventListener('DOMContentLoaded', function () {
    // START: Rejection Modal Logic
    const rejectReasonModal = document.getElementById('rejectReasonModal');
    const adminResourcesGrid = document.querySelector('.admin-resources-grid'); // Container for cards

    if (rejectReasonModal && adminResourcesGrid) {
        const closeBtn = rejectReasonModal.querySelector('.modal-close');
        const cancelBtn = rejectReasonModal.querySelector('#rejectReasonModalCancel');
        const form = document.getElementById('rejectReasonForm');
        const titleSpan = document.getElementById('rejectCollectionTitle');

        const openModal = (actionUrl, collectionTitle) => {
            form.action = actionUrl; // Set the form's submission URL
            titleSpan.textContent = collectionTitle; // Display collection title in modal
            rejectReasonModal.classList.add('active');
        };

        const closeModal = () => {
            rejectReasonModal.classList.remove('active');
            form.reset(); // Clear the reason textarea
            form.action = '#'; // Reset action
        };

        // Event listener for Reject buttons using delegation
        adminResourcesGrid.addEventListener('click', function(event) {
            const rejectButton = event.target.closest('.rejectResourceBtn');
            if (rejectButton) {
                const actionUrl = rejectButton.dataset.action;
                const collectionTitle = rejectButton.dataset.collectionTitle;
                if (actionUrl) {
                    openModal(actionUrl, collectionTitle);
                } else {
                    console.error('Reject button is missing data-action URL.');
                }
            }
        });

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Optional: Close modal if background is clicked
        rejectReasonModal.addEventListener('click', function(event) {
            if (event.target === rejectReasonModal) {
                closeModal();
            }
        });

        // Re-open if validation fails (requires hidden input in Blade if needed)
        const errorInput = rejectReasonModal.querySelector('input[name="has_reject_errors"]'); // Example hidden input
        if (errorInput && errorInput.value === 'true') {
            console.log("Rejection validation failed.");
        }
    }
    // END: Rejection Modal Logic
});
