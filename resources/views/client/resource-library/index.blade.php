@extends('layouts.client')

@section('title', 'View User Profile')
@section('page-title', 'User Profile')

@section('content')
    <!-- Resource Library Page -->
    <div id="resource-library" class="">
        <div class="page-content">
            <!-- Resource Library Header -->
            <div class="resource-header">
                <div class="resource-title">
                    <h1>Resource Library</h1>
                    <p>Your comprehensive financial education hub</p>
                </div>

                <div class="resource-controls">
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search guides, tools, or templates..." class="resource-search">
                    </div>

                    <div class="filter-controls">
                        <select class="filter-dropdown">
                            <option value="">By Phase</option>
                            <option value="reset-rewire">Reset &amp; Rewire</option>
                            <option value="take-control">Take Control</option>
                            <option value="grow-multiply">Grow &amp; Multiply</option>
                            <option value="sustain-scale">Sustain &amp; Scale</option>
                        </select>

                        <select class="filter-dropdown">
                            <option value="">By Format</option>
                            <option value="guide">Word</option>
                            <option value="excel">Excel Template</option>
                            <option value="video">Video</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="resource-tabs">
                <button class="resource-tab active" data-tab="learning-phases">
                    <i class="fas fa-graduation-cap"></i>
                    Learning Phases
                </button>
                <button class="resource-tab" data-tab="tools-templates">
                    <i class="fas fa-tools"></i>
                    Tools &amp; Templates
                </button>
            </div>

            <!-- Learning Phases Tab -->
            <div id="learning-phases" class="resource-tab-content active">
                <div class="phases-container">
                    <!-- Phase 1: Reset & Rewire -->
                    <div class="phase-card" data-phase="reset-rewire">
                        <div class="phase-header">
                            <div class="phase-icon">
                                <i class="fas fa-refresh"></i>
                            </div>
                            <div class="phase-info">
                                <h3>Reset &amp; Rewire</h3>
                                <p>Foundation building and mindset transformation for financial success</p>
                            </div>
                            <div class="phase-status completed">
                                <i class="fas fa-check-circle"></i>
                                <span>Completed</span>
                            </div>
                        </div>

                        <div class="phase-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 100%"></div>
                            </div>
                            <span class="progress-text">8/8 modules • 100% complete</span>
                        </div>

                        <div class="phase-modules">
                            <div class="module-item completed">
                                <div class="module-info">
                                    <h4>Money Mindset Transformation</h4>
                                    <p>Overcome limiting beliefs about money</p>
                                </div>
                                <div class="module-resources">
                                    <span class="resource-tag guide">Word Guide</span>
                                    <span class="resource-tag video">Video</span>
                                </div>
                                <button class="module-btn">Open Resource</button>
                            </div>

                            <div class="module-item completed">
                                <div class="module-info">
                                    <h4>Financial Values Assessment</h4>
                                    <p>Identify your core financial values and priorities</p>
                                </div>
                                <div class="module-resources">
                                    <span class="resource-tag guide">Word Guide</span>
                                    <span class="resource-tag excel">Excel Tool</span>
                                </div>
                                <button class="module-btn">Open Resource</button>
                            </div>

                            <div class="module-item completed">
                                <div class="module-info">
                                    <h4>Goal Setting Framework</h4>
                                    <p>Create SMART financial goals that motivate action</p>
                                </div>
                                <div class="module-resources">
                                    <span class="resource-tag guide">Word Guide</span>
                                    <span class="resource-tag excel">Excel Tool</span>
                                    <span class="resource-tag video">Video</span>
                                </div>
                                <button class="module-btn">Open Resource</button>
                            </div>
                        </div>
                    </div>

                    <!-- Phase 2: Take Control -->
                    <div class="phase-card current" data-phase="take-control">
                        <div class="phase-header">
                            <div class="phase-icon">
                                <i class="fas fa-hand-holding-dollar"></i>
                            </div>
                            <div class="phase-info">
                                <h3>Take Control</h3>
                                <p>Master budgeting, saving, and debt management fundamentals</p>
                            </div>
                            <div class="phase-status current">
                                <i class="fas fa-play-circle"></i>
                                <span>In Progress</span>
                            </div>
                        </div>

                        <div class="phase-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 68%"></div>
                            </div>
                            <span class="progress-text">7/12 modules • 68% complete</span>
                        </div>

                        <div class="phase-modules">
                            <div class="module-item completed">
                                <div class="module-info">
                                    <h4>Budget Creation Masterclass</h4>
                                    <p>Build a realistic budget that actually works</p>
                                </div>
                                <div class="module-resources">
                                    <span class="resource-tag guide">Word Guide</span>
                                    <span class="resource-tag excel">Excel Tool</span>
                                    <span class="resource-tag video">Video</span>
                                </div>
                                <button class="module-btn">Open Resource</button>
                            </div>

                            <div class="module-item completed">
                                <div class="module-info">
                                    <h4>Emergency Fund Planning</h4>
                                    <p>Calculate and build your financial safety net</p>
                                </div>
                                <div class="module-resources">
                                    <span class="resource-tag guide">Word Guide</span>
                                    <span class="resource-tag excel">Excel Tool</span>
                                </div>
                                <button class="module-btn">Open Resource</button>
                            </div>

                            <div class="module-item current">
                                <div class="module-info">
                                    <h4>Debt Elimination Strategies</h4>
                                    <p>Proven methods to eliminate debt faster</p>
                                </div>
                                <div class="module-resources">
                                    <span class="resource-tag guide">Word Guide</span>
                                    <span class="resource-tag excel">Excel Tool</span>
                                    <span class="resource-tag video">Video</span>
                                </div>
                                <button class="module-btn primary">Continue Learning</button>
                            </div>

                            <div class="module-item">
                                <div class="module-info">
                                    <h4>Credit Score Optimization</h4>
                                    <p>Understand and improve your credit profile</p>
                                </div>
                                <div class="module-resources">
                                    <span class="resource-tag guide">Word Guide</span>
                                    <span class="resource-tag excel">Excel Tool</span>
                                </div>
                                <button class="module-btn">Start Module</button>
                            </div>
                        </div>
                    </div>

                    <!-- Phase 3: Grow & Multiply -->
                    <div class="phase-card locked" data-phase="grow-multiply">
                        <div class="phase-header">
                            <div class="phase-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="phase-info">
                                <h3>Grow &amp; Multiply</h3>
                                <p>Investment strategies and wealth building techniques</p>
                            </div>
                            <div class="phase-status locked">
                                <i class="fas fa-lock"></i>
                                <span>Locked</span>
                            </div>
                        </div>

                        <div class="phase-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                            <span class="progress-text">0/15 modules • Complete "Take Control" to unlock</span>
                        </div>

                        <div class="phase-modules locked">
                            <div class="module-item locked">
                                <div class="module-info">
                                    <h4>Investing Fundamentals Toolkit</h4>
                                    <p>Beginner-friendly guide + growth calculator to start investing</p>
                                </div>
                                <div class="module-resources">
                                    <span class="resource-tag guide">Word Guide</span>
                                    <span class="resource-tag excel">Excel Tool</span>
                                </div>
                                <button class="module-btn locked">Locked</button>
                            </div>

                            <div class="module-item locked">
                                <div class="module-info">
                                    <h4>Portfolio Diversification</h4>
                                    <p>Build a balanced investment portfolio</p>
                                </div>
                                <div class="module-resources">
                                    <span class="resource-tag guide">Word Guide</span>
                                    <span class="resource-tag excel">Excel Tool</span>
                                    <span class="resource-tag video">Video</span>
                                </div>
                                <button class="module-btn locked">Locked</button>
                            </div>
                        </div>
                    </div>

                    <!-- Phase 4: Sustain & Scale -->
                    <div class="phase-card locked" data-phase="sustain-scale">
                        <div class="phase-header">
                            <div class="phase-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="phase-info">
                                <h3>Sustain &amp; Scale</h3>
                                <p>Advanced wealth management and legacy planning</p>
                            </div>
                            <div class="phase-status locked">
                                <i class="fas fa-lock"></i>
                                <span>Locked</span>
                            </div>
                        </div>

                        <div class="phase-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>
                            <span class="progress-text">0/10 modules • Complete "Grow &amp; Multiply" to unlock</span>
                        </div>

                        <div class="phase-modules locked">
                            <div class="module-item locked">
                                <div class="module-info">
                                    <h4>Tax Optimization Strategies</h4>
                                    <p>Minimize taxes and maximize wealth retention</p>
                                </div>
                                <div class="module-resources">
                                    <span class="resource-tag guide">Word Guide</span>
                                    <span class="resource-tag excel">Excel Tool</span>
                                </div>
                                <button class="module-btn locked">Locked</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tools & Templates Tab -->
            <div id="tools-templates" class="resource-tab-content">
                <div class="tools-grid">
                    <div class="tool-card">
                        <div class="tool-accent"></div>
                        <button class="walkthrough-indicator" data-tool="monthly-budget-tracker" title="Watch walkthrough video">
                            <i class="fas fa-play"></i>
                        </button>
                        <div class="tool-icon">
                            <i class="fas fa-file-excel"></i>
                        </div>
                        <div class="tool-content">
                            <h3>Monthly Budget Tracker</h3>
                            <p>Comprehensive Excel template for tracking income, expenses, and savings goals</p>
                            <div class="tool-formats">
                                <span class="format-tag excel">Excel Template</span>
                            </div>
                        </div>
                        <button class="tool-download-btn">
                            <i class="fas fa-download"></i>
                            Download
                        </button>
                    </div>

                    <div class="tool-card">
                        <div class="tool-accent"></div>
                        <button class="walkthrough-indicator" data-tool="debt-payoff-calculator" title="Watch walkthrough video">
                            <i class="fas fa-play"></i>
                        </button>
                        <div class="tool-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="tool-content">
                            <h3>Debt Payoff Calculator</h3>
                            <p>Calculate your debt elimination timeline using snowball or avalanche methods</p>
                            <div class="tool-formats">
                                <span class="format-tag excel">Excel Template</span>
                            </div>
                        </div>
                        <button class="tool-download-btn">
                            <i class="fas fa-download"></i>
                            Download
                        </button>
                    </div>

                    <div class="tool-card">
                        <div class="tool-accent"></div>
                        <button class="walkthrough-indicator" data-tool="emergency-fund-planner" title="Watch walkthrough video">
                            <i class="fas fa-play"></i>
                        </button>
                        <div class="tool-icon">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div class="tool-content">
                            <h3>Emergency Fund Planner</h3>
                            <p>Calculate your ideal emergency fund size and track your progress</p>
                            <div class="tool-formats">
                                <span class="format-tag excel">Excel Template</span>
                                <span class="format-tag pdf">PDF Guide</span>
                            </div>
                        </div>
                        <button class="tool-download-btn">
                            <i class="fas fa-download"></i>
                            Download
                        </button>
                    </div>

                    <div class="tool-card">
                        <div class="tool-accent"></div>
                        <button class="walkthrough-indicator" data-tool="investment-portfolio-tracker" title="Watch walkthrough video">
                            <i class="fas fa-play"></i>
                        </button>
                        <div class="tool-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="tool-content">
                            <h3>Investment Portfolio Tracker</h3>
                            <p>Monitor your investment performance and asset allocation</p>
                            <div class="tool-formats">
                                <span class="format-tag excel">Excel Template</span>
                            </div>
                        </div>
                        <button class="tool-download-btn">
                            <i class="fas fa-download"></i>
                            Download
                        </button>
                    </div>

                    <div class="tool-card">
                        <div class="tool-accent"></div>
                        <button class="walkthrough-indicator" data-tool="financial-goals-worksheet" title="Watch walkthrough video">
                            <i class="fas fa-play"></i>
                        </button>
                        <div class="tool-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="tool-content">
                            <h3>Financial Goals Worksheet</h3>
                            <p>Set and track SMART financial goals with actionable steps</p>
                            <div class="tool-formats">
                                <span class="format-tag pdf">PDF Worksheet</span>
                                <span class="format-tag word">Word Doc</span>
                            </div>
                        </div>
                        <button class="tool-download-btn">
                            <i class="fas fa-download"></i>
                            Download
                        </button>
                    </div>

                    <div class="tool-card">
                        <div class="tool-accent"></div>
                        <button class="walkthrough-indicator" data-tool="home-buying-readiness-checklist" title="Watch walkthrough video">
                            <i class="fas fa-play"></i>
                        </button>
                        <div class="tool-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="tool-content">
                            <h3>Home Buying Readiness Checklist</h3>
                            <p>Comprehensive checklist to determine if you're ready to buy a home</p>
                            <div class="tool-formats">
                                <span class="format-tag pdf">PDF Checklist</span>
                                <span class="format-tag excel">Excel Calculator</span>
                            </div>
                        </div>
                        <button class="tool-download-btn">
                            <i class="fas fa-download"></i>
                            Download
                        </button>
                    </div>

                    <div class="tool-card">
                        <div class="tool-accent"></div>
                        <button class="walkthrough-indicator" data-tool="college-savings-planner" title="Watch walkthrough video">
                            <i class="fas fa-play"></i>
                        </button>
                        <div class="tool-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="tool-content">
                            <h3>College Savings Planner</h3>
                            <p>Plan and track savings for education expenses</p>
                            <div class="tool-formats">
                                <span class="format-tag excel">Excel Template</span>
                            </div>
                        </div>
                        <button class="tool-download-btn">
                            <i class="fas fa-download"></i>
                            Download
                        </button>
                    </div>

                    <div class="tool-card">
                        <div class="tool-accent"></div>
                        <button class="walkthrough-indicator" data-tool="insurance-needs-calculator" title="Watch walkthrough video">
                            <i class="fas fa-play"></i>
                        </button>
                        <div class="tool-icon">
                            <i class="fas fa-umbrella"></i>
                        </div>
                        <div class="tool-content">
                            <h3>Insurance Needs Calculator</h3>
                            <p>Determine the right amount of life and disability insurance</p>
                            <div class="tool-formats">
                                <span class="format-tag excel">Excel Calculator</span>
                                <span class="format-tag pdf">PDF Guide</span>
                            </div>
                        </div>
                        <button class="tool-download-btn">
                            <i class="fas fa-download"></i>
                            Download
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

