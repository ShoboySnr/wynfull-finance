// Navigation functionality
document.addEventListener('DOMContentLoaded', function() {
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
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const text = this.textContent.trim();
            
            if (text === 'Book Now') {
                // Navigate to coach call page
                document.querySelector('[data-page="coach-call"]').click();
            } else if (text === 'Explore') {
                // Navigate to resource library
                document.querySelector('[data-page="resource-library"]').click();
            } else if (text === 'Ask Now') {
                // Navigate to AI assistant
                document.querySelector('[data-page="ai-assistant"]').click();
            }
        });
    });

    // Chat functionality
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.querySelector('.send-btn');
    const chatMessages = document.querySelector('.chat-messages');

    function sendMessage() {
        const message = chatInput.value.trim();
        if (message) {
            // Add user message
            addMessage(message, 'user');
            chatInput.value = '';
            
            // Simulate AI response after a delay
            setTimeout(() => {
                const responses = [
                    "That's a great question! Let me help you with that.",
                    "Based on your current financial situation, I'd recommend...",
                    "Here's what you should consider for your financial goals:",
                    "I can help you create a plan for that. Let's start by..."
                ];
                const randomResponse = responses[Math.floor(Math.random() * responses.length)];
                addMessage(randomResponse, 'ai');
            }, 1000);
        }
    }

    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}-message`;
        
        const avatarDiv = document.createElement('div');
        avatarDiv.className = 'message-avatar';
        
        if (sender === 'ai') {
            avatarDiv.innerHTML = '<i class="fas fa-robot"></i>';
        } else {
            avatarDiv.innerHTML = '<img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=40&h=40&fit=crop&crop=face&auto=format" alt="User">';
        }
        
        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'message-bubble';
        bubbleDiv.innerHTML = `
            <p>${text}</p>
            <span class="message-time">Just now</span>
        `;
        
        messageDiv.appendChild(avatarDiv);
        messageDiv.appendChild(bubbleDiv);
        chatMessages.appendChild(messageDiv);
        
        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    if (sendBtn) {
        sendBtn.addEventListener('click', sendMessage);
    }

    if (chatInput) {
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }

    // Suggestion chips
    const suggestionChips = document.querySelectorAll('.suggestion-chip');
    suggestionChips.forEach(chip => {
        chip.addEventListener('click', function() {
            const question = this.textContent.trim();
            chatInput.value = question;
            sendMessage();
        });
    });

    // Initialize circular progress
    const progressRing = document.querySelector('.progress-ring-fill');
    if (progressRing) {
        const progress = 68; // 68% progress
        const circumference = 2 * Math.PI * 52; // radius = 52
        const offset = circumference - (progress / 100) * circumference;
        progressRing.style.strokeDashoffset = offset;
    }

    // Smooth scrolling for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add hover effects to cards
    const cards = document.querySelectorAll('.action-card, .progress-section, .activity-section');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Resource Library tab functionality
    const resourceTabs = document.querySelectorAll('.resource-tab');
    const resourceTabContents = document.querySelectorAll('.resource-tab-content');

    resourceTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            resourceTabs.forEach(t => t.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Hide all tab contents
            resourceTabContents.forEach(content => {
                content.classList.remove('active');
            });
            
            // Show selected tab content
            const targetTab = this.getAttribute('data-tab');
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });

    // Phase card interactions
    const phaseCards = document.querySelectorAll('.phase-card');
    phaseCards.forEach(card => {
        card.addEventListener('click', function() {
            if (!this.classList.contains('locked')) {
                const modules = this.querySelector('.phase-modules');
                if (modules) {
                    modules.style.display = modules.style.display === 'none' ? 'block' : 'none';
                }
            }
        });
    });

    // Module button interactions
    const moduleButtons = document.querySelectorAll('.module-btn');
    moduleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            
            if (!this.classList.contains('locked')) {
                const buttonText = this.textContent.trim();
                
                if (buttonText === 'Continue Learning' || buttonText === 'Open Resource') {
                    // Simulate opening a resource
                    this.textContent = 'Opening...';
                    this.disabled = true;
                    
                    setTimeout(() => {
                        this.textContent = buttonText;
                        this.disabled = false;
                        alert('Resource would open in a new window/modal');
                    }, 1000);
                }
            }
        });
    });

    // Tool download functionality
    const downloadButtons = document.querySelectorAll('.tool-download-btn');
    downloadButtons.forEach(button => {
        button.addEventListener('click', function() {
            const toolName = this.closest('.tool-card').querySelector('h3').textContent;
            
            // Simulate download
            const originalContent = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
            this.disabled = true;
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check"></i> Downloaded!';
                
                setTimeout(() => {
                    this.innerHTML = originalContent;
                    this.disabled = false;
                }, 2000);
                
                // Show download notification
                showNotification(`${toolName} has been downloaded to your computer.`);
            }, 1500);
        });
    });

    // Search functionality
    const resourceSearch = document.querySelector('.resource-search');
    if (resourceSearch) {
        resourceSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            // Search in phase modules
            const moduleItems = document.querySelectorAll('.module-item');
            moduleItems.forEach(item => {
                const title = item.querySelector('h4').textContent.toLowerCase();
                const description = item.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = searchTerm === '' ? 'flex' : 'none';
                }
            });
            
            // Search in tools
            const toolCards = document.querySelectorAll('.tool-card');
            toolCards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const description = card.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = searchTerm === '' ? 'block' : 'none';
                }
            });
        });
    }

    // Filter functionality
    const filterDropdowns = document.querySelectorAll('.filter-dropdown');
    filterDropdowns.forEach(dropdown => {
        dropdown.addEventListener('change', function() {
            const filterType = this.options[0].textContent; // "By Phase" or "By Format"
            const filterValue = this.value;
            
            if (filterType === 'By Phase') {
                const phaseCards = document.querySelectorAll('.phase-card');
                phaseCards.forEach(card => {
                    const phaseType = card.getAttribute('data-phase');
                    
                    if (filterValue === '' || phaseType === filterValue) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            } else if (filterType === 'By Format') {
                const moduleItems = document.querySelectorAll('.module-item');
                moduleItems.forEach(item => {
                    const resourceTags = item.querySelectorAll('.resource-tag');
                    let hasFormat = filterValue === '';
                    
                    resourceTags.forEach(tag => {
                        if (tag.classList.contains(filterValue)) {
                            hasFormat = true;
                        }
                    });
                    
                    item.style.display = hasFormat ? 'flex' : 'none';
                });
            }
        });
    });

    // Notification system
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--wynfull-blue);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            font-size: 0.9rem;
            max-width: 300px;
            animation: slideIn 0.3s ease;
        `;
        
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Add CSS animations for notifications
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // Initialize onboarding functionality
    initializeOnboarding();
    
    // Initialize modal functionality
    initializeModal();
    
    // Initialize notification functionality
    initializeNotifications();
    
    // Initialize theme functionality
    initializeTheme();
    
    // Show onboarding modal on page load
    openOnboardingModal();
});

// Onboarding functionality
let currentStep = 1;
const totalSteps = 6;

function showStep(stepNumber) {
    // Hide all steps
    document.querySelectorAll('.onboarding-step').forEach(step => {
        step.classList.remove('active');
    });
    
    // Show current step
    const targetStep = document.getElementById(`step${stepNumber}`);
    if (targetStep) {
        targetStep.classList.add('active');
    }
    
    // Update step counter
    const currentStepElement = document.getElementById('currentStep');
    if (currentStepElement) {
        currentStepElement.textContent = stepNumber;
    }
    
    // Update progress bar
    const progressFill = document.getElementById('progressFill');
    if (progressFill) {
        const progressPercentage = (stepNumber / totalSteps) * 100;
        progressFill.style.width = `${progressPercentage}%`;
    }
    
    // Update navigation buttons
    updateNavigationButtons(stepNumber);
}

function updateProgressIndicator(currentStep) {
    const steps = document.querySelectorAll('.step');
    const lines = document.querySelectorAll('.step-line');
    
    steps.forEach((step, index) => {
        const stepNumber = index + 1;
        if (stepNumber < currentStep) {
            step.classList.add('completed');
            step.classList.remove('active');
        } else if (stepNumber === currentStep) {
            step.classList.add('active');
            step.classList.remove('completed');
        } else {
            step.classList.remove('active', 'completed');
        }
    });
    
    lines.forEach((line, index) => {
        const stepNumber = index + 1;
        if (stepNumber < currentStep) {
            line.classList.add('completed');
        } else {
            line.classList.remove('completed');
        }
    });
}

function updateNavigationButtons(stepNumber) {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (prevBtn) {
        prevBtn.style.display = stepNumber === 1 ? 'none' : 'inline-block';
    }
    
    if (nextBtn) {
        nextBtn.textContent = stepNumber === totalSteps ? 'Complete Setup' : 'Next';
    }
}

function nextStep() {
    // Validate current step before proceeding
    if (!validateCurrentStep()) {
        return; // Don't proceed if validation fails
    }
    
    if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
    } else {
        // Complete onboarding
        completeOnboarding();
    }
}

function validateCurrentStep() {
    switch (currentStep) {
        case 1: // Income
            const income = document.getElementById('income');
            if (!income.value || parseFloat(income.value) <= 0) {
                alert('Please enter a valid annual income amount.');
                income.focus();
                return false;
            }
            break;
            
        case 2: // Debt
            const debt = document.getElementById('debt');
            if (!debt.value || parseFloat(debt.value) < 0) {
                alert('Please enter your total debt amount (enter 0 if you have no debt).');
                debt.focus();
                return false;
            }
            break;
            
        case 3: // Savings
            const savings = document.getElementById('savings');
            if (!savings.value || parseFloat(savings.value) < 0) {
                alert('Please enter your current savings amount (enter 0 if you have no savings).');
                savings.focus();
                return false;
            }
            break;
            
        case 4: // Experience
            const experience = document.querySelector('input[name="experience"]:checked');
            if (!experience) {
                alert('Please select your investing experience level.');
                return false;
            }
            break;
            
        case 5: // Goals
            const goals = document.querySelectorAll('input[name="goals"]:checked');
            if (goals.length === 0) {
                alert('Please select at least one financial goal.');
                return false;
            }
            break;
            
        case 6: // Motivation
            const motivation = document.getElementById('motivation');
            if (!motivation.value.trim()) {
                alert('Please tell us what motivates you to improve your finances.');
                motivation.focus();
                return false;
            }
            break;
    }
    
    return true;
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
    }
}

function completeOnboarding() {
    // Collect all form data
    const formData = collectOnboardingData();
    
    // Show completion message
    alert('Profile created successfully!');
    
    // Close the modal
    closeOnboardingModal();
    
    // Ensure Dashboard is active
    showPage('dashboard');
}

function collectOnboardingData() {
    const data = {};
    
    // Step 1: Income
    const income = document.getElementById('income');
    if (income) data.income = income.value;
    
    // Step 2: Debt
    const debt = document.getElementById('debt');
    if (debt) data.debt = debt.value;
    
    // Step 3: Savings
    const savings = document.getElementById('savings');
    if (savings) data.savings = savings.value;
    
    // Step 4: Experience
    const experience = document.querySelector('input[name="experience"]:checked');
    if (experience) data.experience = experience.value;
    
    // Step 5: Goals
    const goals = [];
    document.querySelectorAll('input[name="goals"]:checked').forEach(cb => {
        goals.push(cb.value);
    });
    data.goals = goals;
    
    // Step 6: Motivation
    const motivation = document.getElementById('motivation');
    if (motivation) data.motivation = motivation.value;
    
    console.log('Onboarding data collected:', data);
    return data;
}

// Slider value updates
function updateSliderValue(sliderId, displayId) {
    const slider = document.getElementById(sliderId);
    const display = document.getElementById(displayId);
    
    if (slider && display) {
        // Set initial value
        display.textContent = slider.value;
        
        // Update on change
        slider.addEventListener('input', function() {
            display.textContent = this.value;
        });
    }
}

// Initialize onboarding when page loads
function initializeOnboarding() {
    // Add event listeners for navigation buttons
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    
    if (nextBtn) {
        nextBtn.addEventListener('click', nextStep);
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', prevStep);
    }
}

// Modal functionality
function initializeModal() {
    const startBtn = document.getElementById('startOnboardingBtn');
    
    if (startBtn) {
        startBtn.addEventListener('click', openOnboardingModal);
    }
    
    // Modal is non-closable - no close button or click-outside functionality
}

function openOnboardingModal() {
    const modal = document.getElementById('onboardingModal');
    if (modal) {
        modal.classList.add('active');
        currentStep = 1;
        showStep(1);
    }
}

function closeOnboardingModal() {
    const modal = document.getElementById('onboardingModal');
    if (modal) {
        modal.classList.remove('active');
        // Reset form
        resetOnboardingForm();
    }
}

function resetOnboardingForm() {
    // Reset all form inputs
    document.querySelectorAll('#onboardingModal input, #onboardingModal textarea').forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });
    
    // Reset to first step
    currentStep = 1;
    showStep(1);
}

// Theme functionality
function initializeTheme() {
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    
    // Load saved theme or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = body.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            setTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
}

function setTheme(theme) {
    const body = document.body;
    const themeToggle = document.getElementById('themeToggle');
    const icon = themeToggle?.querySelector('i');
    
    if (theme === 'dark') {
        body.setAttribute('data-theme', 'dark');
        if (icon) {
            icon.className = 'fas fa-sun';
        }
    } else {
        body.removeAttribute('data-theme');
        if (icon) {
            icon.className = 'fas fa-moon';
        }
    }
}

// Notification functionality
function initializeNotifications() {
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationPanel = document.getElementById('notificationPanel');
    const closeNotifications = document.getElementById('closeNotifications');
    
    if (notificationBtn && notificationPanel) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleNotificationPanel();
        });
    }
    
    if (closeNotifications) {
        closeNotifications.addEventListener('click', function() {
            hideNotificationPanel();
        });
    }
    
    // Close notification panel when clicking outside
    document.addEventListener('click', function(e) {
        if (notificationPanel && !notificationPanel.contains(e.target) && !notificationBtn.contains(e.target)) {
            hideNotificationPanel();
        }
    });
}

function toggleNotificationPanel() {
    const panel = document.getElementById('notificationPanel');
    if (panel) {
        panel.classList.toggle('active');
    }
}

function hideNotificationPanel() {
    const panel = document.getElementById('notificationPanel');
    if (panel) {
        panel.classList.remove('active');
    }
}

// Helper function to show page (used by other parts of the app)
function showPage(pageId) {
    // Hide all pages
    document.querySelectorAll('.page').forEach(page => {
        page.classList.remove('active');
    });
    
    // Show target page
    const targetPage = document.getElementById(pageId);
    if (targetPage) {
        targetPage.classList.add('active');
        
        // Initialize page-specific functionality
        if (pageId === 'financial-profile') {
            initializeFinancialProfile();
        }
    }
    
    // Update navigation
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    const navLink = document.querySelector(`[data-page="${pageId}"]`);
    if (navLink) {
        navLink.parentElement.classList.add('active');
    }
}

// Financial Profile functionality
function initializeFinancialProfile() {
    // Load user data from onboarding or localStorage
    loadUserProfileData();
    
    // Initialize radar chart
    createRadarChart();
    
    // Add event listeners for profile actions
    setupProfileActions();
}

function loadUserProfileData() {
    // This would normally load from a database or localStorage
    // For now, we'll use sample data
    const sampleData = {
        confidence: 7,
        stress: 4,
        primaryGoal: "Build Emergency Fund",
        timeframe: "6-12 months",
        experience: "Beginner",
        budgetingHabits: ["Track expenses manually", "Use budgeting apps"],
        savingFrequency: "Monthly",
        investmentExperience: "None"
    };
    
    // Update profile display
    updateProfileDisplay(sampleData);
}

function updateProfileDisplay(data) {
    // Update confidence score
    const confidenceElement = document.getElementById('profile-confidence');
    const confidenceFill = document.querySelector('.confidence-fill');
    if (confidenceElement && confidenceFill) {
        confidenceElement.textContent = data.confidence;
        confidenceFill.style.width = `${data.confidence * 10}%`;
    }
    
    // Update stress score
    const stressElement = document.getElementById('profile-stress');
    const stressFill = document.querySelector('.stress-fill');
    if (stressElement && stressFill) {
        stressElement.textContent = data.stress;
        stressFill.style.width = `${data.stress * 10}%`;
    }
    
    // Update goal and timeline
    const goalElement = document.getElementById('profile-goal');
    const timelineElement = document.getElementById('profile-timeline');
    if (goalElement) goalElement.textContent = data.primaryGoal;
    if (timelineElement) timelineElement.textContent = data.timeframe;
    
    // Update experience
    const experienceElement = document.getElementById('profile-experience');
    if (experienceElement) experienceElement.textContent = data.experience;
}

function createRadarChart() {
    const canvas = document.getElementById('radarChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = 150;
    
    // Sample data for financial health areas
    const areas = [
        { label: 'Budgeting', current: 7, target: 9 },
        { label: 'Saving', current: 6, target: 8 },
        { label: 'Investing', current: 3, target: 7 },
        { label: 'Debt Management', current: 8, target: 9 },
        { label: 'Emergency Fund', current: 4, target: 8 },
        { label: 'Financial Planning', current: 5, target: 8 }
    ];
    
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Draw background grid
    drawRadarGrid(ctx, centerX, centerY, radius, areas.length);
    
    // Draw data polygons
    drawRadarData(ctx, centerX, centerY, radius, areas, 'current', '#0E4DA4', 0.3);
    drawRadarData(ctx, centerX, centerY, radius, areas, 'target', '#2EB67D', 0.2);
    
    // Draw labels
    drawRadarLabels(ctx, centerX, centerY, radius + 20, areas);
}

function drawRadarGrid(ctx, centerX, centerY, radius, sides) {
    const angleStep = (2 * Math.PI) / sides;
    
    // Draw concentric circles
    ctx.strokeStyle = '#E5E7EB';
    ctx.lineWidth = 1;
    
    for (let i = 1; i <= 5; i++) {
        const r = (radius * i) / 5;
        ctx.beginPath();
        ctx.arc(centerX, centerY, r, 0, 2 * Math.PI);
        ctx.stroke();
    }
    
    // Draw radial lines
    for (let i = 0; i < sides; i++) {
        const angle = i * angleStep - Math.PI / 2;
        const x = centerX + Math.cos(angle) * radius;
        const y = centerY + Math.sin(angle) * radius;
        
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(x, y);
        ctx.stroke();
    }
}

function drawRadarData(ctx, centerX, centerY, radius, areas, dataType, color, alpha) {
    const angleStep = (2 * Math.PI) / areas.length;
    
    ctx.beginPath();
    
    for (let i = 0; i < areas.length; i++) {
        const angle = i * angleStep - Math.PI / 2;
        const value = areas[i][dataType] / 10; // Normalize to 0-1
        const r = radius * value;
        const x = centerX + Math.cos(angle) * r;
        const y = centerY + Math.sin(angle) * r;
        
        if (i === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    }
    
    ctx.closePath();
    ctx.fillStyle = color + Math.floor(alpha * 255).toString(16).padStart(2, '0');
    ctx.fill();
    ctx.strokeStyle = color;
    ctx.lineWidth = 2;
    ctx.stroke();
    
    // Draw data points
    ctx.fillStyle = color;
    for (let i = 0; i < areas.length; i++) {
        const angle = i * angleStep - Math.PI / 2;
        const value = areas[i][dataType] / 10;
        const r = radius * value;
        const x = centerX + Math.cos(angle) * r;
        const y = centerY + Math.sin(angle) * r;
        
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, 2 * Math.PI);
        ctx.fill();
    }
}

function drawRadarLabels(ctx, centerX, centerY, radius, areas) {
    const angleStep = (2 * Math.PI) / areas.length;
    
    ctx.fillStyle = '#374151';
    ctx.font = '12px Inter, sans-serif';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    
    for (let i = 0; i < areas.length; i++) {
        const angle = i * angleStep - Math.PI / 2;
        const x = centerX + Math.cos(angle) * radius;
        const y = centerY + Math.sin(angle) * radius;
        
        // Adjust text alignment based on position
        if (x < centerX - 10) {
            ctx.textAlign = 'right';
        } else if (x > centerX + 10) {
            ctx.textAlign = 'left';
        } else {
            ctx.textAlign = 'center';
        }
        
        ctx.fillText(areas[i].label, x, y);
    }
}

function setupProfileActions() {
    // Update Profile button
    const updateBtn = document.querySelector('.profile-actions .btn-secondary');
    if (updateBtn) {
        updateBtn.addEventListener('click', function() {
            showNotification('Redirecting to profile update...', 'info');
            setTimeout(() => {
                showPage('onboarding-page');
            }, 1000);
        });
    }
    
    // Download Report button
    const downloadBtn = document.querySelector('.profile-actions .btn-primary');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            generateReport();
        });
    }
    
    // Recommendation buttons
    const recButtons = document.querySelectorAll('.rec-btn');
    recButtons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.textContent.trim();
            const card = this.closest('.recommendation-card');
            const title = card.querySelector('h4').textContent;
            
            if (action === 'Start Now' || action === 'Explore Tools') {
                showNotification(`Opening ${title} resources...`, 'success');
                setTimeout(() => {
                    showPage('resource-library');
                }, 1000);
            } else {
                showNotification(`${action} for ${title}`, 'info');
            }
        });
    });
}

function generateReport() {
    showNotification('Generating your financial profile report...', 'info');
    
    // Simulate report generation
    setTimeout(() => {
        showNotification('Report generated successfully! Check your downloads.', 'success');
    }, 2000);
}

// Profile update function
function updateProfile() {
    showNotification('Redirecting to profile update...', 'info');
    setTimeout(() => {
        showPage('onboarding-page');
    }, 1000);
}
