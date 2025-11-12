@extends('layouts.client')
@section('title', 'Client Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ready to continue your financial journey?')
@section('breadcrumb', 'Client Dashboard')


@section('content')

    <!-- Start Here Page -->
    <!-- Dashboard Page -->
    <div class="active" id="dashboard">
        <div class="page-content">
            <div class="page-header">
                <h1>Welcome back, {{ $user->name }}! 👋</h1>
                <p>Ready to continue your financial journey?</p>
            </div>

            <div class="dashboard-top-section">
                <div class="phase-status-card">
                    <div class="card-header-with-info">
                        <h2>Phase Status</h2>
                        <div class="info-indicator" data-tooltip="phase-status">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                    <div class="phase-chart">
                        @php
                            $barStates = [
                               'reset-rewire' => '',
                               'take-control' => '',
                               'grow-multiply' => '',
                               'sustain-scale' => '',
                           ];

                        $clientAnswers = is_array($financialSituations) ? $financialSituations : (array)$financialSituations;
                                    if (in_array('struggling with debt', $clientAnswers)) {
                                        $barStates['reset-rewire'] = 'active';
                                    }
                                    if (in_array('paycheck to paycheck', $clientAnswers) || in_array('okay not saving', $clientAnswers)) {
                                        $barStates['take-control'] = 'active';
                                    }
                                    if (in_array('saving regularly', $clientAnswers) || in_array('okay not saving', $clientAnswers)) {
                                        $barStates['grow-multiply'] = 'active';
                                    }
                                    if (in_array('confident and focused', $clientAnswers)) {
                                        $barStates['sustain-scale'] = 'active';
                                    }

                                    $labelStates = $barStates;
                        @endphp
                        <div class="phase-bar-container">
                            <div class="phase-bar reset-rewire {{ $barStates['reset-rewire'] }}"></div>
                            <div class="phase-bar take-control {{ $barStates['take-control'] }}"></div>
                            <div class="phase-bar grow-multiply {{ $barStates['grow-multiply'] }}"></div>
                            <div class="phase-bar sustain-scale {{ $barStates['sustain-scale'] }}"></div>
                        </div>
                        <div class="phase-labels">
                            <span class="phase-label {{ $labelStates['reset-rewire'] }}">Reset</span>
                            <span class="phase-label {{ $labelStates['take-control'] }}">Control</span>
                            <span class="phase-label {{ $labelStates['grow-multiply'] }}">Grow</span>
                            <span class="phase-label {{ $labelStates['sustain-scale'] }}">Sustain</span>
                        </div>
                    </div>
                </div>

                <div class="primary-goals-card">
                    <div class="card-header-with-info">
                        <h2>Primary Goals</h2>
                        <div class="info-indicator" data-tooltip="primary-goals">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                    <ul class="goals-list">
                        @forelse($pickedGoals as $goal)
                            <li>{{ $goal }}</li>
                        @empty
                            <li>No goal selected yet</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="metric-card confidence-card">
                    <div class="card-header-with-info">
                        <h3>Confidence Score</h3>
                        <div class="info-indicator" data-tooltip="confidence-score">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                    <div class="confidence-circle">
                        @php
                            $score = $confidence['score'] ?? 0;
                            $degree = round($score * 3.6); // 1% = 3.6 degrees
                        @endphp
                        <div class="confidence-progress" style="background-image: conic-gradient(var(--success-green) 0deg {{ $degree }}deg, #E5E7EB {{ $degree }}deg 360deg)">
                            <span class="confidence-value">{{ $score }}</span>
                        </div>
                    </div>
                </div>

                <div class="metric-card debt-progress-card">
                    <div class="card-header-with-info">
                        <h3>Debt Journey</h3>
                        <div class="info-indicator" data-tooltip="debt-journey">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                    <div class="debt-status">
                        <div class="debt-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="debt-content">
                            <span class="debt-text">{{ $journey['label'] ?? '' }}</span>
                            <div class="debt-progress-bar">
                                <div class="debt-progress-fill" style="width: {{ $journey['score'] ?? 0 }}%"></div>
                            </div>
                            <span class="debt-subtitle">{{ $journey['badge']['text'] ?? '' }}</span>
                        </div>
                    </div>
                </div>

                <div class="metric-card knowledge-card">
                    <div class="card-header-with-info">
                        <h3>Investing Knowledge</h3>
                        <div class="info-indicator" data-tooltip="investing-knowledge">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                    <div class="knowledge-content">
                        <div class="knowledge-level">
                            <div class="knowledge-icon">
                                <i class="fas fa-brain"></i>
                            </div>
                            <div class="knowledge-info">
                                <span class="knowledge-text">{{ $financialKnowledge['experience_label'] ?? '' }}</span>
                                <x-knowledge-dots :score="$financialKnowledge['score'] ?? 0"/>
                                <span class="knowledge-subtitle">Growing your expertise</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="metric-card emergency-fund-card">
                    <div class="card-header-with-info">
                        <h3>Emergency Fund</h3>
                        <div class="info-indicator" data-tooltip="emergency-fund">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                    <div class="fund-amount">{{ $wealthCards['saved_display'] ?? ''}}</div>
                    <div class="fund-progress">
                        <div class="fund-bar">
                            <div class="fund-fill"></div>
                        </div>
                    </div>
                </div>

                @php
                    $investingWidth = '15%'; // Default for 'Not yet investing'
                    $investingLabel = $investingStatus['label'] ?? 'Not yet investing';
                    if ($investingLabel === 'Just starting') {
                        $investingWidth = '50%';
                    } elseif ($investingLabel === 'Investing consistently') {
                        $investingWidth = '100%';
                    }
                @endphp
                <div class="metric-card investing-card">
                    <div class="card-header-with-info">
                        <h3>Investing</h3>
                        <div class="info-indicator" data-tooltip="investing-contribution">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                    <div class="investing-content">
                        <span class="investing-label">Contribution score</span>
                        <span class="investing-status">{{ $investingStatus['label'] ?? '' }}</span>
                        <div class="investing-progress">
                            <div class="investing-bar">
                                <div class="investing-fill" style="width: {{ $investingWidth ?? 0 }};"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-sections two-columns">
                <div class="ai-insights">
                    <h2>AI Insights</h2>
                    <div class="insight-card">
                        <i class="fas fa-lightbulb"></i>
                        <p>Based on your spending patterns, you could save an additional $200/month by optimizing your
                            subscription services.</p>
                    </div>
                </div>

                <div class="recent-activity">
                    <h2>Recent Activity</h2>
                    <div class="activity-list">
                        @forelse($activities as $a)
                            @php
                                $event = (string) ($a->event ?? '');
                                $desc  = trim((string) ($a->description ?? ''));
                                $props = $a->properties ?? collect();

                                // Friendly text fallback
                                $text = $desc !== '' ? $desc : ($event !== '' ? ucfirst(str_replace(['.', '-', '_'], ' ', $event)) : ucfirst($a->log_name ?? 'activity'));

                                // Optional richer messages based on common events you log
                                if ($event === 'clients.card.emergency_fund.view' && isset($props['progress'])) {
                                    // e.g., "Emergency fund progress: 35%"
                                    $text = 'Emergency fund progress: ' . (int)$props['progress'] . '%';
                                } elseif ($event === 'clients.card.investing.view' && isset($props['score'])) {
                                    // e.g., "Investing contribution score: 72"
                                    $text = 'Investing contribution score: ' . (int)$props['score'];
                                } elseif ($event === 'onboarding_completed') {
                                    $text = 'Completed onboarding';
                                } elseif (str_contains($event, 'logout')) {
                                    $text = 'Signed out';
                                }

                                // Icon map
                                $icon = match (true) {
                                    str_contains($event, 'emergency_fund') => 'fas fa-piggy-bank',
                                    str_contains($event, 'invest')         => 'fas fa-chart-line',
                                    str_contains($event, 'primary_goals')  => 'fas fa-bullseye',
                                    str_contains($event, 'onboarding')     => 'fas fa-check-circle',
                                    str_contains($event, 'login')          => 'fas fa-sign-in-alt',
                                    str_contains($event, 'logout')         => 'fas fa-sign-out-alt',
                                    default                                => 'fas fa-clipboard-list',
                                };
                            @endphp
                            <div class="activity-item">
                                <i class="{{ $icon }}"></i>
                                <span>{{ $text }}</span>
                                <time>{{ $a->created_at?->timezone(config('app.timezone'))->diffForHumans() }}</time>
                            </div>
                        @empty
                            <div class="activity-item">
                                <i class="fas fa-clipboard-list"></i>
                                <span>No recent activity yet</span>
                                <time>—</time>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Download Format Popup -->
    <div id="download-format-popup" class="download-popup-overlay">
        <div class="download-popup">
            <div class="download-popup-header">
                <h3>Choose Download Format</h3>
                <button class="download-popup-close">&times;</button>
            </div>
            <div class="download-popup-content">
                <p>Select the format you'd like to download:</p>
                <div class="format-options">
                    <button class="format-option" data-format="pdf">
                        <i class="fas fa-file-pdf"></i>
                        <span>PDF</span>
                    </button>
                    <button class="format-option" data-format="docx">
                        <i class="fas fa-file-word"></i>
                        <span>Word Document</span>
                    </button>
                    <button class="format-option" data-format="excel">
                        <i class="fas fa-file-excel"></i>
                        <span>Excel Spreadsheet</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Walkthrough Video Sidebar -->
    <div id="walkthrough-sidebar" class="walkthrough-sidebar">
        <div class="walkthrough-header">
            <h3 id="walkthrough-title">Tool Walkthrough</h3>
            <button class="walkthrough-close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="walkthrough-content">
            <div class="video-container">
                <video id="walkthrough-video" controls poster="">
                    <source src="" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>

            <div class="walkthrough-details">
                <div class="tool-overview">
                    <h4>Overview</h4>
                    <p id="tool-description">Learn how to use this tool effectively with our step-by-step
                        walkthrough.</p>
                </div>

                <div class="key-features">
                    <h4>Key Features</h4>
                    <ul id="tool-features">
                        <li>Feature 1</li>
                        <li>Feature 2</li>
                        <li>Feature 3</li>
                    </ul>
                </div>

                <div class="getting-started">
                    <h4>Getting Started</h4>
                    <ol id="tool-steps">
                        <li>Step 1</li>
                        <li>Step 2</li>
                        <li>Step 3</li>
                    </ol>
                </div>

                <div class="walkthrough-actions">
                    <button class="btn-primary download-after-video">
                        <i class="fas fa-download"></i>
                        Download Template
                    </button>
                    <button class="btn-secondary replay-video">
                        <i class="fas fa-redo"></i>
                        Replay Video
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay"></div>


    <!-- Notification Panel -->
    <div class="notification-panel" id="notificationPanel">
        <div class="notification-header">
            <h3>Notifications</h3>
            <button class="close-notifications" id="closeNotifications">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="notification-list" id="notificationList">
            <div class="notification-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading notifications...</p>
            </div>
        </div>
        <div class="notification-footer">
            <button class="mark-all-read-btn" id="markAllReadBtn">Mark All as Read</button>
        </div>
    </div>

    @if(auth()->user()->hasRole('client'))
        <script>
            window.WYNFULL = window.WYNFULL || {};
            window.WYNFULL.onboardingCompleted = @json((bool)auth()->user()->onboarding_completed);
            window.WYNFULL.onboardingCompleteRoute = @json(route('onboarding.complete'));
            window.WYNFULL.csrfToken = @json(csrf_token());
        </script>

        @include('client.partials.onboarding-modal')
    @endif

    <!-- Welcome Modal -->
    <div class="modal-overlay" id="welcomeModal" style="display: none;">
        <div class="modal welcome-modal">
            <div class="welcome-header">
                <div class="welcome-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2>Welcome to Your Financial Journey!</h2>
                <p>Your personalized dashboard is ready</p>
            </div>
            <div class="welcome-content">
                <div class="welcome-summary">
                    <h3>Your Profile Summary</h3>
                    <div class="summary-section">
                        <div class="summary-item">
                            <strong>Financial Situation:</strong>
                            <span id="summary-financial-situation"></span>
                        </div>
                        <div class="summary-item">
                            <strong>Primary Goal:</strong>
                            <span id="summary-primary-goal"></span>
                        </div>
                        <div class="summary-item">
                            <strong>Confidence Level:</strong>
                            <span id="summary-confidence-level"></span>
                        </div>
                        <div class="summary-item">
                            <strong>Debt Feelings:</strong>
                            <span id="summary-debt-feelings"></span>
                        </div>
                        <div class="summary-item">
                            <strong>Savings Amount:</strong>
                            <span id="summary-savings-amount"></span>
                        </div>
                        <div class="summary-item">
                            <strong>Investing Experience:</strong>
                            <span id="summary-investing-experience"></span>
                        </div>
                        <div class="summary-item">
                            <strong>Learning Style:</strong>
                            <span id="summary-learning-style"></span>
                        </div>
                    </div>
                </div>
                <div class="welcome-features">
                    <div class="feature-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Personalized progress tracking</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-target"></i>
                        <span>Goal-focused recommendations</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-brain"></i>
                        <span>AI-powered insights</span>
                    </div>
                </div>
                <p class="welcome-message">
                    Based on your responses, we've customized your dashboard to help you achieve your financial goals.
                    Let's start building your wealth together!
                </p>
            </div>
            <div class="welcome-actions">
                <button class="btn-primary" onclick="closeWelcomeModal()">
                    <i class="fas fa-rocket"></i>
                    Let's Get Started!
                </button>
            </div>
        </div>
    </div>

    <!-- Info Tooltips -->
    <div class="tooltip-container" id="tooltip-container" style="display: none;">
        <div class="tooltip-content" id="tooltip-content"></div>
        <div class="tooltip-arrow"></div>
    </div>
@endsection

@push('scripts')
<script>
function closeWelcomeModal() {
    const welcomeModal = document.getElementById('welcomeModal');
    if (welcomeModal) {
        welcomeModal.style.display = 'none';
        // Store that the user has seen the welcome modal
        localStorage.setItem('welcomeModalSeen', 'true');
    }
}

// Tooltip functionality
const tooltipData = {
    'phase-status': {
        title: 'Phase Status',
        content: `<strong>Purpose:</strong><br>
        Shows where you are across the four Wynfull phases — Reset & Rewire, Take Control, Grow & Multiply, and Sustain & Scale.<br><br>
        
        <strong>How to Read It:</strong><br>
        • The height of each bar reflects where your attention is most needed.<br>
        • Bar heights are determined by your answers to the onboarding questionnaire.<br>
        • Each phase corresponds to your current financial situation:<br>
        &nbsp;&nbsp;- Reset & Rewire: "Struggling with debt"<br>
        &nbsp;&nbsp;- Take Control: "Living paycheck to paycheck" or "Doing okay but not saving much"<br>
        &nbsp;&nbsp;- Grow: "Saving regularly and want to invest"<br>
        &nbsp;&nbsp;- Sustain: "Confident and focused on long-term wealth"<br>
        &nbsp;&nbsp;- Other: All bars will appear at equal height.<br><br>
        
        <strong>Action Tip:</strong><br>
        Focus your next steps and conversations on the phase with the highest bar — that's where your current financial journey is centered.`
    },
    'primary-goals': {
        title: 'Primary Goals',
        content: `<strong>Purpose:</strong><br>
        Displays your main focus areas for this pilot phase of your financial journey.<br><br>
        
        <strong>How to Read It:</strong><br>
        • These goals are automatically set based on your onboarding questionnaire.<br>
        • They can be updated anytime in collaboration with your coach.<br><br>
        
        <strong>Action Tip:</strong><br>
        Use your goals as your North Star — revisit and adjust as you hit milestones or refine your priorities.`
    },
    'confidence-score': {
        title: 'Confidence Score',
        content: `<strong>Purpose:</strong><br>
        Reflects how confident you feel managing your personal finances.<br><br>
        
        <strong>Scale:</strong><br>
        25 (Low) → 100 (High)<br><br>
        
        <strong>How to Read It:</strong><br>
        • A lower score indicates financial stress or uncertainty.<br>
        • A higher score represents clarity, control, and progress.<br><br>
        
        <strong>Action Tip:</strong><br>
        Your confidence grows through habit — track your progress, build consistency, and celebrate small wins.`
    },
    'debt-journey': {
        title: 'Debt Journey',
        content: `<strong>Purpose:</strong><br>
        Tracks your current stage in managing or paying off debt.<br><br>
        
        <strong>Stages:</strong><br>
        Overwhelmed → Managing → Taking Control → Debt-Free<br><br>
        
        <strong>How to Read It:</strong><br>
        • The progress bar shows how far you've moved toward full debt control.<br>
        • Your stage is updated based on questionnaire responses and future progress inputs.<br><br>
        
        <strong>Action Tip:</strong><br>
        Use your Wynfull Debt Tracker and coaching sessions to reflect on progress and strategies. Each milestone moves you closer to financial peace.`
    },
    'investing-knowledge': {
        title: 'Investing Knowledge',
        content: `<strong>Purpose:</strong><br>
        Shows your current level of understanding and experience with investing.<br><br>
        
        <strong>Levels:</strong><br>
        Beginner (1–2/5) → Intermediate (3–4/5) → Expert (5/5)<br><br>
        
        <strong>How to Read It:</strong><br>
        • Based on your questionnaire responses about your investing habits and knowledge.<br>
        • Updated as you complete learning modules or coaching milestones.<br><br>
        
        <strong>Action Tip:</strong><br>
        Use Wynfull's Resource Library and your coach's guidance to build confidence and move to the next investing tier.`
    },
    'emergency-fund': {
        title: 'Emergency Fund',
        content: `<strong>Purpose:</strong><br>
        Displays your current amount saved for unexpected events or emergencies.<br><br>
        
        <strong>Goal Benchmark:</strong><br>
        3–6 months of essential and priority expenses.<br><br>
        
        <strong>How to Read It:</strong><br>
        • The number reflects your estimated savings level, based on your responses or updated progress.<br>
        • You'll see this number increase as you grow your buffer fund and update your response.<br><br>
        
        <strong>Action Tip:</strong><br>
        Building an emergency fund is one of the strongest financial defenses — even small, consistent contributions make a big difference over time.`
    },
    'investing-contribution': {
        title: 'Investing (Contribution Score)',
        content: `<strong>Purpose:</strong><br>
        Tracks your consistency in contributing to investment or retirement accounts.<br><br>
        
        <strong>Scale:</strong><br>
        Not Started → Just Starting → Consistent<br><br>
        
        <strong>How to Read It:</strong><br>
        • Measures how regularly you contribute, not how much you invest.<br>
        • Based on your onboarding questionnaire and updates made through your coach or the dashboard.<br><br>
        
        <strong>Action Tip:</strong><br>
        Start with small automated investments — even $25 or $50 per paycheck builds powerful momentum. Consistency beats amount over time.`
    }
};

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const infoIndicators = document.querySelectorAll('.info-indicator');
    const tooltipContainer = document.getElementById('tooltip-container');
    const tooltipContent = document.getElementById('tooltip-content');
    
    infoIndicators.forEach(indicator => {
        indicator.addEventListener('click', function(e) {
            e.stopPropagation();
            const tooltipType = this.getAttribute('data-tooltip');
            const data = tooltipData[tooltipType];
            
            if (data) {
                tooltipContent.innerHTML = `<h4>${data.title}</h4>${data.content}`;
                showTooltip(e.target, tooltipContainer);
            }
        });
    });
    
    // Close tooltip when clicking outside
    document.addEventListener('click', function(e) {
        if (!tooltipContainer.contains(e.target) && !e.target.closest('.info-indicator')) {
            hideTooltip(tooltipContainer);
        }
    });
});

function showTooltip(trigger, tooltip) {
    const rect = trigger.getBoundingClientRect();
    const tooltipRect = tooltip.getBoundingClientRect();
    
    // Position tooltip
    tooltip.style.display = 'block';
    tooltip.style.position = 'fixed';
    tooltip.style.left = (rect.left - 200) + 'px'; // Offset to the left
    tooltip.style.top = (rect.bottom + 10) + 'px';
    
    // Adjust if tooltip goes off screen
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;
    
    if (rect.left - 200 < 0) {
        tooltip.style.left = '10px';
    }
    
    if (rect.bottom + tooltip.offsetHeight + 10 > viewportHeight) {
        tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
    }
}

function hideTooltip(tooltip) {
    tooltip.style.display = 'none';
}
</script>
@endpush

