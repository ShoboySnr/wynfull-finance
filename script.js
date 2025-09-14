// Navigation functionality
document.addEventListener('DOMContentLoaded', function() {
    // Check if onboarding has been completed
    checkOnboardingStatus();
    
    // Initialize download popup functionality
    initializeDownloadPopup();
    
    // Initialize walkthrough sidebar functionality
    initializeWalkthroughSidebar();
    
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
    const quickActionBtns = document.querySelectorAll('.action-btn');
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.action-card');
            const title = card.querySelector('h3').textContent;
            
            // Add some visual feedback
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            // You can add specific functionality for each action here
            console.log(`${title} clicked`);
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

    // Tool download functionality is now handled by initializeDownloadPopup()

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
    
    // Initialize all functionality
    initializeModal();
    initializeTheme();
    initializeAnimations();
    initializeMessaging();
    initializeNotifications();
});

// Messaging functionality
function initializeMessaging() {
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const messagesList = document.getElementById('messagesList');
    const coachItems = document.querySelectorAll('.coach-item');
    
    if (!messageInput || !sendBtn || !messagesList) {
        return; // Messaging elements not found, probably not on messaging page
    }
    
    // Coach selection functionality
    coachItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all coaches
            coachItems.forEach(coach => coach.classList.remove('active'));
            
            // Add active class to clicked coach
            this.classList.add('active');
            
            // Update chat header with selected coach info
            const coachName = this.querySelector('h4').textContent;
            const coachSpecialty = this.querySelector('.coach-specialty').textContent;
            const coachAvatar = this.querySelector('.coach-avatar img').src;
            const statusDot = this.querySelector('.status-dot');
            const isOnline = statusDot.classList.contains('online');
            
            updateChatHeader(coachName, coachSpecialty, coachAvatar, isOnline);
            
            // Clear current messages and load coach-specific messages
            loadCoachMessages(this.dataset.coach);
            
            // Remove unread badge if present
            const unreadBadge = this.querySelector('.unread-badge');
            if (unreadBadge) {
                unreadBadge.remove();
            }
        });
    });
    
    function updateChatHeader(name, specialty, avatar, isOnline) {
        const coachHeader = document.querySelector('.coach-header');
        const statusText = isOnline ? 'Online' : 'Offline';
        const statusClass = isOnline ? 'online' : 'offline';
        
        coachHeader.querySelector('.coach-profile img').src = avatar;
        coachHeader.querySelector('.coach-details h3').textContent = name;
        coachHeader.querySelector('.coach-details p').textContent = specialty;
        coachHeader.querySelector('.coach-status span:last-child').textContent = statusText;
        coachHeader.querySelector('.coach-status').className = `coach-status ${statusClass}`;
    }
    
    function loadCoachMessages(coachId) {
        // Clear existing messages except date separators
        const messages = messagesList.querySelectorAll('.message');
        messages.forEach(msg => msg.remove());
        
        // Load different message sets based on coach
        if (coachId === 'sarah-chen') {
            loadSarahChenMessages();
        } else if (coachId === 'michael-torres') {
            loadMichaelTorresMessages();
        } else if (coachId === 'lisa-wang') {
            loadLisaWangMessages();
        } else if (coachId === 'david-kim') {
            loadDavidKimMessages();
        }
    }
    
    function loadSarahChenMessages() {
        // Keep existing Sarah Chen messages (default)
        // Messages are already in HTML
    }
    
    function loadMichaelTorresMessages() {
        const todaySection = messagesList.querySelector('.message-date');
        
        const message1 = createMessageElement("I've reviewed your 401k contribution strategy. You're currently contributing 6% but your employer matches up to 8%. I recommend increasing to get the full match.", 'coach');
        const message2 = createMessageElement("That's free money you're leaving on the table! How does increasing to 8% sound?", 'coach');
        const message3 = createMessageElement("You're absolutely right! I can increase it to 8% starting next paycheck. Will this affect my tax situation significantly?", 'user');
        const message4 = createMessageElement("Great decision! The additional 2% will actually reduce your taxable income, so you'll see tax benefits too. I'll send you a projection.", 'coach');
        
        todaySection.after(message1, message2, message3, message4);
    }
    
    function loadLisaWangMessages() {
        const todaySection = messagesList.querySelector('.message-date');
        
        const message1 = createMessageElement("The home buying checklist you requested is ready! I've customized it based on your current financial situation and the local market.", 'coach');
        const message2 = createMessageElement("Based on your income and savings, you're in a good position to consider homes in the $350-400k range.", 'coach');
        const message3 = createMessageElement("That's exciting! Should I start looking at pre-approval options? What's the typical down payment you'd recommend?", 'user');
        const message4 = createMessageElement("I'd recommend 10-15% down to avoid PMI while keeping some emergency funds. Let's schedule a call to discuss lenders.", 'coach');
        
        todaySection.after(message1, message2, message3, message4);
    }
    
    function loadDavidKimMessages() {
        const todaySection = messagesList.querySelector('.message-date');
        
        const message1 = createMessageElement("Your business plan looks solid! The financial projections are realistic and the market analysis is thorough.", 'coach');
        const message2 = createMessageElement("I especially like your conservative revenue estimates for year one. Have you considered the business structure - LLC vs S-Corp?", 'coach');
        const message3 = createMessageElement("I was leaning towards LLC for simplicity. What are the main advantages of S-Corp for a business like mine?", 'user');
        const message4 = createMessageElement("For your projected revenue, S-Corp could save on self-employment taxes. Let's run the numbers together in our next session.", 'coach');
        
        todaySection.after(message1, message2, message3, message4);
    }
    
    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        
        // Enable/disable send button based on content
        sendBtn.disabled = this.value.trim() === '';
    });
    
    // Send message on button click
    sendBtn.addEventListener('click', sendMessage);
    
    // Send message on Enter key (but allow Shift+Enter for new lines)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    function sendMessage() {
        const messageText = messageInput.value.trim();
        if (!messageText) return;
        
        // Create user message element
        const messageElement = createMessageElement(messageText, 'user');
        
        // Add message to list
        messagesList.appendChild(messageElement);
        
        // Clear input
        messageInput.value = '';
        messageInput.style.height = 'auto';
        sendBtn.disabled = true;
        
        // Scroll to bottom
        messagesList.scrollTop = messagesList.scrollHeight;
        
        // Simulate coach response after a delay
        setTimeout(() => {
            const responses = [
                "Thanks for your message! I'll review this and get back to you shortly.",
                "Great question! Let me look into this for you.",
                "I appreciate you sharing this with me. I'll provide some guidance soon.",
                "That's a good point. I'll prepare some recommendations for you."
            ];
            const randomResponse = responses[Math.floor(Math.random() * responses.length)];
            const coachMessage = createMessageElement(randomResponse, 'coach');
            messagesList.appendChild(coachMessage);
            messagesList.scrollTop = messagesList.scrollHeight;
        }, 1000 + Math.random() * 2000); // Random delay between 1-3 seconds
    }
    
    function createMessageElement(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender === 'user' ? 'user-message' : 'coach-message'}`;
        
        const currentTime = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        if (sender === 'coach') {
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=40&h=40&fit=crop&crop=face&auto=format" alt="Sarah Chen">
                </div>
                <div class="message-content">
                    <div class="message-header">
                        <span class="message-sender">Sarah Chen</span>
                        <span class="message-time">${currentTime}</span>
                    </div>
                    <div class="message-text">${text}</div>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="message-content">
                    <div class="message-header">
                        <span class="message-sender">You</span>
                        <span class="message-time">${currentTime}</span>
                    </div>
                    <div class="message-text">${text}</div>
                </div>
            `;
        }
        
        return messageDiv;
    }
    
    // Initialize send button state
    sendBtn.disabled = true;
}

// Check onboarding status function
function checkOnboardingStatus() {
    const onboardingCompleted = localStorage.getItem('wynfullOnboardingCompleted');
    
    if (onboardingCompleted === 'true') {
        // Onboarding already completed, load existing data and update dashboard
        const savedData = localStorage.getItem('wynfullOnboardingData');
        if (savedData) {
            try {
                const formData = JSON.parse(savedData);
                updateDashboardFromOnboarding(formData);
            } catch (error) {
                console.error('Error loading onboarding data:', error);
            }
        }
        return;
    }
    
    // Show onboarding modal automatically for new users
    setTimeout(() => {
        openOnboardingModal();
    }, 1000); // Small delay to ensure page is fully loaded
}

// Onboarding functionality
let currentStep = 1;
const totalSteps = 7;

function showStep(stepNumber) {
    // Clear any existing error message when changing steps
    hideErrorMessage();
    
    // Hide all steps
    document.querySelectorAll('.onboarding-step').forEach(step => {
        step.classList.remove('active');
    });
    
    // Show current step
    const currentStepElement = document.getElementById(`step${stepNumber}`);
    if (currentStepElement) {
        currentStepElement.classList.add('active');
    }
    
    // Update progress bar
    const progressFill = document.getElementById('progressFill');
    const progressPercentage = (stepNumber / totalSteps) * 100;
    if (progressFill) {
        progressFill.style.width = `${progressPercentage}%`;
    }
    
    // Update step counter
    const currentStepDisplay = document.getElementById('currentStep');
    if (currentStepDisplay) {
        currentStepDisplay.textContent = stepNumber;
    }
    
    // Update button visibility
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const completeBtn = document.getElementById('completeBtn');
    
    if (prevBtn) {
        prevBtn.style.display = stepNumber === 1 ? 'none' : 'inline-flex';
    }
    
    if (nextBtn && completeBtn) {
        if (stepNumber === totalSteps) {
            nextBtn.style.display = 'none';
            completeBtn.style.display = 'inline-flex';
        } else {
            nextBtn.style.display = 'inline-flex';
            completeBtn.style.display = 'none';
        }
    }
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
    // Clear any existing error message
    hideErrorMessage();
    
    switch (currentStep) {
        case 1: // Financial situation
            const financialSituationCheckboxes = document.querySelectorAll('input[name="financial-situation"]:checked');
            if (financialSituationCheckboxes.length === 0) {
                showErrorMessage('Please select at least one option that describes your current financial situation.');
                return false;
            }
            // If "other" is selected, check if text is provided
            const otherSelected = Array.from(financialSituationCheckboxes).some(cb => cb.value === 'other');
            if (otherSelected) {
                const otherText = document.getElementById('financial-situation-other');
                if (!otherText.value.trim()) {
                    showErrorMessage('Please specify your financial situation.');
                    otherText.focus();
                    return false;
                }
            }
            break;
            
        case 2: // Primary goal
            const primaryGoal = document.querySelector('input[name="primary-goal"]:checked');
            if (!primaryGoal) {
                showErrorMessage('Please select your primary financial goal.');
                return false;
            }
            // If "other" is selected, check if text is provided
            if (primaryGoal.value === 'other') {
                const otherText = document.getElementById('primary-goal-other');
                if (!otherText.value.trim()) {
                    showErrorMessage('Please specify your primary financial goal.');
                    otherText.focus();
                    return false;
                }
            }
            break;
            
        case 3: // Confidence level
            const confidenceLevel = document.querySelector('input[name="confidence-level"]:checked');
            if (!confidenceLevel) {
                showErrorMessage('Please select your confidence level.');
                return false;
            }
            break;
            
        case 4: // Debt feelings
            const debtFeelings = document.querySelector('input[name="debt-feeling"]:checked');
            if (!debtFeelings) {
                showErrorMessage('Please select how you feel about your current debt.');
                return false;
            }
            break;
            
        case 5: // Savings amount
            const savingsAmount = document.querySelector('input[name="savings-amount"]:checked');
            if (!savingsAmount) {
                showErrorMessage('Please select your current savings amount.');
                return false;
            }
            break;
            
        case 6: // Investing status
            const investingStatus = document.querySelector('input[name="investing-status"]:checked');
            if (!investingStatus) {
                showErrorMessage('Please select your current investing status.');
                return false;
            }
            break;
            
        case 7: // Investing experience
            const investingExperience = document.querySelector('input[name="investing-experience"]:checked');
            if (!investingExperience) {
                showErrorMessage('Please select your investing experience level.');
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

// Error message functions for onboarding modal
function showErrorMessage(message) {
    const errorContainer = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');
    
    if (errorContainer && errorText) {
        errorText.textContent = message;
        errorContainer.style.display = 'flex';
        
        // Scroll to the error message
        setTimeout(() => {
            errorContainer.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }, 100); // Small delay to ensure the element is visible
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            hideErrorMessage();
        }, 5000);
    }
}

function hideErrorMessage() {
    const errorContainer = document.getElementById('errorMessage');
    if (errorContainer) {
        errorContainer.style.display = 'none';
    }
}

// Welcome modal functions
function showWelcomeModal() {
    const welcomeModal = document.getElementById('welcomeModal');
    if (welcomeModal) {
        populateWelcomeSummary();
        welcomeModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function populateWelcomeSummary() {
    // Get data from localStorage if available, otherwise collect from form
    let data = JSON.parse(localStorage.getItem('wynfullOnboardingData')) || collectOnboardingData();
    
    // Financial Situation
    const financialSituation = document.getElementById('summary-financial-situation');
    if (financialSituation) {
        if (data.financialSituation) {
            if (Array.isArray(data.financialSituation)) {
                const situations = data.financialSituation.map(situation => {
                    if (situation === 'other' && data.financialSituationOther) {
                        return data.financialSituationOther;
                    }
                    return formatOptionText(situation);
                });
                financialSituation.textContent = situations.join(', ');
            } else {
                financialSituation.textContent = formatOptionText(data.financialSituation);
            }
        } else {
            financialSituation.textContent = 'Not specified';
        }
    }
    
    // Primary Goal
    const primaryGoal = document.getElementById('summary-primary-goal');
    if (primaryGoal) {
        if (data.primaryGoal) {
            if (data.primaryGoal === 'other' && data.primaryGoalOther) {
                primaryGoal.textContent = data.primaryGoalOther;
            } else {
                primaryGoal.textContent = formatOptionText(data.primaryGoal);
            }
        } else {
            primaryGoal.textContent = 'Not specified';
        }
    }
    
    // Confidence Level
    const confidenceLevel = document.getElementById('summary-confidence-level');
    if (confidenceLevel) {
        confidenceLevel.textContent = data.confidenceLevel ? formatOptionText(data.confidenceLevel) : 'Not specified';
    }
    
    // Debt Feelings
    const debtFeelings = document.getElementById('summary-debt-feelings');
    if (debtFeelings) {
        debtFeelings.textContent = data.debtFeeling ? formatOptionText(data.debtFeeling) : 'Not specified';
    }
    
    // Savings Amount
    const savingsAmount = document.getElementById('summary-savings-amount');
    if (savingsAmount) {
        savingsAmount.textContent = data.savingsAmount ? formatOptionText(data.savingsAmount) : 'Not specified';
    }
    
    // Investing Experience
    const investingExperience = document.getElementById('summary-investing-experience');
    if (investingExperience) {
        investingExperience.textContent = data.investingExperience ? formatOptionText(data.investingExperience) : 'Not specified';
    }
    
    // Learning Style
    const learningStyle = document.getElementById('summary-learning-style');
    if (learningStyle) {
        learningStyle.textContent = data.learningStyle ? formatOptionText(data.learningStyle) : 'Not specified';
    }
}

function formatOptionText(value) {
    // Convert kebab-case to readable text
    return value.split('-').map(word => 
        word.charAt(0).toUpperCase() + word.slice(1)
    ).join(' ');
}

function closeWelcomeModal() {
    const welcomeModal = document.getElementById('welcomeModal');
    if (welcomeModal) {
        welcomeModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

function completeOnboarding() {
    // Collect all form data
    const formData = collectOnboardingData();
        
    // Save onboarding completion status to localStorage
    localStorage.setItem('wynfullOnboardingCompleted', 'true');
    localStorage.setItem('wynfullOnboardingData', JSON.stringify(formData));
    
    // Update dashboard based on questionnaire responses
    updateDashboardFromOnboarding(formData);
    
    // Close the onboarding modal
    closeOnboardingModal();
    
    // Show welcome modal
    setTimeout(() => {
        showWelcomeModal();
    }, 500);
    
    // Ensure Dashboard is active
    showPage('dashboard');
}

function collectOnboardingData() {
    const data = {};
    
    // Step 1: Financial Situation
    const financialSituationCheckboxes = document.querySelectorAll('input[name="financial-situation"]:checked');
    if (financialSituationCheckboxes.length > 0) {
        data.financialSituation = Array.from(financialSituationCheckboxes).map(cb => cb.value);
        if (data.financialSituation.includes('other')) {
            const otherInput = document.getElementById('financial-situation-other');
            if (otherInput) data.financialSituationOther = otherInput.value;
        }
    }
    
    // Step 2: Primary Goal
    const primaryGoal = document.querySelector('input[name="primary-goal"]:checked');
    if (primaryGoal) {
        data.primaryGoal = primaryGoal.value;
        if (primaryGoal.value === 'other') {
            const otherInput = document.getElementById('primary-goal-other');
            if (otherInput) data.primaryGoalOther = otherInput.value;
        }
    }
    
    // Step 3: Confidence Level
    const confidenceLevel = document.querySelector('input[name="confidence-level"]:checked');
    if (confidenceLevel) data.confidenceLevel = confidenceLevel.value;
    
    // Step 4: Debt Feeling
    const debtFeeling = document.querySelector('input[name="debt-feeling"]:checked');
    if (debtFeeling) data.debtFeeling = debtFeeling.value;
    
    // Step 5: Savings Amount
    const savingsAmount = document.querySelector('input[name="savings-amount"]:checked');
    if (savingsAmount) data.savingsAmount = savingsAmount.value;
    
    // Step 6: Investing Status
    const investingStatus = document.querySelector('input[name="investing-status"]:checked');
    if (investingStatus) data.investingStatus = investingStatus.value;
    
    // Step 7: Investing Experience
    const investingExperience = document.querySelector('input[name="investing-experience"]:checked');
    if (investingExperience) data.investingExperience = investingExperience.value;
    
    console.log('Onboarding data collected:', data);
    return data;
}

// Update dashboard components based on onboarding responses
function updateDashboardFromOnboarding(data) {
    // Update Primary Goal Card
    updatePrimaryGoalCard(data.primaryGoal);
    
    // Update Confidence Score Card
    updateConfidenceScoreCard(data.confidenceLevel);
    
    // Update Investing Knowledge Card
    updateInvestingKnowledgeCard(data.investingExperience);
    
    // Update Debt Progress Card
    updateDebtProgressCard(data.debtFeeling);
    
    // Update Phase Progress based on financial situation
    updatePhaseProgress(data.financialSituation);
    
    // Update Emergency Fund Thermometer
    updateEmergencyFundThermometer(data.savingsAmount);
    
    // Update Investment Tracker
    updateInvestmentTracker(data.investingStatus);
}

function updateDebtProgressCard(debtFeeling) {
    const debtText = document.querySelector('.debt-text');
    const debtSubtitle = document.querySelector('.debt-subtitle');
    const debtProgressFill = document.querySelector('.debt-progress-fill');
    const debtIcon = document.querySelector('.debt-icon i');
    
    if (!debtText || !debtSubtitle || !debtProgressFill || !debtIcon) return;
    
    const debtMap = {
        'overwhelmed': {
            text: 'Starting your journey',
            subtitle: 'Every step counts',
            progress: 15,
            icon: 'fas fa-seedling'
        },
        'concerned': {
            text: 'Building momentum',
            subtitle: 'You\'re taking action',
            progress: 35,
            icon: 'fas fa-chart-line'
        },
        'manageable': {
            text: 'Making great progress',
            subtitle: 'Keep up the good work',
            progress: 65,
            icon: 'fas fa-trending-up'
        },
        'confident': {
            text: 'Debt under control',
            subtitle: 'You\'re doing amazing',
            progress: 85,
            icon: 'fas fa-check-circle'
        }
    };
    
    const debtStatus = debtMap[debtFeeling] || debtMap['concerned'];
    
    debtText.textContent = debtStatus.text;
    debtSubtitle.textContent = debtStatus.subtitle;
    debtProgressFill.style.width = `${debtStatus.progress}%`;
    debtIcon.className = debtStatus.icon;
}

function updatePrimaryGoalCard(primaryGoal) {
    const goalText = document.getElementById('primaryGoalText');
    const goalProgress = document.getElementById('primaryGoalProgress');
    
    if (!goalText || !goalProgress) return;
    
    const goalMap = {
        'pay-off-debt': 'Pay Off Debt',
        'emergency-fund': 'Build Emergency Fund',
        'big-purchase': 'Save for Big Purchase',
        'start-investing': 'Start Investing',
        'wealth-retirement': 'Build Wealth'
    };
    
    goalText.textContent = goalMap[primaryGoal] || 'Not Set';
    goalProgress.textContent = 'In Progress';
    goalProgress.className = 'metric-change positive';
}

function updateConfidenceScoreCard(confidenceLevel) {
    const scoreValue = document.getElementById('confidenceScoreValue');
    const scoreLevel = document.getElementById('confidenceScoreLevel');
    
    if (!scoreValue || !scoreLevel) return;
    
    const confidenceMap = {
        'not-confident': { score: '25%', level: 'Building Confidence', class: 'negative' },
        'somewhat-confident': { score: '50%', level: 'Growing Confidence', class: 'neutral' },
        'confident': { score: '75%', level: 'Confident', class: 'positive' },
        'very-confident': { score: '90%', level: 'Very Confident', class: 'positive' }
    };
    
    const confidence = confidenceMap[confidenceLevel] || { score: '--', level: 'Not Set', class: 'neutral' };
    scoreValue.textContent = confidence.score;
    scoreLevel.textContent = confidence.level;
    scoreLevel.className = `metric-change ${confidence.class}`;
}

function updateInvestingKnowledgeCard(investingExperience) {
    const knowledgeText = document.querySelector('.knowledge-text');
    const knowledgeSubtitle = document.querySelector('.knowledge-subtitle');
    const knowledgeDots = document.querySelectorAll('.knowledge-dots .dot');
    
    if (!knowledgeText || !knowledgeSubtitle || !knowledgeDots.length) return;
    
    const experienceMap = {
        'never-invested': { 
            level: 'Beginner', 
            subtitle: 'Starting your learning journey',
            activeDots: 1
        },
        'some-experience': { 
            level: 'Intermediate', 
            subtitle: 'Growing your expertise',
            activeDots: 3
        },
        'experienced': { 
            level: 'Advanced', 
            subtitle: 'Mastering financial strategies',
            activeDots: 5
        }
    };
    
    const experience = experienceMap[investingExperience] || experienceMap['some-experience'];
    
    knowledgeText.textContent = experience.level;
    knowledgeSubtitle.textContent = experience.subtitle;
    
    // Update dots based on experience level
    knowledgeDots.forEach((dot, index) => {
        if (index < experience.activeDots) {
            dot.classList.add('active');
        } else {
            dot.classList.remove('active');
        }
    });
}

function updatePhaseProgress(financialSituation) {
    const phaseMap = {
        'struggling-debt': { reset: 100, control: 0, grow: 0, sustain: 0 },
        'paycheck-to-paycheck': { reset: 80, control: 20, grow: 0, sustain: 0 },
        'okay-not-saving': { reset: 60, control: 40, grow: 0, sustain: 0 },
        'saving-regularly': { reset: 100, control: 80, grow: 30, sustain: 0 },
        'confident-focused': { reset: 100, control: 100, grow: 70, sustain: 20 }
    };
    
    // Handle array of financial situations (multiple checkboxes)
    let progress = { reset: 0, control: 0, grow: 0, sustain: 0 };
    
    if (Array.isArray(financialSituation)) {
        // Calculate average progress across all selected situations
        let totalProgress = { reset: 0, control: 0, grow: 0, sustain: 0 };
        let validSelections = 0;
        
        financialSituation.forEach(situation => {
            if (phaseMap[situation]) {
                totalProgress.reset += phaseMap[situation].reset;
                totalProgress.control += phaseMap[situation].control;
                totalProgress.grow += phaseMap[situation].grow;
                totalProgress.sustain += phaseMap[situation].sustain;
                validSelections++;
            }
        });
        
        if (validSelections > 0) {
            progress = {
                reset: Math.round(totalProgress.reset / validSelections),
                control: Math.round(totalProgress.control / validSelections),
                grow: Math.round(totalProgress.grow / validSelections),
                sustain: Math.round(totalProgress.sustain / validSelections)
            };
        }
    } else {
        // Handle single selection (backward compatibility)
        progress = phaseMap[financialSituation] || { reset: 0, control: 0, grow: 0, sustain: 0 };
    }
    
    updatePhaseBar('resetRewireProgress', progress.reset);
    updatePhaseBar('takeControlProgress', progress.control);
    updatePhaseBar('growMultiplyProgress', progress.grow);
    updatePhaseBar('sustainScaleProgress', progress.sustain);
}

function updatePhaseBar(phaseId, percentage) {
    const phaseElement = document.getElementById(phaseId);
    if (!phaseElement) return;
    
    const fill = phaseElement.querySelector('.phase-fill');
    const percentageText = phaseElement.querySelector('.phase-percentage');
    
    if (fill) fill.style.width = `${percentage}%`;
    if (percentageText) percentageText.textContent = `${percentage}%`;
}

function updateEmergencyFundThermometer(savingsAmount) {
    const currentElement = document.getElementById('emergencyFundCurrent');
    const targetElement = document.getElementById('emergencyFundTarget');
    const fillElement = document.getElementById('emergencyFundFill');
    
    if (!currentElement || !targetElement || !fillElement) return;
    
    const savingsMap = {
        'zero': { current: 0, target: 5000 },
        'under-1k': { current: 500, target: 5000 },
        '1k-5k': { current: 3000, target: 10000 },
        '5k-20k': { current: 12500, target: 20000 },
        '20k-plus': { current: 25000, target: 30000 }
    };
    
    const savings = savingsMap[savingsAmount] || { current: 0, target: 5000 };
    const percentage = Math.min((savings.current / savings.target) * 100, 100);
    
    currentElement.textContent = `$${savings.current.toLocaleString()}`;
    targetElement.textContent = `$${savings.target.toLocaleString()}`;
    fillElement.style.height = `${percentage}%`;
}

function updateInvestmentTracker(investingStatus) {
    const investmentCard = document.querySelector('.metric-card:has(.fa-chart-line)');
    if (!investmentCard) return;
    
    const valueElement = investmentCard.querySelector('.metric-value');
    const changeElement = investmentCard.querySelector('.metric-change');
    
    const statusMap = {
        'not-yet': { value: '$0', change: 'Ready to Start', class: 'neutral' },
        'just-starting': { value: '$2,500', change: 'Getting Started', class: 'positive' },
        'consistently': { value: '$32,100', change: '+18.7%', class: 'positive' }
    };
    
    const status = statusMap[investingStatus] || { value: '$0', change: 'Not Set', class: 'neutral' };
    
    if (valueElement) valueElement.textContent = status.value;
    if (changeElement) {
        changeElement.textContent = status.change;
        changeElement.className = `metric-change ${status.class}`;
    }
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

// Download Format Popup Functions
function initializeDownloadPopup() {
    const downloadButtons = document.querySelectorAll('.tool-download-btn');
    const popup = document.getElementById('download-format-popup');
    const closeBtn = document.querySelector('.download-popup-close');
    const formatOptions = document.querySelectorAll('.format-option');
    
    let currentToolName = '';
    
    // Add click event to all download buttons
    downloadButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get the tool name from the closest tool card
            const toolCard = this.closest('.tool-card');
            const toolTitle = toolCard.querySelector('h3').textContent;
            currentToolName = toolTitle;
            
            // Show the popup
            popup.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });
    });
    
    // Close popup when clicking the close button
    closeBtn.addEventListener('click', function() {
        closeDownloadPopup();
    });
    
    // Close popup when clicking outside
    popup.addEventListener('click', function(e) {
        if (e.target === popup) {
            closeDownloadPopup();
        }
    });
    
    // Handle format selection
    formatOptions.forEach(option => {
        option.addEventListener('click', function() {
            const format = this.dataset.format;
            startDownload(currentToolName, format);
            closeDownloadPopup();
        });
    });
    
    // Close popup with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && popup.style.display === 'block') {
            closeDownloadPopup();
        }
    });
}

function closeDownloadPopup() {
    const popup = document.getElementById('download-format-popup');
    popup.style.display = 'none';
    document.body.style.overflow = ''; // Restore scrolling
}

function startDownload(toolName, format) {
    // Create a temporary download link (simulating file download)
    const fileName = `${toolName.toLowerCase().replace(/\s+/g, '-')}.${format === 'docx' ? 'docx' : format === 'excel' ? 'xlsx' : 'pdf'}`;
    
    // In a real application, you would have actual file URLs
    // For now, we'll simulate the download with a placeholder
    const link = document.createElement('a');
    link.href = `#`; // This would be the actual file URL
    link.download = fileName;
    
    // Show a brief success message
    showDownloadNotification(toolName, format);
    
    // Simulate download (in real app, uncomment the line below)
    // link.click();
}

function showDownloadNotification(toolName, format) {
    // Create a temporary notification
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #10b981;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1001;
        font-size: 14px;
        font-weight: 500;
        animation: slideInRight 0.3s ease-out;
    `;
    
    const formatName = format === 'docx' ? 'Word Document' : format === 'excel' ? 'Excel Spreadsheet' : 'PDF';
    notification.textContent = `${toolName} (${formatName}) download started!`;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-in';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Walkthrough Sidebar Functions
function initializeWalkthroughSidebar() {
    const walkthroughIndicators = document.querySelectorAll('.walkthrough-indicator');
    const sidebar = document.getElementById('walkthrough-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const closeBtn = document.querySelector('.walkthrough-close');
    const replayBtn = document.querySelector('.replay-video');
    const downloadBtn = document.querySelector('.download-after-video');
    
    // Add click event to all walkthrough indicators
    walkthroughIndicators.forEach(indicator => {
        indicator.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const toolId = this.dataset.tool;
            openWalkthroughSidebar(toolId);
        });
    });
    
    // Close sidebar events
    closeBtn.addEventListener('click', closeWalkthroughSidebar);
    overlay.addEventListener('click', closeWalkthroughSidebar);
    
    // Replay video
    replayBtn.addEventListener('click', function() {
        const video = document.getElementById('walkthrough-video');
        video.currentTime = 0;
        video.play();
    });
    
    // Download after video
    downloadBtn.addEventListener('click', function() {
        // Trigger the download popup for the current tool
        const currentTool = sidebar.dataset.currentTool;
        if (currentTool) {
            closeWalkthroughSidebar();
            // Find the download button for this tool and trigger it
            const toolCard = document.querySelector(`[data-tool="${currentTool}"]`).closest('.tool-card');
            const downloadButton = toolCard.querySelector('.tool-download-btn');
            if (downloadButton) {
                downloadButton.click();
            }
        }
    });
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            closeWalkthroughSidebar();
        }
    });
}

function openWalkthroughSidebar(toolId) {
    const sidebar = document.getElementById('walkthrough-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    // Store current tool for download functionality
    sidebar.dataset.currentTool = toolId;
    
    // Populate sidebar with tool-specific content
    populateWalkthroughContent(toolId);
    
    // Show sidebar and overlay
    sidebar.classList.add('open');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeWalkthroughSidebar() {
    const sidebar = document.getElementById('walkthrough-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const video = document.getElementById('walkthrough-video');
    
    // Hide sidebar and overlay
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    
    // Pause video if playing
    if (video && !video.paused) {
        video.pause();
    }
}

function populateWalkthroughContent(toolId) {
    const toolData = getToolData(toolId);
    
    // Update title
    document.getElementById('walkthrough-title').textContent = `${toolData.name} Walkthrough`;
    
    // Update video
    const video = document.getElementById('walkthrough-video');
    video.src = toolData.videoUrl;
    video.poster = toolData.posterUrl;
    
    // Update description
    document.getElementById('tool-description').textContent = toolData.description;
    
    // Update features
    const featuresList = document.getElementById('tool-features');
    featuresList.innerHTML = toolData.features.map(feature => `<li>${feature}</li>`).join('');
    
    // Update steps
    const stepsList = document.getElementById('tool-steps');
    stepsList.innerHTML = toolData.steps.map(step => `<li>${step}</li>`).join('');
}

function getToolData(toolId) {
    const toolDatabase = {
        'monthly-budget-tracker': {
            name: 'Monthly Budget Tracker',
            description: 'A comprehensive Excel template that helps you track your monthly income, categorize expenses, and monitor your savings goals. Perfect for getting a clear picture of your financial habits.',
            videoUrl: '/videos/budget-tracker-walkthrough.mp4',
            posterUrl: '/images/budget-tracker-poster.jpg',
            features: [
                'Automatic calculations for income vs expenses',
                'Pre-built expense categories with customization options',
                'Visual charts showing spending patterns',
                'Savings goal tracking with progress indicators',
                'Monthly comparison reports'
            ],
            steps: [
                'Download and open the Excel template',
                'Enter your monthly income in the designated cells',
                'Input your expenses by category throughout the month',
                'Review the automatically generated charts and summaries',
                'Set and track your savings goals using the built-in calculator'
            ]
        },
        'debt-payoff-calculator': {
            name: 'Debt Payoff Calculator',
            description: 'Calculate your debt elimination timeline using either the debt snowball or avalanche method. This tool helps you create a strategic plan to become debt-free faster.',
            videoUrl: '/videos/debt-calculator-walkthrough.mp4',
            posterUrl: '/images/debt-calculator-poster.jpg',
            features: [
                'Support for both snowball and avalanche methods',
                'Interest calculation and payment scheduling',
                'Visual timeline showing debt elimination progress',
                'Extra payment impact analysis',
                'Printable payment schedule'
            ],
            steps: [
                'List all your debts with balances and interest rates',
                'Choose between snowball or avalanche method',
                'Set your total monthly payment amount',
                'Review the generated payment schedule',
                'Track your progress as you pay down debts'
            ]
        },
        'emergency-fund-planner': {
            name: 'Emergency Fund Planner',
            description: 'Calculate your ideal emergency fund size based on your expenses and track your progress toward this crucial financial safety net.',
            videoUrl: '/videos/emergency-fund-walkthrough.mp4',
            posterUrl: '/images/emergency-fund-poster.jpg',
            features: [
                'Personalized emergency fund target calculation',
                'Monthly savings goal recommendations',
                'Progress tracking with visual indicators',
                'Scenario planning for different emergency types',
                'High-yield savings account recommendations'
            ],
            steps: [
                'Calculate your monthly essential expenses',
                'Determine your target emergency fund amount (3-6 months)',
                'Set a realistic monthly savings goal',
                'Choose the best savings account for your fund',
                'Track your progress and celebrate milestones'
            ]
        },
        'investment-portfolio-tracker': {
            name: 'Investment Portfolio Tracker',
            description: 'Monitor your investment performance, track asset allocation, and analyze your portfolio\'s growth over time with this comprehensive tracking tool.',
            videoUrl: '/videos/portfolio-tracker-walkthrough.mp4',
            posterUrl: '/images/portfolio-tracker-poster.jpg',
            features: [
                'Multi-account portfolio consolidation',
                'Asset allocation analysis and rebalancing alerts',
                'Performance tracking with benchmark comparisons',
                'Dividend and income tracking',
                'Tax-loss harvesting opportunities identification'
            ],
            steps: [
                'Input your investment accounts and holdings',
                'Set your target asset allocation percentages',
                'Update market values regularly (or link to data feeds)',
                'Review performance reports and rebalancing needs',
                'Use insights to optimize your investment strategy'
            ]
        },
        'financial-goals-worksheet': {
            name: 'Financial Goals Worksheet',
            description: 'Set and track SMART financial goals with actionable steps and timeline tracking. Turn your financial dreams into achievable milestones.',
            videoUrl: '/videos/goals-worksheet-walkthrough.mp4',
            posterUrl: '/images/goals-worksheet-poster.jpg',
            features: [
                'SMART goal framework implementation',
                'Timeline and milestone tracking',
                'Action step breakdown and planning',
                'Progress monitoring and adjustment tools',
                'Goal prioritization matrix'
            ],
            steps: [
                'Define your financial goals using the SMART framework',
                'Break down each goal into actionable steps',
                'Set realistic timelines and milestones',
                'Create accountability measures and check-ins',
                'Track progress and adjust plans as needed'
            ]
        },
        'home-buying-readiness-checklist': {
            name: 'Home Buying Readiness Checklist',
            description: 'Comprehensive checklist and calculator to determine if you\'re financially ready to buy a home, including affordability analysis and preparation steps.',
            videoUrl: '/videos/home-buying-walkthrough.mp4',
            posterUrl: '/images/home-buying-poster.jpg',
            features: [
                'Affordability calculator with debt-to-income ratios',
                'Down payment and closing cost estimators',
                'Credit score improvement tracking',
                'Pre-approval preparation checklist',
                'Market research and comparison tools'
            ],
            steps: [
                'Assess your current financial situation',
                'Calculate how much house you can afford',
                'Determine your down payment and closing cost needs',
                'Check and improve your credit score if needed',
                'Complete the pre-approval preparation checklist'
            ]
        },
        'college-savings-planner': {
            name: 'College Savings Planner',
            description: 'Plan and track savings for education expenses with projections for tuition costs and optimal savings strategies including 529 plans.',
            videoUrl: '/videos/college-savings-walkthrough.mp4',
            posterUrl: '/images/college-savings-poster.jpg',
            features: [
                'Future tuition cost projections with inflation',
                '529 plan vs other savings vehicle comparisons',
                'Age-based savings goal calculations',
                'Tax benefit optimization strategies',
                'Multiple child planning support'
            ],
            steps: [
                'Estimate future education costs for your timeline',
                'Choose the best savings vehicle (529, Coverdell, etc.)',
                'Calculate required monthly savings amounts',
                'Set up automatic contributions and investments',
                'Monitor progress and adjust for cost changes'
            ]
        },
        'insurance-needs-calculator': {
            name: 'Insurance Needs Calculator',
            description: 'Determine the right amount of life and disability insurance coverage based on your family\'s needs and financial obligations.',
            videoUrl: '/videos/insurance-calculator-walkthrough.mp4',
            posterUrl: '/images/insurance-calculator-poster.jpg',
            features: [
                'Life insurance needs analysis using multiple methods',
                'Disability insurance coverage calculations',
                'Family income replacement planning',
                'Debt and expense coverage analysis',
                'Policy comparison and recommendation tools'
            ],
            steps: [
                'Calculate your family\'s income replacement needs',
                'Factor in debts, mortgages, and future expenses',
                'Determine appropriate coverage amounts',
                'Compare different policy types and providers',
                'Review and update coverage as life changes'
            ]
        }
    };
    
    return toolDatabase[toolId] || {
        name: 'Tool Walkthrough',
        description: 'Learn how to use this financial tool effectively.',
        videoUrl: '/videos/default-walkthrough.mp4',
        posterUrl: '/images/default-poster.jpg',
        features: ['Feature 1', 'Feature 2', 'Feature 3'],
        steps: ['Step 1', 'Step 2', 'Step 3']
    };
}
