@extends('layouts.client')
@section('title', 'Client Dashboard')


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
                            <h2>Phase Status</h2>
                            <div class="phase-chart">
                                <div class="phase-bar-container">
                                    <div class="phase-bar reset-rewire"></div>
                                    <div class="phase-bar take-control"></div>
                                    <div class="phase-bar grow-multiply"></div>
                                    <div class="phase-bar sustain-scale"></div>
                                </div>
                                <div class="phase-labels">
                                    <span class="phase-label">Reset & Rewire</span>
                                    <span class="phase-label">Grow & Multiply</span>
                                </div>
                            </div>
                        </div>

                        <div class="primary-goals-card">
                            <h2>Primary Goals</h2>
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
                            <h3>Confidence Score</h3>
                            <div class="confidence-circle">
                                <div class="confidence-progress">
                                    <span class="confidence-value">{{ $confidence['score'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="metric-card debt-progress-card">
                            <h3>Debt Journey</h3>
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
                            <h3>Financial Knowledge</h3>
                            <div class="knowledge-content">
                                <div class="knowledge-level">
                                    <div class="knowledge-icon">
                                        <i class="fas fa-brain"></i>
                                    </div>
                                    <div class="knowledge-info">
                                        <span class="knowledge-text">{{ $financialKnowledge['experience_label'] ?? '' }}</span>
                                        <x-knowledge-dots :score="$financialKnowledge['score'] ?? 0" />
                                        <span class="knowledge-subtitle">Growing your expertise</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="metric-card emergency-fund-card">
                            <h3>Emergency Fund</h3>
                            <div class="fund-amount">{{ $wealthCards['saved_display'] ?? ''}}</div>
                            <div class="fund-progress">
                                <div class="fund-bar">
                                    <div class="fund-fill"></div>
                                </div>
                            </div>
                        </div>

                        <div class="metric-card investing-card">
                            <h3>Investing</h3>
                            <div class="investing-content">
                                <span class="investing-label">Contribution score</span>
                                <span class="investing-status">Just starting</span>
                                <div class="investing-progress">
                                    <div class="investing-bar">
                                        <div class="investing-fill"></div>
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
                                <p>Based on your spending patterns, you could save an additional $200/month by optimizing your subscription services.</p>
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
                            <p id="tool-description">Learn how to use this tool effectively with our step-by-step walkthrough.</p>
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
        <div class="notification-list">
            <div class="notification-item unread">
                <div class="notification-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="notification-content">
                    <h4>Portfolio Update</h4>
                    <p>Your investments are up 2.3% this week</p>
                    <time>2 hours ago</time>
                </div>
            </div>
            <div class="notification-item unread">
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="notification-content">
                    <h4>Goal Milestone</h4>
                    <p>You're 75% towards your emergency fund goal!</p>
                    <time>1 day ago</time>
                </div>
            </div>
            <div class="notification-item">
                <div class="notification-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="notification-content">
                    <h4>Coaching Session</h4>
                    <p>Reminder: Session with Sarah Chen tomorrow at 2 PM</p>
                    <time>2 days ago</time>
                </div>
            </div>
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
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/script.js') }}" defer></script>
@endpush
