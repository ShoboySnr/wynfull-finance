// Main JavaScript file for Wynfull Finance
// Theme functionality that works across all layouts

document.addEventListener('DOMContentLoaded', function() {
    // Initialize theme on page load
    initializeTheme();
    
    // Initialize other common functionality
    initializeUserDropdowns();
    initializeNotifications();
    initializeLogoutConfirmation();
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
