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
            if (in_array('debt-management', $clientAnswers)) {
                $barStates['reset-rewire'] = 'active';
            }
            if (in_array('cash-flow', $clientAnswers) || in_array('savings-habits', $clientAnswers)) {
                $barStates['take-control'] = 'active';
            }
            if (in_array('investing-basics', $clientAnswers) || in_array('savings-habits', $clientAnswers)) {
                $barStates['grow-multiply'] = 'active';
            }
            if (in_array('wealth-building', $clientAnswers) || in_array('financial-education', $clientAnswers)) {
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

    <div class="metric-card confidence-card">
        <div class="card-header-with-info">
            <h3>Budget Confidence Score</h3>
            <div class="info-indicator" data-tooltip="confidence-score">
                <i class="fas fa-info-circle"></i>
            </div>
        </div>
        <div class="confidence-circle">
            @php
                $score = $confidence['score'] ?? 0;
                $degree = round($score * 3.6);
            @endphp
            <div class="confidence-progress" style="background-image: conic-gradient(var(--success-green) 0deg {{ $degree }}deg, #E5E7EB {{ $degree }}deg 360deg)">
                <span class="confidence-value">{{ $score }}</span>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="metric-card confidence-card">
        <div class="card-header-with-info">
            <h3>Personal Finance Confidence Score</h3>
            <div class="info-indicator" data-tooltip="personal-finance-confidence">
                <i class="fas fa-info-circle"></i>
            </div>
        </div>
        <div class="confidence-circle">
            @php
                $pfScore = $personalFinanceConfidence['score'] ?? 0;
                $pfDegree = round($pfScore * 3.6);
            @endphp
            <div class="confidence-progress" style="background-image: conic-gradient(var(--success-green) 0deg {{ $pfDegree }}deg, #E5E7EB {{ $pfDegree }}deg 360deg)">
                <span class="confidence-value">{{ $pfScore }}</span>
            </div>
        </div>
    </div>

    <div class="metric-card debt-progress-card">
        <div class="card-header-with-info">
            <h3>Debt Knowledge Journey</h3>
            <div class="info-indicator" data-tooltip="debt-knowledge-journey">
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
            <h3>Emergency Readiness Level</h3>
            <div class="info-indicator" data-tooltip="emergency-readiness">
                <i class="fas fa-info-circle"></i>
            </div>
        </div>
        <div class="confidence-circle">
            @php
                $erScore = $emergencyReadiness['score'] ?? 0;
                $erDegree = round($erScore * 3.6);
            @endphp
            <div class="confidence-progress" style="background-image: conic-gradient(var(--success-green) 0deg {{ $erDegree }}deg, #E5E7EB {{ $erDegree }}deg 360deg)">
                <span class="confidence-value">{{ $erScore }}</span>
            </div>
        </div>
        <div style="text-align: center; margin-top: 10px;">
            <span class="badge badge-{{ $emergencyReadiness['badge']['style'] ?? 'secondary' }}">
                {{ $emergencyReadiness['badge']['text'] ?? 'Not Set' }}
            </span>
        </div>
    </div>

    <div class="metric-card investing-card">
        <div class="card-header-with-info">
            <h3>Investing Habit / Contribution Readiness</h3>
            <div class="info-indicator" data-tooltip="investing-habit">
                <i class="fas fa-info-circle"></i>
            </div>
        </div>
        <div class="investing-content">
            <span class="investing-label">{{ $investingHabit['label'] ?? 'Not Set' }}</span>
            <div class="investing-progress">
                <div class="investing-bar">
                    <div class="investing-fill" style="width: {{ $investingHabit['percentage'] ?? 0 }}%;"></div>
                </div>
            </div>
            <div style="text-align: center; margin-top: 10px;">
                <span class="badge badge-{{ $investingHabit['badge']['style'] ?? 'secondary' }}">
                    {{ $investingHabit['badge']['text'] ?? 'Not Set' }}
                </span>
            </div>
        </div>
    </div>
</div>
