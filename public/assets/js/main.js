// Main JavaScript file for Wynfull Finance
// Theme functionality that works across all layouts

document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme on page load
    initializeTheme();

    // Initialize other common functionality
    initializeUserDropdowns();
    initializeNotifications();
    initializeInfoPanel();
    initializeLogoutConfirmation();
    initializeMobileMenu();
});

// Theme functionality
function initializeTheme() {
    // Get system preference
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const systemTheme = systemPrefersDark ? 'dark' : 'light';

    // Load saved theme or use system preference as default
    const savedTheme = localStorage.getItem('wynfullTheme') || systemTheme;
    setTheme(savedTheme);

    // Listen for system theme changes (optional - updates when user changes system preference)
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.addEventListener('change', function(e) {
        // Only update if user hasn't manually set a preference
        if (!localStorage.getItem('wynfullTheme')) {
            const newSystemTheme = e.matches ? 'dark' : 'light';
            setTheme(newSystemTheme);
        }
    });

    // Initialize theme toggle with retry mechanism
    initializeThemeToggle();
}

function initializeThemeToggle(retryCount = 0) {
    const themeToggle = document.getElementById('themeToggle');

    if (themeToggle && !themeToggle.hasAttribute('data-theme-initialized')) {
        // Mark as initialized to prevent duplicate event listeners
        themeToggle.setAttribute('data-theme-initialized', 'true');

        themeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const body = document.body;
            const currentTheme = body.getAttribute('data-theme');

            // If data-theme is set to 'dark', switch to light. Otherwise, switch to dark.
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            setTheme(newTheme);
            localStorage.setItem('wynfullTheme', newTheme);
        });
    } else if (!themeToggle && retryCount < 10) {
        // Retry after a short delay if element not found (max 10 retries)
        setTimeout(() => initializeThemeToggle(retryCount + 1), 100);
    }
}

function setTheme(theme) {
    const body = document.body;
    const documentElement = document.documentElement;
    const themeToggle = document.getElementById('themeToggle');
    const icon = themeToggle?.querySelector('i');

    if (theme === 'dark') {
        // Set theme on both elements to match inline script behavior
        documentElement.setAttribute('data-theme', 'dark');
        body.setAttribute('data-theme', 'dark');
        if (icon) {
            icon.className = 'fas fa-sun';
        }
    } else {
        // Remove or set to light theme
        documentElement.removeAttribute('data-theme');
        body.removeAttribute('data-theme');
        if (icon) {
            icon.className = 'fas fa-moon';
        }
    }
}

// User dropdown functionality
function initializeUserDropdowns() {
    const userDropdownToggle = document.getElementById('userDropdownToggle');
    const userDropdown = document.getElementById('userDropdown');

    if (userDropdownToggle && userDropdown) {
        userDropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        // Prevent dropdown from closing when clicking inside it
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdownToggle.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });
    }
}

// Comprehensive notification functionality
function initializeNotifications() {
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationPanel = document.getElementById('notificationPanel');
    const closeNotifications = document.getElementById('closeNotifications');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    const notificationList = document.getElementById('notificationList');


    if (notificationBtn && notificationPanel) {
        // Load initial notifications and count
        loadNotifications();
        updateNotificationCount();

        // Set up periodic updates (every 30 seconds)
        setInterval(() => {
            updateNotificationCount();
        }, 30000);

        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleNotificationPanel();
        });

        if (closeNotifications) {
            closeNotifications.addEventListener('click', function() {
                hideNotificationPanel();
            });
        }

        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                markAllNotificationsAsRead();
            });
        }
    }

    // Close notification panel when clicking outside
    document.addEventListener('click', function(e) {
        if (notificationPanel && !notificationPanel.contains(e.target) && !notificationBtn.contains(e.target)) {
            hideNotificationPanel();
        }
    });
}

function toggleNotificationPanel() {
    const notificationPanel = document.getElementById('notificationPanel');
    if (notificationPanel) {
        const isVisible = notificationPanel.classList.contains('active');
        if (isVisible) {
            hideNotificationPanel();
        } else {
            showNotificationPanel();
        }
    }
}

function showNotificationPanel() {
    const notificationPanel = document.getElementById('notificationPanel');
    if (notificationPanel) {
        notificationPanel.classList.add('active');
        loadNotifications(); // Refresh notifications when panel opens
    }
}

function hideNotificationPanel() {
    const notificationPanel = document.getElementById('notificationPanel');
    if (notificationPanel) {
        notificationPanel.classList.remove('active');
    }
}

async function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    if (!notificationList) return;

    try {
        const response = await fetch('/notifications');
        const data = await response.json();

        if (data.ok) {
            renderNotifications(data.data);
        } else {
            showNotificationError('Failed to load notifications');
        }
    } catch (error) {
        showNotificationError('Error loading notifications');
    }
}

function renderNotifications(notifications) {
    const notificationList = document.getElementById('notificationList');
    if (!notificationList) return;

    if (notifications.length === 0) {
        notificationList.innerHTML = `
            <div class="no-notifications">
                <i class="fas fa-bell-slash"></i>
                <p>No notifications yet</p>
            </div>
        `;
        return;
    }

    const notificationsHtml = notifications.map(notification => {
        const timeAgo = getTimeAgo(new Date(notification.created_at));
        const icon = getNotificationIcon(notification.type);
        const unreadClass = notification.read_at ? '' : 'unread';

        return `
            <div class="notification-item ${unreadClass}" data-id="${notification.id}" data-type="${notification.type}" data-assignment-id="${notification.data?.assignment_id || ''}" style="cursor: pointer;">
                <div class="notification-icon">
                    <i class="${icon}"></i>
                </div>
                <div class="notification-content">
                    <h4>${notification.title}</h4>
                    <p>${notification.message}</p>
                    <time>${timeAgo}</time>
                </div>
                ${!notification.read_at ? '<div class="notification-unread-dot"></div>' : ''}
            </div>
        `;
    }).join('');

    notificationList.innerHTML = notificationsHtml;

    // Add click handlers for all notifications (read and unread)
    notificationList.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            const notificationType = this.dataset.type;
            const assignmentId = this.dataset.assignmentId;

            // Mark as read if unread
            if (this.classList.contains('unread')) {
                markNotificationAsRead(notificationId, this);
            }

            // Handle different notification types
            if (notificationType === 'message' && assignmentId) {
                // Redirect to messaging page based on user role
                // The messaging page will handle opening the specific conversation
                const userRole = document.body.getAttribute('data-user-role') || 'client';
                if (userRole === 'coach') {
                    window.location.href = '/messages';
                } else {
                    window.location.href = '/messages/client';
                }
            }
            // Add more notification types here as needed
        });
    });
}

function getNotificationIcon(type) {
    const iconMap = {
        'message': 'fas fa-comment',
        'assignment': 'fas fa-user-plus',
        'reminder': 'fas fa-bell',
        'system': 'fas fa-info-circle'
    };
    return iconMap[type] || 'fas fa-bell';
}

function getTimeAgo(date) {
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;

    return date.toLocaleDateString();
}

async function markNotificationAsRead(notificationId, element) {
    try {
        const response = await fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();

        if (data.ok) {
            element.classList.remove('unread');
            const unreadDot = element.querySelector('.notification-unread-dot');
            if (unreadDot) {
                unreadDot.remove();
            }
            updateNotificationCount();
        }
    } catch (error) {
        // Silently handle error
    }
}

async function markAllNotificationsAsRead() {
    try {
        const response = await fetch('/notifications/read-all', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        const data = await response.json();

        if (data.ok) {
            // Remove unread class from all notifications
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
                const unreadDot = item.querySelector('.notification-unread-dot');
                if (unreadDot) {
                    unreadDot.remove();
                }
            });
            updateNotificationCount();
        }
    } catch (error) {
        // Silently handle error
    }
}

async function updateNotificationCount() {
    try {
        const response = await fetch('/notifications/count');
        const data = await response.json();

        if (data.ok) {
            const badge = document.querySelector('.notification-badge');
            if (badge) {
                if (data.unread_count > 0) {
                    badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    } catch (error) {
        // Silently handle error
    }
}

function showNotificationError(message) {
    const notificationList = document.getElementById('notificationList');
    if (notificationList) {
        notificationList.innerHTML = `
            <div class="notification-error">
                <i class="fas fa-exclamation-triangle"></i>
                <p>${message}</p>
            </div>
        `;
    }
}

// Logout confirmation functionality
function initializeLogoutConfirmation() {
    // Handle all logout forms (both sidebar and dropdown)
    const logoutForms = document.querySelectorAll('form[action*="logout"]');

    logoutForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            showLogoutModal(form);
        });
    });
}

// Show logout confirmation modal
function showLogoutModal(form) {
    // Create modal HTML
    const modalHTML = `
        <div class="logout-modal-overlay" id="logoutModalOverlay">
            <div class="logout-modal">
                <div class="logout-modal-header">
                    <h3>Confirm Logout</h3>
                </div>
                <div class="logout-modal-body">
                    <p>Are you sure you want to logout? You will need to sign in again to access your account.</p>
                </div>
                <div class="logout-modal-footer">
                    <button type="button" class="btn-secondary" id="cancelLogout">Cancel</button>
                    <button type="button" class="btn-danger" id="confirmLogout">Yes, Logout</button>
                </div>
            </div>
        </div>
    `;

    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    const modal = document.getElementById('logoutModalOverlay');
    const cancelBtn = document.getElementById('cancelLogout');
    const confirmBtn = document.getElementById('confirmLogout');

    // Show modal
    modal.style.display = 'flex';

    // Handle cancel
    cancelBtn.addEventListener('click', function() {
        closeLogoutModal();
    });

    // Handle confirm
    confirmBtn.addEventListener('click', function() {
        closeLogoutModal();
        // Close any open dropdowns before logout
        const userDropdown = document.getElementById('userDropdown');
        if (userDropdown) {
            userDropdown.classList.remove('show');
        }
        form.submit(); // Actually submit the logout form
    });

    // Handle click outside modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeLogoutModal();
        }
    });

    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLogoutModal();
        }
    });
}

// Close logout modal
function closeLogoutModal() {
    const modal = document.getElementById('logoutModalOverlay');
    if (modal) {
        modal.remove();
    }
}

// Global theme toggle function for standalone theme toggles
window.toggleTheme = function() {
    const body = document.body;
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const systemTheme = systemPrefersDark ? 'dark' : 'light';
    const currentTheme = body.getAttribute('data-theme') || systemTheme;
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    setTheme(newTheme);
    localStorage.setItem('wynfullTheme', newTheme);
};

// Mobile Menu Functionality
function initializeMobileMenu() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.querySelector('.sidebar');

    if (mobileMenuToggle && sidebar) {
        // Remove any existing event listeners
        mobileMenuToggle.removeEventListener('click', toggleMobileMenu);

        // Add click event listener
        mobileMenuToggle.addEventListener('click', toggleMobileMenu);

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (sidebar.classList.contains('mobile-active') &&
                !sidebar.contains(event.target) &&
                !mobileMenuToggle.contains(event.target)) {
                closeMobileMenu();
            }
        });

        // Close mobile menu when window is resized to desktop size
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeMobileMenu();
            }
        });
    }
}

function toggleMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const body = document.body;

    if (sidebar) {
        if (sidebar.classList.contains('mobile-active')) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }
}

function openMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const body = document.body;
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const toggleIcon = mobileMenuToggle ? mobileMenuToggle.querySelector('i') : null;

    if (sidebar) {
        sidebar.classList.add('mobile-active');
        body.style.overflow = 'hidden'; // Prevent background scrolling

        // Change hamburger to X icon
        if (mobileMenuToggle) {
            mobileMenuToggle.classList.add('active');
        }
        if (toggleIcon) {
            toggleIcon.className = 'fas fa-times';
        }
    }
}

function closeMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    const body = document.body;
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const toggleIcon = mobileMenuToggle ? mobileMenuToggle.querySelector('i') : null;

    if (sidebar) {
        sidebar.classList.remove('mobile-active');
        body.style.overflow = ''; // Restore scrolling

        // Change X back to hamburger icon
        if (mobileMenuToggle) {
            mobileMenuToggle.classList.remove('active');
        }
        if (toggleIcon) {
            toggleIcon.className = 'fas fa-bars';
        }
    }
}

// Info Panel functionality
function initializeInfoPanel() {
    const infoPanel = document.getElementById('infoPanel');
    const closeInfoBtn = document.getElementById('closeInfo');

    if (!infoPanel) return;

    // Close info panel
    if (closeInfoBtn) {
        closeInfoBtn.addEventListener('click', function() {
            closeInfoPanel();
        });
    }

    // Close panel when clicking outside
    document.addEventListener('click', function(e) {
        if (infoPanel.classList.contains('active') &&
            !infoPanel.contains(e.target) &&
            !e.target.closest('.info-indicator')) {
            closeInfoPanel();
        }
    });

    // Initialize info indicators
    initializeInfoIndicators();
}

function initializeInfoIndicators() {
    const infoIndicators = document.querySelectorAll('.info-indicator');

    infoIndicators.forEach(indicator => {
        indicator.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const tooltipType = this.getAttribute('data-tooltip');
            openInfoPanel(tooltipType);
        });
    });
}

function openInfoPanel(tooltipType) {
    const infoPanel = document.getElementById('infoPanel');
    const infoPanelTitle = document.getElementById('infoPanelTitle');
    const infoContent = document.getElementById('infoContent');

    if (!infoPanel || !infoPanelTitle || !infoContent) return;

    // Set title and content based on tooltip type
    const infoData = getInfoContent(tooltipType);
    infoPanelTitle.textContent = infoData.title;
    infoContent.innerHTML = infoData.content;

    // Show panel
    infoPanel.classList.add('active');
}

function closeInfoPanel() {
    const infoPanel = document.getElementById('infoPanel');
    if (infoPanel) {
        infoPanel.classList.remove('active');
    }
}

function getInfoContent(tooltipType) {
    const infoContents = {
        'phase-status': {
            title: 'Phase Status Information',
            content: `
                <h4>Understanding Your Financial Journey</h4>
                <p>Your phase status shows where you currently stand in your financial journey. Each phase represents a different stage of financial growth and stability.</p>

                <div class="info-highlight">
                    <strong>Current Phase Indicators:</strong>
                </div>

                <ul>
                    <li><strong>Reset & Rewire:</strong> Addressing debt and financial challenges</li>
                    <li><strong>Take Control:</strong> Building budgeting skills and emergency funds</li>
                    <li><strong>Grow & Multiply:</strong> Investing and growing your wealth</li>
                    <li><strong>Sustain & Scale:</strong> Advanced wealth management and planning</li>
                </ul>

                <p>Your coach will help you progress through these phases at your own pace, providing personalized guidance and support along the way.</p>
            `
        },
        'confidence-level': {
            title: 'Confidence Level',
            content: `
                <h4>Your Financial Confidence</h4>
                <p>This metric tracks how confident you feel about your financial decisions and future.</p>

                <div class="info-highlight">
                    <strong>Confidence Levels:</strong>
                </div>

                <ul>
                    <li><strong>Low (1-3):</strong> Feeling uncertain about financial decisions</li>
                    <li><strong>Moderate (4-6):</strong> Some confidence but room for improvement</li>
                    <li><strong>High (7-8):</strong> Generally confident in financial planning</li>
                    <li><strong>Expert (9-10):</strong> Very confident and knowledgeable</li>
                </ul>

                <p>Your confidence level helps your coach understand how to best support you and what areas need more focus.</p>
            `
        },
        'primary-goals': {
            title: 'Primary Goals',
            content: `
                <h4>Your Financial Objectives</h4>
                <p>Primary goals are the main financial objectives you want to achieve. These form the foundation of your financial planning strategy.</p>

                <div class="info-highlight">
                    <strong>Goal Types:</strong>
                </div>

                <ul>
                    <li><strong>Emergency Fund:</strong> Building financial security</li>
                    <li><strong>Debt Payoff:</strong> Eliminating high-interest debt</li>
                    <li><strong>Savings:</strong> Building wealth for the future</li>
                    <li><strong>Investment:</strong> Growing money through investments</li>
                    <li><strong>Major Purchase:</strong> House, car, or other significant expenses</li>
                </ul>

                <p>Your coach will help you prioritize and create actionable plans for each goal.</p>
            `
        },
        'confidence-score': {
            title: 'Budget Confidence Score',
            content: `
                <h4>Your Budget Confidence Level</h4>
                <p>This score reflects how confident you feel about creating, managing, and maintaining a budget. It measures your ability to track spending, plan ahead, and make intentional day-to-day financial decisions.
</p>

                <div class="info-highlight">
                    <strong>Score Levels:</strong>
                </div>

                <ul>
                    <li><strong>25% (Building):</strong> Just starting to build budgeting habits and understand your income and expenses.</li>
                    <li><strong>50% (Growing):</strong> Developing stronger budgeting skills and gaining better control over your spending.</li>
                    <li><strong>75% (Confident):</strong> Comfortable creating and managing a budget, with consistent tracking and adjustments.</li>
                    <li><strong>100% (Expert):</strong> Highly confident in budgeting, with strong discipline and proactive financial planning.</li>
                </ul>

                <p>This score helps your coach tailor their guidance to your comfort level and experience.</p>
            `
        },
        'personal-finance-confidence': {
            title: 'Personal Finance Confidence Score',
            content: `
                <h4>Your Personal Finance Confidence Level</h4>
                <p>This score reflects how confident you feel understanding core personal financial concepts.</p>

                <div class="info-highlight">
                    <strong>Score Level:</strong>
                </div>

                <ul>
                    <li><strong>25% (Not confident):</strong> Still building foundational financial knowledge</li>
                    <li><strong>50% (Somewhat confident):</strong> Developing understanding of core concepts</li>
                    <li><strong>75% (Confident):</strong> Strong grasp of core financial principles</li>
                    <li><strong>100% (Very confident):</strong> Excellent understanding of personal finance concepts</li>
                </ul>

                <p>This score helps your coach tailor their guidance to your comfort level and experience.</p>
            `
        },
        'debt-knowledge-journey': {
            title: 'Debt Knowledge Journey',
            content: `
                <h4>Your Debt Management Understanding</h4>
                <p>Tracks your understanding and confidence in applying debt management strategies.</p>

                <div class="info-highlight">
                    <strong>Knowledge Stages:</strong>
                </div>

                <ul>
                    <li><strong>No Knowledge (10%):</strong> Just starting to learn about debt management</li>
                    <li><strong>Learning Basics (40%):</strong> Understanding basics but finding them stressful to apply</li>
                    <li><strong>Applying Strategies (70%):</strong> Comfortable applying debt management strategies</li>
                    <li><strong>Expert Level (100%):</strong> Confident teaching or explaining debt strategies</li>
                </ul>

                <p>Your coach will help you strengthen your understanding and confidence in managing debt effectively.</p>
            `
        },
        'investing-knowledge': {
            title: 'Investing Knowledge',
            content: `
                <h4>Your Familiarity with Investing Concepts</h4>
                <p>Shows your current level of familiarity with investing concepts.</p>

                <div class="info-highlight">
                    <strong>Knowledge Levels:</strong>
                </div>

                <ul>
                    <li><strong>Not Familiar Yet (1/5):</strong> Just starting to learn about investing</li>
                    <li><strong>Familiar with Basics (2/5):</strong> Understanding fundamental investing concepts</li>
                    <li><strong>Comfortable Applying (4/5):</strong> Can apply investing concepts with confidence</li>
                    <li><strong>Advanced Understanding (5/5):</strong> Deep knowledge of investing strategies</li>
                </ul>

                <p>Your coach will help you build understanding and move to the next knowledge tier.</p>
            `
        },
        'emergency-readiness': {
            title: 'Emergency Readiness Level',
            content: `
                <h4>Your Emergency Preparedness Understanding</h4>
                <p>Reflects your confidence in understanding the steps involved in preparing for unexpected financial situations.</p>

                <div class="info-highlight">
                    <strong>Readiness Levels:</strong>
                </div>

                <ul>
                    <li><strong>Not Prepared (25%):</strong> Still learning about emergency financial planning</li>
                    <li><strong>Building Readiness (50%):</strong> Developing understanding of emergency preparedness</li>
                    <li><strong>Well Prepared (75%):</strong> Strong understanding of how to prepare for emergencies</li>
                    <li><strong>Fully Prepared (100%):</strong> Confident in emergency financial planning strategies</li>
                </ul>

                <p>Work with your trainer to build an emergency fund strategy and learn essential preparedness steps.</p>
            `
        },
        'investing-habit': {
            title: 'Investing Habit / Contribution Readiness',
            content: `
                <h4>Your Investing Experience Level</h4>
                <p>Reflects your investing experience level and readiness to contribute to investment accounts.</p>

                <div class="info-highlight">
                    <strong>Experience Levels:</strong>
                </div>

                <ul>
                    <li><strong>Building Foundation (33%):</strong> Beginner - Learning the basics of investing</li>
                    <li><strong>Growing Confidence (66%):</strong> Intermediate - Practical experience with investments</li>
                    <li><strong>Experienced Investor (100%):</strong> Advanced - Strong investing knowledge and experience</li>
                </ul>

                <p>Your coach will help you develop investing habits that match your experience level.</p>
            `
        },
        'default': {
            title: 'Information',
            content: `
                <h4>Help & Information</h4>
                <p>This section provides additional context and explanations for the various elements on your dashboard.</p>

                <p>If you need more specific help, please don't hesitate to reach out to your coach or our support team.</p>
            `
        }
    };

    return infoContents[tooltipType] || infoContents['default'];
}

// Make functions globally available
window.toggleMobileMenu = toggleMobileMenu;
window.openMobileMenu = openMobileMenu;
window.openInfoPanel = openInfoPanel;
window.closeInfoPanel = closeInfoPanel;
window.closeMobileMenu = closeMobileMenu;
