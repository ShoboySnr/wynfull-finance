document.addEventListener('DOMContentLoaded', function () {
    // --- DOM Element Selection ---
    const calendarTitle = document.getElementById('calendarTitle');
    const calendarGrid = document.getElementById('calendarGrid');
    const calendarWeekDays = document.getElementById('calendarWeekDays');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const viewBtns = document.querySelectorAll('.schedule-nav .nav-btn');

    // Modal elements
    const newSessionBtn = document.getElementById('newSessionBtn');
    const newSessionModal = document.getElementById('newSessionModal');
    const closeNewSessionModal = document.getElementById('closeNewSessionModal');
    const cancelNewSession = document.getElementById('cancelNewSession');
    const newSessionForm = document.getElementById('newSessionForm');

    // --- State and Constants ---
    let currentDate = new Date();
    let currentView = 'week'; // Default view
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    // --- API Functions ---

    /**
     * Fetches session data from the backend for a given date range.
     * @param {Date} startDate - The start of the date range.
     * @param {Date} endDate - The end of the date range.
     * @returns {Promise<Array>} A promise that resolves to an array of session objects.
     */
    async function fetchSessions(startDate, endDate) {
        const start = startDate.toISOString().split('T')[0];
        const end = endDate.toISOString().split('T')[0];

        try {
            // IMPORTANT: Replace '/api/sessions' with your actual API endpoint.
            const response = await fetch(`{{ route('coach.sessions.feed') }}?start=${start}&end=${end}`, );
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Failed to fetch sessions:', error);
            alert('Could not load schedule. Please check the console for details.');
            return []; // Return an empty array to prevent the app from crashing.
        }
    }

    // --- Calendar Rendering ---

    /**
     * Main function to render the calendar grid and fetch the corresponding sessions.
     */
    async function renderCalendar() {
        calendarGrid.innerHTML = '';
        calendarWeekDays.innerHTML = '';
        calendarGrid.className = 'calendar-grid';

        let startDate, endDate;

        switch (currentView) {
            case 'month':
                startDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                endDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
                renderMonthView();
                break;
            case 'week':
                startDate = new Date(currentDate);
                startDate.setDate(currentDate.getDate() - currentDate.getDay());
                endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + 6);
                renderWeekView(startDate);
                break;
            case 'day':
                startDate = endDate = new Date(currentDate);
                renderDayView();
                break;
        }

        const sessions = await fetchSessions(startDate, endDate);
        placeSessions(sessions);
    }

    function renderMonthView() {
        calendarGrid.classList.add('month-view');
        calendarTitle.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
        dayNames.forEach(day => calendarWeekDays.innerHTML += `<div class="day-name">${day}</div>`);

        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

        for (let i = 0; i < firstDay.getDay(); i++) {
            calendarGrid.innerHTML += `<div class="calendar-day other-month"></div>`;
        }

        for (let day = 1; day <= lastDay.getDate(); day++) {
            const dayDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
            const dateString = dayDate.toISOString().split('T')[0];
            const isToday = new Date().toDateString() === dayDate.toDateString();
            calendarGrid.innerHTML += `
                <div class="calendar-day ${isToday ? 'today' : ''}" data-date="${dateString}">
                    <div class="day-number">${day}</div>
                    <div class="day-sessions"></div>
                </div>`;
        }
    }

    function renderWeekView(startOfWeek) {
        calendarGrid.classList.add('week-view');
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);
        calendarTitle.textContent = `${startOfWeek.toLocaleDateString()} - ${endOfWeek.toLocaleDateString()}`;

        for (let i = 0; i < 7; i++) {
            const day = new Date(startOfWeek);
            day.setDate(startOfWeek.getDate() + i);
            calendarWeekDays.innerHTML += `<div class="day-name">${dayNames[day.getDay()]}</div>`;

            const dateString = day.toISOString().split('T')[0];
            const isToday = new Date().toDateString() === day.toDateString();
            calendarGrid.innerHTML += `
                <div class="calendar-day ${isToday ? 'today' : ''}" data-date="${dateString}">
                    <div class="day-number">${day.getDate()}</div>
                    <div class="day-sessions"></div>
                </div>`;
        }
    }

    function renderDayView() {
        calendarGrid.classList.add('day-view');
        calendarTitle.textContent = currentDate.toLocaleDateString();
        calendarWeekDays.innerHTML = `<div class="day-name">${dayNames[currentDate.getDay()]}</div>`;

        const dateString = currentDate.toISOString().split('T')[0];
        const isToday = new Date().toDateString() === currentDate.toDateString();
        calendarGrid.innerHTML = `
            <div class="calendar-day ${isToday ? 'today' : ''}" data-date="${dateString}">
                <div class="day-sessions"></div>
            </div>`;
    }

    /**
     * Places fetched session blocks onto the correct days in the calendar.
     * @param {Array} sessions - An array of session objects from the API.
     */
    function placeSessions(sessions) {
        sessions.forEach(session => {
            const sessionDate = session.date.split('T')[0]; // Expects YYYY-MM-DD
            const dayEl = document.querySelector(`.calendar-day[data-date="${sessionDate}"]`);

            if (dayEl) {
                const sessionsContainer = dayEl.querySelector('.day-sessions');
                if (sessionsContainer) {
                    sessionsContainer.innerHTML += `
                        <div class="session-block" title="${session.type}">
                            <div class="session-time">${session.time}</div>
                            <div class="session-client">${session.client_name}</div>
                        </div>
                    `;
                }
            }
        });
    }

    // --- Event Listeners ---

    prevBtn.addEventListener('click', () => {
        if (currentView === 'month') currentDate.setMonth(currentDate.getMonth() - 1);
        else if (currentView === 'week') currentDate.setDate(currentDate.getDate() - 7);
        else currentDate.setDate(currentDate.getDate() - 1);
        renderCalendar();
    });

    nextBtn.addEventListener('click', () => {
        if (currentView === 'month') currentDate.setMonth(currentDate.getMonth() + 1);
        else if (currentView === 'week') currentDate.setDate(currentDate.getDate() + 7);
        else currentDate.setDate(currentDate.getDate() + 1);
        renderCalendar();
    });

    viewBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            viewBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentView = btn.dataset.view;
            renderCalendar();
        });
    });

    // Modal Logic
    const openModal = () => newSessionModal.classList.add('active');
    const closeModal = () => newSessionModal.classList.remove('active');

    newSessionBtn.addEventListener('click', openModal);
    closeNewSessionModal.addEventListener('click', closeModal);
    cancelNewSession.addEventListener('click', closeModal);
    newSessionModal.addEventListener('click', e => {
        if (e.target === newSessionModal) closeModal();
    });

    // New Session Form Submission
    newSessionForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const action = this.getAttribute('action');

        try {
            const response = await fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) {
                const errorData = await response.json();

                alert(`Error: ${errorData.message || 'Could not create session.'}`);
                return;
            }

            closeModal();
            this.reset();
            await renderCalendar(); // Refresh the calendar to show the new event

        } catch (error) {
            console.error('Error submitting form:', error);
            alert('A network error occurred. Please try again.');
        }
    });

    // --- Initial Load ---
    renderCalendar();
});

