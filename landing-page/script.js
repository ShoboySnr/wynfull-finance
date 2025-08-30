// Landing Page JavaScript

// Tab Switching for Resource Library
document.addEventListener('DOMContentLoaded', function() {
    // Resource Library Tabs
    const resourceTabs = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    resourceTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            resourceTabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // Signup Form Tabs - Fixed implementation
    const signupTabs = document.querySelectorAll('.signup-tab');
    const formContainers = document.querySelectorAll('.form-container');
    
    console.log('Found signup tabs:', signupTabs.length);
    console.log('Found form containers:', formContainers.length);
    
    signupTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetForm = this.getAttribute('data-form');
            console.log('Clicked tab for:', targetForm);
            
            // Remove active class from all tabs and forms
            signupTabs.forEach(t => t.classList.remove('active'));
            formContainers.forEach(form => form.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding form
            this.classList.add('active');
            const targetFormElement = document.getElementById(targetForm + '-form');
            if (targetFormElement) {
                targetFormElement.classList.add('active');
                console.log('Activated form:', targetForm + '-form');
            } else {
                console.error('Could not find form element:', targetForm + '-form');
            }
        });
    });
    
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const offsetTop = targetElement.offsetTop - 80; // Account for fixed navbar
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Form Submissions
    const clientForm = document.querySelector('#client-form form');
    const coachForm = document.querySelector('#coach-form form');
    
    if (clientForm) {
        clientForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleClientFormSubmission(this);
        });
    }
    
    if (coachForm) {
        coachForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleCoachFormSubmission(this);
        });
    }
    
    // Mobile menu toggle (basic implementation)
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const navLinksMenu = document.querySelector('.nav-links');
    
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            navLinksMenu.classList.toggle('mobile-active');
        });
    }
    
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    const animateElements = document.querySelectorAll('.step-card, .resource-card, .tool-card, .testimonial-card');
    animateElements.forEach(el => observer.observe(el));
    
    // Hero CTA button actions
    const primaryCTA = document.querySelector('.hero-ctas .btn-primary');
    const secondaryCTA = document.querySelector('.hero-ctas .btn-secondary');
    
    if (primaryCTA) {
        primaryCTA.addEventListener('click', function() {
            // Scroll to signup section
            const signupSection = document.querySelector('#contact');
            if (signupSection) {
                signupSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
    
    if (secondaryCTA) {
        secondaryCTA.addEventListener('click', function() {
            // Scroll to how it works section
            const howItWorksSection = document.querySelector('#how-it-works');
            if (howItWorksSection) {
                howItWorksSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
});

// Handle Client Form Submission
function handleClientFormSubmission(form) {
    const formData = new FormData(form);
    const data = {
        name: formData.get('name'),
        email: formData.get('email'),
        goal: formData.get('goal'),
        military: formData.get('military'),
        notes: formData.get('notes')
    };
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Show success message
        showSuccessMessage('Thank you! We\'ll be in touch soon about early access.');
        
        // Reset form
        form.reset();
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Track event (if analytics is set up)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'form_submit', {
                'event_category': 'engagement',
                'event_label': 'client_signup'
            });
        }
    }, 2000);
}

// Handle Coach Form Submission
function handleCoachFormSubmission(form) {
    const formData = new FormData(form);
    const specialties = [];
    const specialtyCheckboxes = form.querySelectorAll('input[name="specialties"]:checked');
    specialtyCheckboxes.forEach(checkbox => {
        specialties.push(checkbox.value);
    });
    
    const data = {
        name: formData.get('name'),
        email: formData.get('email'),
        experience: formData.get('experience'),
        specialties: specialties,
        linkedin: formData.get('linkedin'),
        website: formData.get('website')
    };
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting Application...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Show success message
        showSuccessMessage('Application received! We\'ll review your profile and get back to you within 48 hours.');
        
        // Reset form
        form.reset();
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Track event (if analytics is set up)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'form_submit', {
                'event_category': 'engagement',
                'event_label': 'coach_application'
            });
        }
    }, 2000);
}

// Show Success Message
function showSuccessMessage(message) {
    // Create success notification
    const notification = document.createElement('div');
    notification.className = 'success-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: #22C55E;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 1001;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 5 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}

// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.background = 'rgba(255, 255, 255, 0.98)';
        navbar.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
    } else {
        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
        navbar.style.boxShadow = 'none';
    }
});

// Add animation classes to CSS (will be handled by CSS)
const style = document.createElement('style');
style.textContent = `
    .animate-in {
        animation: slideInUp 0.6s ease forwards;
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .mobile-active {
        display: flex !important;
        flex-direction: column;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid var(--border-light);
        padding: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 768px) {
        .nav-links {
            display: none;
        }
    }
`;
document.head.appendChild(style);
