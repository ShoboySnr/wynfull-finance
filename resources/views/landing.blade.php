@extends('layouts.guest')
@section('title', 'Wynfull Finance - Building Financial Warriors for Life')

@push('styles')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}">
@endpush

@section('content')

    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <div class="logo-icon">
                    <img src="{{ asset('assets/img/wynfull-logo.png') }}" alt="Wynfull Finance Logo" class="logo-image">
                </div>
                <div class="logo-text">
                    <h2>Wynfull</h2>
                    <span>Finance</span>
                </div>
            </div>
            <div class="nav-links">
                <a href="#how-it-works">How It Works</a>
                <a href="#resources">Strategy</a>
            </div>
            <div class="nav-cta-container">
                <a href="#contact" class="nav-cta">Join the Community</a>
                @auth
                    @if(auth()->user()->hasRole('client'))
                        <a href="{{ route('dashboard.client') }}" class="nav-cta dashboard-nav-btn">Go to Dashboard</a>
                    @elseif(auth()->user()->hasRole('coach'))
                        <a href="{{ route('coach.dashboard') }}" class="nav-cta dashboard-nav-btn">Go to Dashboard</a>
                    @elseif(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.dashboard') }}" class="nav-cta dashboard-nav-btn">Go to Dashboard</a>
                    @endif
                @endauth
                <button class="theme-toggle-landing" id="themeToggle" title="Toggle Dark/Light Mode">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
            <div class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-menu-header">
                <div class="mobile-menu-close" onclick="closeMobileMenu()">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            <div class="mobile-menu-content">
                <a href="#how-it-works" onclick="closeMobileMenu()">How It Works</a>
                <a href="#resources" onclick="closeMobileMenu()">Strategy</a>
                <a href="#contact" onclick="closeMobileMenu()" class="mobile-cta">Join the Community</a>
                @auth
                    @if(auth()->user()->hasRole('client'))
                        <a href="{{ route('dashboard.client') }}" onclick="closeMobileMenu()" class="mobile-cta dashboard-mobile-btn">Go to Dashboard</a>
                    @elseif(auth()->user()->hasRole('coach'))
                        <a href="{{ route('coach.dashboard') }}" onclick="closeMobileMenu()" class="mobile-cta dashboard-mobile-btn">Go to Dashboard</a>
                    @elseif(auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.dashboard') }}" onclick="closeMobileMenu()" class="mobile-cta dashboard-mobile-btn">Go to Dashboard</a>
                    @endif
                @endauth
                <button class="theme-toggle-mobile" onclick="toggleTheme(); closeMobileMenu();" title="Toggle Dark/Light Mode">
                    <i class="fas fa-moon"></i> <span>Toggle Theme</span>
                </button>
            </div>
        </div>

        <div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <video class="hero-background-video" autoplay muted loop playsinline>
            <source src="{{ asset('assets/video/wynfull_background_video.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="hero-background-gradient"></div>
        <div class="container container-fluid">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-headline">
                        <span class="highlight">Build Real Financial Skills.</span>
                        Take Control of Your Money, Your Way.
                    </h1>
                    <p class="hero-subheadline">
                        Wynfull empowers you to master money through action — build skills in budgeting, saving, investing, and more, while celebrating every milestone.
                    </p>
                    <div class="hero-ctas">
                        <button class="btn-primary" onclick="document.getElementById('contact').scrollIntoView({behavior: 'smooth'})">
                            <i class="fas fa-rocket"></i>
                            Request Early Access
                        </button>
                        <button class="btn-secondary video-play-btn" onclick="openVideoModal()">
                            <i class="fas fa-play"></i>
                            See How It Works
                        </button>
                    </div>
                </div>

                <div class="hero-visual">
                    <div class="hero-video-preview">
                        <div class="video-preview-container">
                            <video class="hero-preview-video" muted loop playsinline>
                                <source src="{{ asset('assets/video/wynfull-finance.mp4') }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <div class="video-overlay" onclick="openVideoModal()">
                                <button class="video-play-button">
                                    <i class="fas fa-play"></i>
                                </button>
                                <div class="video-info">
                                    <h4>See Wynfull in Action</h4>
                                    <p>Watch how our platform transforms financial education</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- /hero-content -->
        </div>
    </section>

    <!-- Video Modal -->
    <div id="videoModal" class="video-modal" style="display: none;">
        <div class="video-modal-content">
            <div class="video-modal-header">
                <h3>See How Wynfull Works</h3>
                <button class="video-modal-close" onclick="closeVideoModal()">&times;</button>
            </div>
            <div class="video-container">
                <video id="modalVideo" controls>
                    <source src="{{ asset('assets/video/wynfull-finance.mp4') }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
        <div class="video-modal-overlay" onclick="closeVideoModal()"></div>
    </div>

    <!-- How Wynfull Works -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2>How Wynfull Works</h2>
                <p>Your journey to financial mastery in six simple steps</p>
                <div class="section-hero-image">
                    <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=300&fit=crop&crop=faces&auto=format&q=80"
                         alt="Diverse team working together on financial planning" class="process-image">
                </div>
            </div>
            <div class="steps-grid">

                <div class="step-card">
                    <div class="step-icon"><i class="fas fa-clipboard-list"></i></div>
                    <h3>Short Questionnaire</h3>
                    <p>Tell us about your goals, challenges, and current situation. We'll create your personalized starting point in minutes.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="fas fa-tachometer-alt"></i></div>
                    <h3>Personalized Dashboard</h3>
                    <p>Get clear steps, track micro-wins, and celebrate progress with your custom financial command center.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h3>Learning Library</h3>
                    <p>Access videos, worksheets, and templates designed for real-world application and immediate results.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="fas fa-users"></i></div>
                    <h3>AI + Real Coaching</h3>
                    <p>Get support from Wynfull AI co-researcher plus real coaches for accountability and personalized guidance.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>Track & Optimize</h3>
                    <p>Monitor your progress with real-time analytics and adjust your strategy based on what's working best for you.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="fas fa-trophy"></i></div>
                    <h3>Achieve & Celebrate</h3>
                    <p>Reach your financial milestones and unlock new goals as you build lasting wealth and financial confidence.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Strategy -->
    <section id="resources" class="resource-preview">
        <div class="container">
            <div class="section-header">
                <h2>The Wynfull 4-Phase Strategy</h2>
                <p>Strategy to help you develop personalized financial planning playbook to become a financial warrior</p>
            </div>
            <div class="tab-content active" id="phases">
                <div class="resources-grid">
                    <div class="resource-card phase-1">
                        <div class="resource-header"><div class="phase-badge">Phase 1</div></div>
                        <h3>Reset & Rewire</h3><p>Build healthy money mindsets and establish foundational habits</p>
                    </div>
                    <div class="resource-card phase-2">
                        <div class="resource-header"><div class="phase-badge">Phase 2</div></div>
                        <h3>Take Control</h3><p>Master Budgeting, manage debt, and build financial planning playbook</p>
                    </div>
                    <div class="resource-card phase-3">
                        <div class="resource-header"><div class="phase-badge">Phase 3</div></div>
                        <h3>Grow & Multiply</h3><p>Build multiple income streams, start investing, and develop investment strategies</p>
                    </div>
                    <div class="resource-card phase-4">
                        <div class="resource-header"><div class="phase-badge">Phase 4</div></div>
                        <h3>Sustain & Scale</h3><p>Advanced strategies for long-term wealth and financial freedom</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Different -->
    <section class="why-different">
        <div class="container">
            <div class="section-header">
                <h2>Why Wynfull is Different</h2>
                <p>Three key advantages that set us apart from other financial platforms</p>
            </div>
            <div class="difference-grid">
                <div class="difference-card">
                    <div class="difference-icon"><i class="fas fa-rocket"></i></div>
                    <h3>Action-First Approach</h3>
                    <p>You don't just learn; you take steps that create results.</p>
                </div>
                <div class="difference-card">
                    <div class="difference-icon"><i class="fas fa-user-friends"></i></div>
                    <h3>Personalized Coaching</h3>
                    <p>Coaches guide you through your unique journey.</p>
                </div>
                <div class="difference-card">
                    <div class="difference-icon"><i class="fas fa-brain"></i></div>
                    <h3>Smart Tools + AI</h3>
                    <p>Practical calculators, planners, and insights always at your fingertips.</p>
                </div>
            </div>

            <div class="why-different-cta">
                <p class="why-different-description">
                    We're redefining financial coaching with clarity, self-action,<br>
                    and real results - so you can build confidence, take control,<br>
                    and grow your wealth step by step.
                </p>
                <a href="#contact" class="cta-button movement-button">Join the Movement</a>
                <a href="#how-it-works" class="cta-button secondary-button">See How it Works</a>
            </div>
        </div>
    </section>

    <!-- Dual Signup Forms -->
    <section id="contact" class="signup-section">
        <div class="container">
            <div class="section-header">
                <h2>Join the Wynfull Community</h2>
                <p>Whether you're ready to transform your finances or help others do the same</p>
                <div class="signup-hero-images">
                    <div class="signup-image-left">
                        <img src="{{ asset('assets/img/join-the-community-1.jpeg') }}" alt="Diverse community working together on financial goals" class="signup-lifestyle-image">
                    </div>
                    <div class="signup-image-right">
                        <img src="{{ asset('assets/img/join-the-community-2.jpeg') }}" alt="Financial coaching and mentorship session" class="signup-lifestyle-image">
                    </div>
                </div>
            </div>

            <div class="signup-container">
                <div class="signup-tabs">
                    <button class="signup-tab active" data-form="client">For Clients</button>
                    <button class="signup-tab" data-form="coach">For Coaches</button>
                </div>

                {{-- CLIENT FORM --}}
                <div class="form-container active" id="client-form">
                    <div class="form-header">
                        <h3>Start Your Financial Journey</h3>
                        <p>Get early access to Wynfull Finance and begin building your financial future today.</p>
                    </div>

                    <form class="signup-form" name="client-signup" method="POST" action="{{ route('register.client') }}">
                        @csrf

                        <div class="form-row">
                            <div class="form-group">
                                <label for="client-name">Full Name</label>
                                <input type="text" id="client-name" name="name" value="{{ old('name') }}" required autocomplete="name">
                                @error('name')<small class="text-red-600 danger-text">{{ $message }}</small>@enderror
                            </div>
                            <div class="form-group">
                                <label for="client-email">Email Address</label>
                                <input type="email" id="client-email" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')<small class="text-red-600 danger-text">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client-goal">Primary Financial Goal</label>
                            <select id="client-goal" name="goal" required>
                                <option value="">Select your main goal</option>
                                <option value="debt-payoff"      @selected(old('goal')==='debt-payoff')>Pay Off or Reduce Debt</option>
                                <option value="emergency-fund"   @selected(old('goal')==='emergency-fund')>Build an Emergency Fund</option>
                                <option value="save-house"       @selected(old('goal')==='save-house')>Save for a Big Purchase (Home, Car, Travel)</option>
                                <option value="invest"           @selected(old('goal')==='invest')>Start Investing or Invest More</option>
                                <option value="retirement"       @selected(old('goal')==='retirement')>Grow Wealth for Retirement/Financial Independence</option>
                                <option value="other"            @selected(old('goal')==='other')>Other</option>
                            </select>
                            @error('goal')<small class="text-red-600 danger-text">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group" id="other-goal-group" style="display: {{ old('goal')==='other' ? 'block' : 'none' }};">
                            <label for="other-goal">Please specify your financial goal</label>
                            <input type="text" id="other-goal" name="other-goal" value="{{ old('other-goal') }}" placeholder="Describe your specific financial goal..." style="width: 100%;">
                            @error('other-goal')<small class="text-red-600 danger-text">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="community">Community Affiliation (Optional)</label>
                            <select id="community" name="community">
                                <option value="">Select if applicable</option>
                                <option value="military"            @selected(old('community')==='military')>Veteran / Active Duty / Military Family</option>
                                <option value="civilian"            @selected(old('community')==='civilian')>Civilian / General Public</option>
                                <option value="prefer-not-to-say"   @selected(old('community')==='prefer-not-to-say')>Prefer not to say</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="client-notes">Additional Notes (Optional)</label>
                            <textarea id="client-notes" name="notes" rows="3" placeholder="Tell us about your current situation or specific challenges...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="accept" {{ old('accept') ? 'checked' : '' }} required>
                                <span class="checkmark"></span>
                                I agree to receive updates about Wynfull Finance and understand I can unsubscribe at any time.
                            </label>
                            @error('accept')<small class="text-red-600 danger-text">{{ $message }}</small>@enderror
                        </div>

                        <button type="submit" class="btn-primary full-width">Request Early Access</button>
                    </form>
                </div>

                {{-- COACH FORM --}}
                <div class="form-container" id="coach-form">
                    <div class="form-header">
                        <h3>Join Our Coach Network</h3>
                        <p>Help build financial warriors and grow your coaching practice with Wynfull's platform.</p>
                    </div>

                    <form class="signup-form" name="coach-signup" method="POST" action="{{ route('register.coach') }}">
                        @csrf

                        <div class="form-row">
                            <div class="form-group">
                                <label for="coach-name">Full Name</label>
                                <input type="text" id="coach-name" name="name" value="{{ old('name') }}" required autocomplete="name">
                                @error('name')<small class="text-red-600 danger-text">{{ $message }}</small>@enderror
                            </div>
                            <div class="form-group">
                                <label for="coach-email">Email Address</label>
                                <input type="email" id="coach-email" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')<small class="text-red-600 danger-text">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="experience">Experience Level</label>
                            <select id="experience" name="experience" required>
                                <option value="">Select your experience</option>
                                <option value="1-3" @selected(old('experience')==='1-3')>1-3 Years Experience</option>
                                <option value="3-5" @selected(old('experience')==='3-5')>3-5 Years Experience</option>
                                <option value="5+" @selected(old('experience')==='5+')>5+ Years Experience</option>
                            </select>
                            @error('experience')<small class="text-red-600 danger-text">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group">
                            <label for="specialties">Specialties (Select all that apply)</label>
                            <div class="checkbox-grid">
                                @php $spec = collect(old('specialties', [])); @endphp
                                <label class="checkbox-label">
                                    <input type="checkbox" name="specialties[]" value="financial-coaching" {{ $spec->contains('financial-coaching') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Financial Coaching
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="specialties[]" value="life-coaching" {{ $spec->contains('life-coaching') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Life Coaching
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="specialties[]" value="accountability-coaching" {{ $spec->contains('accountability-coaching') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Accountability Coaching
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="specialties[]" value="mindset-motivation" {{ $spec->contains('mindset-motivation') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Mindset & Motivation Coaching
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="specialties[]" value="other" id="specialty-other" {{ $spec->contains('other') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Other (please specify)
                                </label>
                            </div>
                            @error('specialties')<small class="text-red-600 danger-text">{{ $message }}</small>@enderror>
                        </div>

                        <div class="form-group" id="other-specialty-group" style="display: {{ collect(old('specialties', []))->contains('other') ? 'block' : 'none' }};">
                            <label for="other-specialty">Please specify your specialty</label>
                            <input type="text" id="other-specialty" name="other-specialty" value="{{ old('other-specialty') }}" placeholder="Describe your specialty..." style="width: 100%;">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="linkedin">LinkedIn Profile (Optional)</label>
                                <input type="url" id="linkedin" name="linkedin" value="{{ old('linkedin') }}" placeholder="https://linkedin.com/in/yourprofile" autocomplete="url">
                                @error('linkedin')<small class="text-red-600 danger-text">{{ $message }}</small>@enderror
                            </div>
                            <div class="form-group">
                                <label for="website">Website (Optional)</label>
                                <input type="url" id="website" name="website" value="{{ old('website') }}" placeholder="https://yourwebsite.com" autocomplete="url">
                                @error('website')<small class="text-red-600 danger-text">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="accept" {{ old('accept') ? 'checked' : '' }} required>
                                <span class="checkmark"></span>
                                I'm interested in joining the Wynfull coach network and agree to be contacted about opportunities.
                            </label>
                            @error('accept')<small class="text-red-600 danger-text">{{ $message }}</small>@enderror
                        </div>

                        <button type="submit" class="btn-primary full-width">Apply to Coach Network</button>
                    </form>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-left">
                    <div class="footer-logo">
                        <div class="logo-icon">
                            <img src="{{ asset('assets/img/wynfull-logo.png') }}" alt="Wynfull Finance Logo" class="logo-image">
                        </div>
                        <div class="logo-text">
                            <h3>Wynfull Finance</h3>
                            <span>Building Financial Warriors for Life</span>
                        </div>
                    </div>
                </div>
                <div class="footer-right">
                    <div class="footer-links">
                        <a href="#" onclick="openPrivacyModal()">Privacy Policy</a>
                        <a href="#" onclick="openTermsModal()">Terms of Service</a>
                        <a href="#contact">Join the Community</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ now()->year }} Wynfull Finance. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Privacy Policy Modal -->
    <div id="privacyModal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h2>Privacy Policy</h2>
                <button class="modal-close" onclick="closePrivacyModal()">&times;</button>
            </div>
            <div class="modal-content">
                <div class="privacy-content">
                    <p><strong>Effective Date:</strong> September 22, 2025</p>
                    <p>Wynfull Finance ("we," "our," "us") values your privacy. This Privacy Policy explains how we collect, use, disclose, and protect your information when you use our website and services.</p>

                    <h4>1. Information We Collect</h4>
                    <ul>
                        <li><strong>Personal Data:</strong> Name, email address, payment information (if applicable).</li>
                        <li><strong>Usage Data:</strong> IP address, browser type, device identifiers, and site activity.</li>
                        <li><strong>Cookies:</strong> We use cookies and tracking technologies for analytics and functionality.</li>
                    </ul>

                    <h4>2. How We Use Your Information</h4>
                    <p>We use data to provide services, process payments, send updates, improve the website, and personalize your experience.</p>

                    <h4>3. Sharing of Data</h4>
                    <p>We may share data with trusted third parties such as payment processors, analytics providers, and service partners. We may also disclose data when required by law.</p>

                    <h4>4. Data Retention</h4>
                    <p>We retain personal data as long as necessary to provide our services or comply with legal obligations.</p>

                    <h4>5. Data Security</h4>
                    <p>We take reasonable measures to protect your data against unauthorized access or misuse.</p>

                    <h4>6. Your Rights</h4>
                    <p>Depending on your location, you may have rights to access, update, or delete your personal data, and to opt out of marketing communications.</p>

                    <h4>7. Children's Privacy</h4>
                    <p>Our services are not directed to children under 13. We do not knowingly collect personal data from minors.</p>

                    <h4>8. International Transfers</h4>
                    <p>Your data may be transferred to and stored in countries outside your own. We implement safeguards to protect this data.</p>

                    <h4>9. Changes to this Policy</h4>
                    <p>We may update this Privacy Policy. Changes will be posted with an updated effective date.</p>

                    <h4>10. Contact Us</h4>
                    <p>For questions, contact us at: <a href="mailto:info@wynfull.com">info@wynfull.com</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms of Service Modal -->
    <div id="termsModal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h2>Terms of Service</h2>
                <button class="modal-close" onclick="closeTermsModal()">&times;</button>
            </div>
            <div class="modal-content">
                <div class="privacy-content">
                    <p><strong>Effective Date:</strong> September 22, 2025</p>
                    <p>These Terms of Service ("Terms") govern your use of Wynfull Finance's website and services. By accessing or using our services, you agree to be bound by these Terms.</p>

                    <h4>1. Services</h4>
                    <p>Wynfull Finance provides financial coaching, resources, and tools to support users in making informed financial decisions.</p>

                    <h4>2. User Obligations</h4>
                    <p>You agree to provide accurate information, comply with all applicable laws, and not misuse our services.</p>

                    <h4>3. Accounts</h4>
                    <p>If you create an account, you are responsible for maintaining its confidentiality and for all activity under your account.</p>

                    <h4>4. Payments</h4>
                    <p>If paid services are offered, you agree to pay fees as described. Payment terms, billing frequency, and refund policies will be specified at checkout.</p>

                    <h4>5. Intellectual Property</h4>
                    <p>All content, branding, and materials provided by Wynfull Finance are owned by us and protected by copyright law.</p>

                    <h4>6. Financial Disclaimer</h4>
                    <p>Wynfull Finance provides educational resources only. We are not licensed financial advisors, and nothing provided should be considered personalized financial, tax, or legal advice. Users are responsible for their financial decisions.</p>

                    <h4>7. Limitation of Liability</h4>
                    <p>We are not liable for damages or losses resulting from your use of our services, to the maximum extent permitted by law.</p>

                    <h4>8. Indemnification</h4>
                    <p>You agree to indemnify and hold Wynfull Finance harmless from claims or damages arising from your misuse of the service or violation of these Terms.</p>

                    <h4>9. Termination</h4>
                    <p>We may suspend or terminate your access if you violate these Terms or misuse the service.</p>

                    <h4>10. Governing Law</h4>
                    <p>These Terms are governed by the laws of the State of Florida, United States.</p>

                    <h4>11. Changes to Terms</h4>
                    <p>We may update these Terms from time to time. Continued use of our services indicates acceptance of the new Terms.</p>

                    <h4>12. Contact</h4>
                    <p>For questions, contact us at: <a href="mailto:info@wynfull.com">info@wynfull.com</a></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/landing.js') }}"></script>
@endpush
