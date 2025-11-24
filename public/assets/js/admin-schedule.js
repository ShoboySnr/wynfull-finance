document.addEventListener('DOMContentLoaded', function () {
    // --- DOM Elements ---
    const calendarTitle = document.getElementById('calendarTitle');
    const calendarGrid = document.getElementById('calendarGrid');
    const calendarWeekDays = document.getElementById('calendarWeekDays');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const viewBtns = document.querySelectorAll('.schedule-nav .nav-btn');

    // Modal Elements
    const newSessionBtn = document.getElementById('newSessionBtn');
    const newSessionModal = document.getElementById('newSessionModal');
    const closeNewSessionModal = document.getElementById('closeNewSessionModal');
    const cancelNewSession = document.getElementById('cancelNewSession');
    const newSessionForm = document.getElementById('newSessionForm');

    // --- State ---
    let currentDate = new Date();
    let currentView = 'week';
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    // --- API Functions ---
    async function fetchSessions(startDate, endDate) {
        const start = startDate.toISOString().split('T')[0];
        const end = endDate.toISOString().split('T')[0];

        try {
            const response = await fetch(`/admin/meetings/feed?start=${start}&end=${end}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error('Failed to fetch sessions:', error);
            return [];
        }
    }

    // --- Calendar Logic (Reused structure) ---
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
            const dateString = new Date(currentDate.getFullYear(), currentDate.getMonth(), day).toISOString().split('T')[0];
            calendarGrid.innerHTML += `<div class="calendar-day" data-date="${dateString}"><div class="day-number">${day}</div><div class="day-sessions"></div></div>`;
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
            calendarGrid.innerHTML += `<div class="calendar-day" data-date="${dateString}"><div class="day-number">${day.getDate()}</div><div class="day-sessions"></div></div>`;
        }
    }

    function renderDayView() {
        calendarGrid.classList.add('day-view');
        calendarTitle.textContent = currentDate.toLocaleDateString();
        calendarWeekDays.innerHTML = `<div class="day-name">${dayNames[currentDate.getDay()]}</div>`;
        const dateString = currentDate.toISOString().split('T')[0];
        calendarGrid.innerHTML += `<div class="calendar-day" data-date="${dateString}"><div class="day-sessions"></div></div>`;
    }

    function placeSessions(sessions) {
        sessions.forEach(session => {
            const sessionDate = session.date.split('T')[0];
            const dayEl = document.querySelector(`.calendar-day[data-date="${sessionDate}"]`);
            if (dayEl) {
                dayEl.querySelector('.day-sessions').innerHTML += `
                    <div class="session-block admin-session">
                        <div class="session-time">${session.time}</div>
                        <div class="session-client">${session.client}</div>
                        <div class="session-type">${session.type}</div>
                    </div>`;
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

    // --- Modal Logic ---
    const openModal = () => newSessionModal.classList.add('active');
    const closeModal = () => newSessionModal.classList.remove('active');

    newSessionBtn.addEventListener('click', openModal);
    closeNewSessionModal.addEventListener('click', closeModal);
    cancelNewSession.addEventListener('click', closeModal);
    newSessionModal.addEventListener('click', e => { if (e.target === newSessionModal) closeModal(); });

    newSessionForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const action = this.getAttribute('action');

        try {
            const response = await fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json',
                }
            });
            if (!response.ok) {
                const data = await response.json();
                throw new Error(data.message || 'Failed to schedule');
            }

            closeModal();
            this.reset();
            renderCalendar();
        } catch (error) {
            alert(error.message);
        }
    });

    renderCalendar();
});
