// Video Sound Toggle Function
function toggleVideoSound() {
    const video = document.querySelector('.hero-main-video');
    const volumeIcon = document.getElementById('volume-icon');

    if (video.muted) {
        video.muted = false;
        volumeIcon.className = 'fas fa-volume-up';
    } else {
        video.muted = true;
        volumeIcon.className = 'fas fa-volume-mute';
    }
}

// Landing Page JavaScript
document.addEventListener('DOMContentLoaded', function () {
    // Resource Library Tabs
    const resourceTabs = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    resourceTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const targetTab = this.getAttribute('data-tab');

            // Remove active class from all tabs and contents
            resourceTabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });

    // Primary Financial Goal dropdown handler
    const goalSelect = document.getElementById('client-goal');
    const otherGoalGroup = document.getElementById('other-goal-group');

    if (goalSelect && otherGoalGroup) {
        goalSelect.addEventListener('change', function () {
            if (this.value === 'other') {
                otherGoalGroup.style.display = 'block';
                document.getElementById('other-goal').setAttribute('required', 'required');
            } else {
                otherGoalGroup.style.display = 'none';
                document.getElementById('other-goal').removeAttribute('required');
            }
        });
    }

    // Coach specialty "Other" checkbox handler
    const specialtyOtherCheckbox = document.getElementById('specialty-other');
    const otherSpecialtyGroup = document.getElementById('other-specialty-group');

    if (specialtyOtherCheckbox && otherSpecialtyGroup) {
        specialtyOtherCheckbox.addEventListener('change', function () {
            if (this.checked) {
                otherSpecialtyGroup.style.display = 'block';
                document.getElementById('other-specialty').setAttribute('required', 'required');
            } else {
                otherSpecialtyGroup.style.display = 'none';
                document.getElementById('other-specialty').removeAttribute('required');
            }
        });
    }

    // Signup form tabs
    const signupTabs = document.querySelectorAll('.signup-tab');
    const formContainers = document.querySelectorAll('.form-container');

    signupTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const targetForm = this.getAttribute('data-form');

            // Remove active class from all tabs and forms
            signupTabs.forEach(t => t.classList.remove('active'));
            formContainers.forEach(form => form.classList.remove('active'));

            // Add active class to clicked tab and corresponding form
            this.classList.add('active');
            const targetFormElement = document.getElementById(targetForm + '-form');
            if (targetFormElement) {
                targetFormElement.classList.add('active');
            }
        });
    });

    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('a[href^="#"]');
    navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                const offsetTop = targetElement.offsetTop - 80;
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

    // Netlify forms will handle submission automatically
    // Remove the preventDefault to allow Netlify to process forms

    // if (clientForm) {
    //     clientForm.addEventListener('submit', function(e) {
    //         e.preventDefault();
    //         handleClientFormSubmission(this);
    //     });
    // }

    // if (coachForm) {
    //     coachForm.addEventListener('submit', function(e) {
    //         e.preventDefault();
    //         handleCoachFormSubmission(this);
    //     });
    // }

    // Hero CTA button actions
    const primaryCTA = document.querySelector('.hero-ctas .btn-primary');
    const secondaryCTA = document.querySelector('.hero-ctas .btn-secondary');

    if (primaryCTA) {
        primaryCTA.addEventListener('click', function () {
            const signupSection = document.querySelector('#contact');
            if (signupSection) {
                signupSection.scrollIntoView({behavior: 'smooth'});
            }
        });
    }

    if (secondaryCTA) {
        secondaryCTA.addEventListener('click', function () {
            const howItWorksSection = document.querySelector('#how-it-works');
            if (howItWorksSection) {
                howItWorksSection.scrollIntoView({behavior: 'smooth'});
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

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    submitBtn.disabled = true;

    setTimeout(() => {
        showSuccessMessage('Thank you! We\'ll be in touch soon about early access.');
        form.reset();
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
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

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting Application...';
    submitBtn.disabled = true;

    setTimeout(() => {
        showSuccessMessage('Application received! We\'ll review your profile and get back to you within 48 hours.');
        form.reset();
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
}

// Show Success Message
function showSuccessMessage(message) {
    const notification = document.createElement('div');
    notification.className = 'success-notification';
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        </div>
    `;

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

    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}

// Navbar scroll effect
window.addEventListener('scroll', function () {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Resource Library tab functionality
document.addEventListener('DOMContentLoaded', function () {
    const resourceTabs = document.querySelectorAll('.resource-tab');
    const resourceTabContents = document.querySelectorAll('.resource-tab-content');

    resourceTabs.forEach(tab => {
        tab.addEventListener('click', function () {
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
});


// Privacy Modal Functions
function openPrivacyModal() {
    document.getElementById('privacyModal').style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closePrivacyModal() {
    document.getElementById('privacyModal').style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore background scrolling
}

// Terms Modal Functions
function openTermsModal() {
    document.getElementById('termsModal').style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeTermsModal() {
    document.getElementById('termsModal').style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore background scrolling
}

// Close modals when clicking outside of them
document.getElementById('privacyModal').addEventListener('click', function (e) {
    if (e.target === this) {
        closePrivacyModal();
    }
});

document.getElementById('termsModal').addEventListener('click', function (e) {
    if (e.target === this) {
        closeTermsModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closePrivacyModal();
        closeTermsModal();
        closeMobileMenu();
    }
});

// Mobile Menu Functions
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('mobileMenuOverlay');

    if (mobileMenu.classList.contains('active')) {
        closeMobileMenu();
    } else {
        openMobileMenu();
    }
}

function openMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('mobileMenuOverlay');

    mobileMenu.classList.add('active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMobileMenu() {
    const mobileMenu = document.getElementById('mobileMenu');
    const overlay = document.getElementById('mobileMenuOverlay');

    mobileMenu.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close mobile menu when window is resized to desktop size
window.addEventListener('resize', function () {
    if (window.innerWidth > 768) {
        closeMobileMenu();
    }
});

// Video Modal Functions
function openVideoModal() {
    const modal = document.getElementById('videoModal');
    const video = document.getElementById('modalVideo');

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Auto-play video when modal opens
    video.currentTime = 0;
    video.play();
}

function closeVideoModal() {
    const modal = document.getElementById('videoModal');
    const video = document.getElementById('modalVideo');

    modal.style.display = 'none';
    document.body.style.overflow = 'auto';

    // Pause video when modal closes
    video.pause();
}

// Close video modal with Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeVideoModal();
    }
});
