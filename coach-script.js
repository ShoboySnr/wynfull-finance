// Coach Dashboard JavaScript Functionality

document.addEventListener('DOMContentLoaded', function() {
    // Initialize coach dashboard functionality
    initializeCoachDashboard();
});

function initializeCoachDashboard() {
    // Get all navigation links and pages
    const navLinks = document.querySelectorAll('.nav-link');
    const pages = document.querySelectorAll('.page');
    const menuToggle = document.querySelector('#mobileMenuToggle');
    const sidebar = document.querySelector('.sidebar');

    // Handle navigation
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked nav item
            this.parentElement.classList.add('active');
            
            // Hide all pages
            pages.forEach(page => {
                page.classList.remove('active');
            });
            
            // Show selected page
            const targetPage = this.getAttribute('data-page');
            const targetElement = document.getElementById(targetPage);
            if (targetElement) {
                targetElement.classList.add('active');
            }
        });
    });

    // Mobile menu toggle
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });

    // Quick action buttons
    const quickActionBtns = document.querySelectorAll('.quick-action-card button');
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.quick-action-card');
            const title = card.querySelector('h3').textContent;
            
            // Add visual feedback
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            console.log(`Coach action: ${title} clicked`);
        });
    });

    // Schedule action buttons
    const scheduleActions = document.querySelectorAll('.schedule-actions button');
    scheduleActions.forEach(btn => {
        btn.addEventListener('click', function() {
            const scheduleItem = this.closest('.schedule-item');
            const clientName = scheduleItem.querySelector('h4').textContent;
            
            // Add visual feedback
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            console.log(`Schedule action for: ${clientName}`);
        });
    });

    // Activity action buttons
    const activityActions = document.querySelectorAll('.activity-action');
    activityActions.forEach(btn => {
        btn.addEventListener('click', function() {
            const activityItem = this.closest('.activity-item');
            const clientAction = activityItem.querySelector('h4').textContent;
            
            // Add visual feedback
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            console.log(`Activity action: ${clientAction}`);
        });
    });

    // Role switch functionality
    const clientViewBtn = document.getElementById('clientViewBtn');
    if (clientViewBtn) {
        clientViewBtn.addEventListener('click', function() {
            // Switch to client view
            window.location.href = 'index.html';
        });
    }

    // Initialize theme toggle
    initializeCoachTheme();
}

function initializeCoachTheme() {
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    
    // Load saved theme or default to light
    const savedTheme = localStorage.getItem('wynfullTheme') || 'light';
    body.setAttribute('data-theme', savedTheme);
    
    if (themeToggle) {
        // Update icon based on current theme
        updateThemeIcon(savedTheme);
        
        themeToggle.addEventListener('click', function() {
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('wynfullTheme', newTheme);
            updateThemeIcon(newTheme);
        });
    }
}

function updateThemeIcon(theme) {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        const icon = themeToggle.querySelector('i');
        if (theme === 'light') {
            icon.className = 'fas fa-moon';
        } else {
            icon.className = 'fas fa-sun';
        }
    }
}
