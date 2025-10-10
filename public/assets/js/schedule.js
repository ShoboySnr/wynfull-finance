document.addEventListener('DOMContentLoaded', function () {
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

    let currentDate = new Date();
    let currentView = 'week'; // 'month', 'week', or 'day'

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];

    const sessions = [
        { date: '2025-10-13', time: '10:00 AM', client: 'John Doe', type: 'Financial Review' },
        { date: '2025-10-15', time: '02:00 PM', client: 'Jane Smith', type: 'Debt Strategy' },
        { date: '2025-10-15', time: '04:30 PM', client: 'David Kim', type: 'Business Planning' },
        { date: '2025-10-22', time: '11:00 AM', client: 'Lisa Wang', type: 'Home Buying' },
    ];

    function renderCalendar() {
        calendarGrid.innerHTML = '';
        calendarWeekDays.innerHTML = '';
        calendarGrid.className = 'calendar-grid';

        switch (currentView) {
            case 'month':
                renderMonthView();
                break;
            case 'week':
                renderWeekView();
                break;
            case 'day':
                renderDayView();
                break;
        }
        placeSessions();
    }

    function renderMonthView() {
        calendarGrid.classList.add('month-view');
        calendarTitle.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

        // Render day names header
        dayNames.forEach(day => {
            calendarWeekDays.innerHTML += `<div class="day-name">${day}</div>`;
        });

        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

        // Add padding for days before the 1st of the month
        for (let i = 0; i < firstDay.getDay(); i++) {
            calendarGrid.innerHTML += `<div class="calendar-day other-month"></div>`;
        }

        for (let day = 1; day <= lastDay.getDate(); day++) {
            const today = new Date();
            const isToday = day === today.getDate() && currentDate.getMonth() === today.getMonth() && currentDate.getFullYear() === today.getFullYear();
            calendarGrid.innerHTML += `
                <div class="calendar-day ${isToday ? 'today' : ''}">
                    <div class="day-number">${day}</div>
                    <div class="day-sessions"></div>
                </div>`;
        }
    }

    function renderWeekView() {
        calendarGrid.classList.add('week-view');
        const startOfWeek = new Date(currentDate);
        startOfWeek.setDate(currentDate.getDate() - currentDate.getDay());
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);

        calendarTitle.textContent = `${startOfWeek.toLocaleDateString()} - ${endOfWeek.toLocaleDateString()}`;

        for (let i = 0; i < 7; i++) {
            const day = new Date(startOfWeek);
            day.setDate(startOfWeek.getDate() + i);

            calendarWeekDays.innerHTML += `<div class="day-name">${dayNames[day.getDay()]}</div>`;

            const today = new Date();
            const isToday = day.toDateString() === today.toDateString();

            calendarGrid.innerHTML += `
                <div class="calendar-day ${isToday ? 'today' : ''}">
                    <div class="day-number">${day.getDate()}</div>
                    <div class="day-sessions"></div>
                </div>`;
        }
    }

    function renderDayView() {
        calendarGrid.classList.add('day-view');
        calendarTitle.textContent = currentDate.toLocaleDateString();
        calendarWeekDays.innerHTML = `<div class="day-name">${dayNames[currentDate.getDay()]}</div>`;

        calendarGrid.innerHTML = `
            <div class="calendar-day">
                <div class="day-number">${currentDate.getDate()}</div>
                <div class="day-sessions"></div>
            </div>`;
    }

    function placeSessions() {
        const allDaySessionContainers = document.querySelectorAll('.calendar-day');

        allDaySessionContainers.forEach((dayContainer, index) => {
            const dayNumberEl = dayContainer.querySelector('.day-number');
            if (!dayNumberEl) return;

            const dayNumber = parseInt(dayNumberEl.textContent, 10);
            const sessionsForThisDay = sessions.filter(s => {
                const sessionDate = new Date(s.date);
                return sessionDate.getDate() + 1 === dayNumber &&
                    sessionDate.getMonth() === currentDate.getMonth() &&
                    sessionDate.getFullYear() === currentDate.getFullYear();
            });

            const sessionsContainer = dayContainer.querySelector('.day-sessions');
            if (sessionsContainer) {
                sessionsForThisDay.forEach(session => {
                    sessionsContainer.innerHTML += `
                        <div class="session-block">
                            <div class="session-time">${session.time}</div>
                            <div class="session-client">${session.client}</div>
                            <div class="session-type">${session.type}</div>
                        </div>
                    `;
                });
            }
        });
    }

    // Event Listeners
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
    if (newSessionBtn) {
        newSessionBtn.addEventListener('click', () => newSessionModal.classList.add('active'));
    }
    const closeModal = () => newSessionModal.classList.remove('active');
    if(closeNewSessionModal) closeNewSessionModal.addEventListener('click', closeModal);
    if(cancelNewSession) cancelNewSession.addEventListener('click', closeModal);
    if(newSessionModal){
        newSessionModal.addEventListener('click', (e) => {
            if (e.target === newSessionModal) closeModal();
        });
    }

    // Initial Render
    renderCalendar();
});
