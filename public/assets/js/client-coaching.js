document.addEventListener('DOMContentLoaded', function () {
    if (typeof coachAvailability === 'undefined') {
        console.error('Coach Availability data is not defined.');
        return;
    }

    const bookSessionModal = document.getElementById('bookSessionModal');
    if (!bookSessionModal) return;

    // START: Refactored Logic for Date/Time Combination
    const bookSessionBtn = document.getElementById('bookSessionBtn');
    const closeBtn = bookSessionModal.querySelector('.modal-close');
    const cancelBtn = bookSessionModal.querySelector('.btn-secondary');
    const dateInput = document.getElementById('session_date');
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
    const requestSessionBtn = document.getElementById('requestSessionBtn');
    const startsAtInput = document.getElementById('starts_at_utc');
    const endsAtInput = document.getElementById('ends_at_utc');

    const openModal = () => bookSessionModal.classList.add('active');
    const closeModal = () => {
        bookSessionModal.classList.remove('active');
        timeSlotsContainer.innerHTML = '<p class="time-slot-placeholder">Please select a date to see available times.</p>';
        dateInput.value = '';
        requestSessionBtn.disabled = true;
    };

    if (bookSessionBtn) bookSessionBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    // When a date is selected, render all available time slots
    dateInput.addEventListener('change', function() {
        if (!this.value) {
            timeSlotsContainer.innerHTML = '<p class="time-slot-placeholder">Please select a date to see available times.</p>';
            return;
        }
        // Always render the same list of times, as they are now independent of the date
        renderTimeSlots(coachAvailability);
    });

    // Render the time slot buttons
    function renderTimeSlots(slots) {
        timeSlotsContainer.innerHTML = '';
        if (slots.length === 0) {
            timeSlotsContainer.innerHTML = '<p class="time-slot-placeholder">No available session times.</p>';
            return;
        }
        const grid = document.createElement('div');
        grid.className = 'time-slots-grid';
        slots.forEach(slot => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'time-slot-btn';
            button.textContent = slot.label;
            // Store the template UTC strings
            button.dataset.startUtc = slot.start_utc;
            button.dataset.endUtc = slot.end_utc;
            grid.appendChild(button);
        });
        timeSlotsContainer.appendChild(grid);
    }

    // Handle time slot selection
    timeSlotsContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('time-slot-btn')) {
            const selectedButton = event.target;
            const selectedDate = dateInput.value;

            // De-select other buttons
            const currentlyActive = timeSlotsContainer.querySelector('.time-slot-btn.active');
            if (currentlyActive) currentlyActive.classList.remove('active');

            // Select the new button
            selectedButton.classList.add('active');

            // Combine selected date with the time from the UTC templates
            const startTimeString = new Date(selectedButton.dataset.startUtc).toUTCString().split(' ')[4];
            const endTimeString = new Date(selectedButton.dataset.endUtc).toUTCString().split(' ')[4];

            const finalStartUtc = new Date(`${selectedDate} ${startTimeString} UTC`).toISOString();
            const finalEndUtc = new Date(`${selectedDate} ${endTimeString} UTC`).toISOString();

            // Populate hidden fields
            startsAtInput.value = finalStartUtc;
            endsAtInput.value = finalEndUtc;

            requestSessionBtn.disabled = false;
        }
    });
    // END: Refactored Logic for Date/Time Combination
});

