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

// Basic notification functionality
function initializeNotifications() {
    const notificationBtn = document.getElementById('notificationBtn');
    
    if (notificationBtn) {
        notificationBtn.addEventListener('click', function() {
            // Placeholder for notification functionality
            console.log('Notifications clicked');
        });
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
