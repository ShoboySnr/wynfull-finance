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
                                    // Map new learning focus options to phases
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
                    @if($onboardings->count() > 0)
                        <div class="chart-container" style="height: 350px; padding: 1rem;">
                            <canvas id="budgetConfidenceChart"></canvas>
                        </div>
                        <div class="chart-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                            <button id="budgetConfidencePrev" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: 0.25rem 0.5rem;" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="budgetConfidencePageInfo" style="font-size: 0.875rem; color: var(--text-secondary); padding-left: 0.5rem; padding-right: 0.5rem;">Page 1</span>
                            <button id="budgetConfidenceNext" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.5rem;">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('budgetConfidenceChart');
                            if (!ctx) return;

                            const allData = @json($budgetConfidenceChart);
                            const itemsPerPage = 4;
                            let currentPage = 0;
                            let chartInstance = null;
                            
                            const isDarkMode = document.body.getAttribute('data-theme') === 'dark' || 
                                             document.documentElement.getAttribute('data-theme') === 'dark';
                            const barColor = isDarkMode ? 'rgba(100, 200, 255, 0.8)' : 'rgba(14, 77, 164, 0.8)';
                            const barBorderColor = isDarkMode ? 'rgba(100, 200, 255, 1)' : 'rgba(14, 77, 164, 1)';
                            const firstSubmissionColor = isDarkMode ? 'rgba(251, 191, 36, 0.8)' : 'rgba(245, 158, 11, 0.8)';
                            const firstSubmissionBorderColor = isDarkMode ? 'rgba(251, 191, 36, 1)' : 'rgba(245, 158, 11, 1)';

                            function renderChart(page) {
                                const totalPages = Math.ceil(allData.length / itemsPerPage);
                                const start = page * itemsPerPage;
                                const end = start + itemsPerPage;
                                const pageData = allData.slice(start, end);

                                const labels = pageData.map(item => item.label);
                                const values = pageData.map(item => item.value);
                                const backgroundColors = pageData.map(item => 
                                    item.isFirst ? firstSubmissionColor : barColor
                                );
                                const borderColors = pageData.map(item => 
                                    item.isFirst ? firstSubmissionBorderColor : barBorderColor
                                );

                                if (chartInstance) {
                                    chartInstance.destroy();
                                }

                                chartInstance = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Budget Confidence Level',
                                            data: values,
                                            backgroundColor: backgroundColors,
                                            borderColor: borderColors,
                                            borderWidth: 2,
                                            borderRadius: 8,
                                            barThickness: 40,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        barPercentage: 0.8,
                                        categoryPercentage: 0.9,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                                padding: 12,
                                                callbacks: {
                                                    label: function(context) {
                                                        const value = context.parsed.y;
                                                        let level = '';
                                                        if (value === 0) level = 'Not Set';
                                                        else if (value === 25) level = 'Not Confident';
                                                        else if (value === 50) level = 'Somewhat Confident';
                                                        else if (value === 75) level = 'Confident';
                                                        else if (value === 100) level = 'Very Confident';
                                                        const item = pageData[context.dataIndex];
                                                        const dateInfo = item.isFirst && item.fullDate ? ' (' + item.fullDate + ')' : '';
                                                        return level + ' - ' + value + '%' + dateInfo;
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                max: 100,
                                                ticks: {
                                                    callback: function(value) {
                                                        return value + '%';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });

                                document.getElementById('budgetConfidencePageInfo').textContent = 
                                    `Page ${page + 1} of ${totalPages}`;
                                
                                document.getElementById('budgetConfidencePrev').disabled = page === 0;
                                document.getElementById('budgetConfidencePrev').style.opacity = page === 0 ? '0.5' : '1';
                                document.getElementById('budgetConfidencePrev').style.cursor = page === 0 ? 'not-allowed' : 'pointer';
                                
                                document.getElementById('budgetConfidenceNext').disabled = page >= totalPages - 1;
                                document.getElementById('budgetConfidenceNext').style.opacity = page >= totalPages - 1 ? '0.5' : '1';
                                document.getElementById('budgetConfidenceNext').style.cursor = page >= totalPages - 1 ? 'not-allowed' : 'pointer';
                            }

                            document.getElementById('budgetConfidencePrev').addEventListener('click', function() {
                                if (currentPage > 0) {
                                    currentPage--;
                                    renderChart(currentPage);
                                }
                            });

                            document.getElementById('budgetConfidenceNext').addEventListener('click', function() {
                                const totalPages = Math.ceil(allData.length / itemsPerPage);
                                if (currentPage < totalPages - 1) {
                                    currentPage++;
                                    renderChart(currentPage);
                                }
                            });

                            renderChart(currentPage);
                        });
                        </script>
                    @else
                        <div class="confidence-circle">
                            @php
                                $score = $confidence['score'] ?? 0;
                                $degree = round($score * 3.6);
                            @endphp
                            <div class="confidence-progress" style="background-image: conic-gradient(var(--success-green) 0deg {{ $degree }}deg, #E5E7EB {{ $degree }}deg 360deg)">
                                <span class="confidence-value">{{ $score }}</span>
                            </div>
                        </div>
                    @endif
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
                    @if($onboardings->count() > 0)
                        <div class="chart-container" style="height: 350px; padding: 1rem;">
                            <canvas id="personalFinanceConfidenceChart"></canvas>
                        </div>
                        <div class="chart-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                            <button id="pfConfidencePrev" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: 0.25rem 0.5rem;" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="pfConfidencePageInfo" style="font-size: 0.875rem; color: var(--text-secondary); padding-left: 0.5rem; padding-right: 0.5rem;">Page 1</span>
                            <button id="pfConfidenceNext" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.5rem;">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('personalFinanceConfidenceChart');
                            if (!ctx) return;

                            const allData = @json($personalFinanceConfidenceChart);
                            const itemsPerPage = 4;
                            let currentPage = 0;
                            let chartInstance = null;
                            
                            const isDarkMode = document.body.getAttribute('data-theme') === 'dark' || 
                                             document.documentElement.getAttribute('data-theme') === 'dark';
                            const barColor = isDarkMode ? 'rgba(100, 200, 255, 0.8)' : 'rgba(14, 77, 164, 0.8)';
                            const barBorderColor = isDarkMode ? 'rgba(100, 200, 255, 1)' : 'rgba(14, 77, 164, 1)';
                            const firstSubmissionColor = isDarkMode ? 'rgba(251, 191, 36, 0.8)' : 'rgba(245, 158, 11, 0.8)';
                            const firstSubmissionBorderColor = isDarkMode ? 'rgba(251, 191, 36, 1)' : 'rgba(245, 158, 11, 1)';

                            function renderChart(page) {
                                const totalPages = Math.ceil(allData.length / itemsPerPage);
                                const start = page * itemsPerPage;
                                const end = start + itemsPerPage;
                                const pageData = allData.slice(start, end);

                                const labels = pageData.map(item => item.label);
                                const values = pageData.map(item => item.value);
                                const backgroundColors = pageData.map(item => 
                                    item.isFirst ? firstSubmissionColor : barColor
                                );
                                const borderColors = pageData.map(item => 
                                    item.isFirst ? firstSubmissionBorderColor : barBorderColor
                                );

                                if (chartInstance) {
                                    chartInstance.destroy();
                                }

                                chartInstance = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Personal Finance Confidence Level',
                                            data: values,
                                            backgroundColor: backgroundColors,
                                            borderColor: borderColors,
                                            borderWidth: 2,
                                            borderRadius: 8,
                                            barThickness: 40,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        barPercentage: 0.8,
                                        categoryPercentage: 0.9,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                                padding: 12,
                                                callbacks: {
                                                    label: function(context) {
                                                        const value = context.parsed.y;
                                                        let level = '';
                                                        if (value === 25) level = 'Not Confident';
                                                        else if (value === 50) level = 'Somewhat Confident';
                                                        else if (value === 75) level = 'Confident';
                                                        else if (value === 100) level = 'Very Confident';
                                                        const isFirst = pageData[context.dataIndex].isFirst;
                                                        return level + ' (' + value + '%)' + (isFirst ? ' 🌟 First Submission' : '');
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                max: 100,
                                                ticks: {
                                                    callback: function(value) {
                                                        return value + '%';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });

                                document.getElementById('pfConfidencePageInfo').textContent = 
                                    `Page ${page + 1} of ${totalPages}`;
                                
                                document.getElementById('pfConfidencePrev').disabled = page === 0;
                                document.getElementById('pfConfidencePrev').style.opacity = page === 0 ? '0.5' : '1';
                                document.getElementById('pfConfidencePrev').style.cursor = page === 0 ? 'not-allowed' : 'pointer';
                                
                                document.getElementById('pfConfidenceNext').disabled = page >= totalPages - 1;
                                document.getElementById('pfConfidenceNext').style.opacity = page >= totalPages - 1 ? '0.5' : '1';
                                document.getElementById('pfConfidenceNext').style.cursor = page >= totalPages - 1 ? 'not-allowed' : 'pointer';
                            }

                            document.getElementById('pfConfidencePrev').addEventListener('click', function() {
                                if (currentPage > 0) {
                                    currentPage--;
                                    renderChart(currentPage);
                                }
                            });

                            document.getElementById('pfConfidenceNext').addEventListener('click', function() {
                                const totalPages = Math.ceil(allData.length / itemsPerPage);
                                if (currentPage < totalPages - 1) {
                                    currentPage++;
                                    renderChart(currentPage);
                                }
                            });

                            renderChart(currentPage);
                        });
                        </script>
                    @else
                        <div class="confidence-circle">
                            @php
                                $pfScore = $personalFinanceConfidence['score'] ?? 0;
                                $pfDegree = round($pfScore * 3.6);
                            @endphp
                            <div class="confidence-progress" style="background-image: conic-gradient(var(--success-green) 0deg {{ $pfDegree }}deg, #E5E7EB {{ $pfDegree }}deg 360deg)">
                                <span class="confidence-value">{{ $pfScore }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="metric-card debt-progress-card">
                    <div class="card-header-with-info">
                        <h3>Debt Knowledge Journey</h3>
                        <div class="info-indicator" data-tooltip="debt-knowledge-journey">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                    @if($onboardings->count() > 0)
                        <div class="chart-container" style="height: 350px; padding: 1rem;">
                            <canvas id="debtKnowledgeChart"></canvas>
                        </div>
                        <div class="chart-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                            <button id="debtKnowledgePrev" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: 0.25rem 0.5rem;" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="debtKnowledgePageInfo" style="font-size: 0.875rem; color: var(--text-secondary); padding-left: 0.5rem; padding-right: 0.5rem;">Page 1</span>
                            <button id="debtKnowledgeNext" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.5rem;">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('debtKnowledgeChart');
                            if (!ctx) return;

                            const allData = @json($debtKnowledgeChart);
                            const itemsPerPage = 4;
                            let currentPage = 0;
                            let chartInstance = null;
                            
                            const isDarkMode = document.body.getAttribute('data-theme') === 'dark' || 
                                             document.documentElement.getAttribute('data-theme') === 'dark';
                            const firstSubmissionColor = isDarkMode ? 'rgba(251, 191, 36, 0.8)' : 'rgba(245, 158, 11, 0.8)';
                            const firstSubmissionBorderColor = isDarkMode ? 'rgba(251, 191, 36, 1)' : 'rgba(245, 158, 11, 1)';

                            function renderChart(page) {
                                const totalPages = Math.ceil(allData.length / itemsPerPage);
                                const start = page * itemsPerPage;
                                const end = start + itemsPerPage;
                                const pageData = allData.slice(start, end);

                                const labels = pageData.map(item => item.label);
                                const values = pageData.map(item => item.value);
                                const backgroundColors = pageData.map(item => 
                                    item.isFirst ? firstSubmissionColor : item.backgroundColor
                                );
                                const borderColors = pageData.map(item => 
                                    item.isFirst ? firstSubmissionBorderColor : item.borderColor
                                );

                                if (chartInstance) {
                                    chartInstance.destroy();
                                }

                                chartInstance = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Debt Knowledge Level',
                                            data: values,
                                            backgroundColor: backgroundColors,
                                            borderColor: borderColors,
                                            borderWidth: 2,
                                            borderRadius: 8,
                                            barThickness: 40,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        barPercentage: 0.8,
                                        categoryPercentage: 0.9,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                                padding: 12,
                                                callbacks: {
                                                    label: function(context) {
                                                        const value = context.parsed.y;
                                                        let level = '';
                                                        if (value === 25) level = 'No Knowledge';
                                                        else if (value === 50) level = 'Learning Basics';
                                                        else if (value === 75) level = 'Applying Strategies';
                                                        else if (value === 100) level = 'Expert Level';
                                                        const isFirst = pageData[context.dataIndex].isFirst;
                                                        return level + ' (' + value + '%)' + (isFirst ? ' 🌟 First Submission' : '');
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                max: 100,
                                                ticks: {
                                                    callback: function(value) {
                                                        return value + '%';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });

                                document.getElementById('debtKnowledgePageInfo').textContent = 
                                    `Page ${page + 1} of ${totalPages}`;
                                
                                document.getElementById('debtKnowledgePrev').disabled = page === 0;
                                document.getElementById('debtKnowledgePrev').style.opacity = page === 0 ? '0.5' : '1';
                                document.getElementById('debtKnowledgePrev').style.cursor = page === 0 ? 'not-allowed' : 'pointer';
                                
                                document.getElementById('debtKnowledgeNext').disabled = page >= totalPages - 1;
                                document.getElementById('debtKnowledgeNext').style.opacity = page >= totalPages - 1 ? '0.5' : '1';
                                document.getElementById('debtKnowledgeNext').style.cursor = page >= totalPages - 1 ? 'not-allowed' : 'pointer';
                            }

                            document.getElementById('debtKnowledgePrev').addEventListener('click', function() {
                                if (currentPage > 0) {
                                    currentPage--;
                                    renderChart(currentPage);
                                }
                            });

                            document.getElementById('debtKnowledgeNext').addEventListener('click', function() {
                                const totalPages = Math.ceil(allData.length / itemsPerPage);
                                if (currentPage < totalPages - 1) {
                                    currentPage++;
                                    renderChart(currentPage);
                                }
                            });

                            renderChart(currentPage);
                        });
                        </script>
                    @else
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
                    @endif
                </div>

                <div class="metric-card knowledge-card">
                    <div class="card-header-with-info">
                        <h3>Investing Knowledge</h3>
                        <div class="info-indicator" data-tooltip="investing-knowledge">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                    @if($onboardings->count() > 0)
                        <div class="chart-container" style="height: 350px; padding: 1rem;">
                            <canvas id="investingKnowledgeChart"></canvas>
                        </div>
                        <div class="chart-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                            <button id="investingKnowledgePrev" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: 0.25rem 0.5rem;" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="investingKnowledgePageInfo" style="font-size: 0.875rem; color: var(--text-secondary); padding-left: 0.5rem; padding-right: 0.5rem;">Page 1</span>
                            <button id="investingKnowledgeNext" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.5rem;">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('investingKnowledgeChart');
                            if (!ctx) return;

                            const allData = @json($investingKnowledgeChart);
                            const itemsPerPage = 4;
                            let currentPage = 0;
                            let chartInstance = null;
                            
                            const isDarkMode = document.body.getAttribute('data-theme') === 'dark' || 
                                             document.documentElement.getAttribute('data-theme') === 'dark';
                            const textColor = isDarkMode ? '#E5E7EB' : '#374151';
                            const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
                            const firstSubmissionColor = isDarkMode ? 'rgba(251, 191, 36, 0.8)' : 'rgba(245, 158, 11, 0.8)';
                            const firstSubmissionBorderColor = isDarkMode ? 'rgba(251, 191, 36, 1)' : 'rgba(245, 158, 11, 1)';

                            function renderChart(page) {
                                const totalPages = Math.ceil(allData.length / itemsPerPage);
                                const start = page * itemsPerPage;
                                const end = start + itemsPerPage;
                                const pageData = allData.slice(start, end);

                                const labels = pageData.map(item => item.label);
                                const values = pageData.map(item => item.value);
                                const backgroundColors = pageData.map(item => 
                                    item.isFirst ? firstSubmissionColor : item.backgroundColor
                                );
                                const borderColors = pageData.map(item => 
                                    item.isFirst ? firstSubmissionBorderColor : item.borderColor
                                );

                                if (chartInstance) {
                                    chartInstance.destroy();
                                }

                                chartInstance = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Investing Knowledge Level',
                                            data: values,
                                            backgroundColor: backgroundColors,
                                            borderColor: borderColors,
                                            borderWidth: 2,
                                            borderRadius: 8,
                                            barThickness: 40,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        barPercentage: 0.8,
                                        categoryPercentage: 0.9,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                                padding: 12,
                                                callbacks: {
                                                    label: function(context) {
                                                        const value = context.parsed.y;
                                                        let level = '';
                                                        if (value === 1) level = 'Not Familiar Yet';
                                                        else if (value === 2) level = 'Familiar with Basics';
                                                        else if (value === 4) level = 'Comfortable Applying';
                                                        else if (value === 5) level = 'Advanced Understanding';
                                                        const isFirst = pageData[context.dataIndex].isFirst;
                                                        return level + ' (Level ' + value + ')' + (isFirst ? ' 🌟 First Submission' : '');
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            x: {
                                                ticks: {
                                                    color: textColor,
                                                    font: {
                                                        size: 12
                                                    }
                                                },
                                                grid: {
                                                    color: gridColor
                                                }
                                            },
                                            y: {
                                                beginAtZero: true,
                                                max: 5,
                                                ticks: {
                                                    stepSize: 1,
                                                    color: textColor,
                                                    font: {
                                                        size: 12
                                                    },
                                                    callback: function(value) {
                                                        return 'Level ' + value;
                                                    }
                                                },
                                                grid: {
                                                    color: gridColor
                                                }
                                            }
                                        }
                                    }
                                });

                                document.getElementById('investingKnowledgePageInfo').textContent = 
                                    `Page ${page + 1} of ${totalPages}`;
                                
                                document.getElementById('investingKnowledgePrev').disabled = page === 0;
                                document.getElementById('investingKnowledgePrev').style.opacity = page === 0 ? '0.5' : '1';
                                document.getElementById('investingKnowledgePrev').style.cursor = page === 0 ? 'not-allowed' : 'pointer';
                                
                                document.getElementById('investingKnowledgeNext').disabled = page >= totalPages - 1;
                                document.getElementById('investingKnowledgeNext').style.opacity = page >= totalPages - 1 ? '0.5' : '1';
                                document.getElementById('investingKnowledgeNext').style.cursor = page >= totalPages - 1 ? 'not-allowed' : 'pointer';
                            }

                            document.getElementById('investingKnowledgePrev').addEventListener('click', function() {
                                if (currentPage > 0) {
                                    currentPage--;
                                    renderChart(currentPage);
                                }
                            });

                            document.getElementById('investingKnowledgeNext').addEventListener('click', function() {
                                const totalPages = Math.ceil(allData.length / itemsPerPage);
                                if (currentPage < totalPages - 1) {
                                    currentPage++;
                                    renderChart(currentPage);
                                }
                            });

                            renderChart(currentPage);
                        });
                        </script>
                    @else
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
                    @endif
                </div>

                <div class="metric-card emergency-fund-card">
                    <div class="card-header-with-info">
                        <h3>Emergency Readiness Level</h3>
                        <div class="info-indicator" data-tooltip="emergency-readiness">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                    @if($onboardings->count() > 0)
                        <div class="chart-container" style="height: 350px; padding: 1rem;">
                            <canvas id="emergencyReadinessChart"></canvas>
                        </div>
                        <div class="chart-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                            <button id="emergencyReadinessPrev" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: 0.25rem 0.5rem;" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="emergencyReadinessPageInfo" style="font-size: 0.875rem; color: var(--text-secondary); padding-left: 0.5rem; padding-right: 0.5rem;">Page 1</span>
                            <button id="emergencyReadinessNext" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.5rem;">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('emergencyReadinessChart');
                            if (!ctx) return;

                            const allData = @json($emergencyReadinessChart);
                            const itemsPerPage = 4;
                            let currentPage = 0;
                            let chartInstance = null;
                            
                            const isDarkMode = document.body.getAttribute('data-theme') === 'dark' || 
                                             document.documentElement.getAttribute('data-theme') === 'dark';
                            const firstSubmissionColor = isDarkMode ? 'rgba(251, 191, 36, 0.8)' : 'rgba(245, 158, 11, 0.8)';
                            const firstSubmissionBorderColor = isDarkMode ? 'rgba(251, 191, 36, 1)' : 'rgba(245, 158, 11, 1)';

                            function renderChart(page) {
                                const totalPages = Math.ceil(allData.length / itemsPerPage);
                                const start = page * itemsPerPage;
                                const end = start + itemsPerPage;
                                const pageData = allData.slice(start, end);

                                const labels = pageData.map(item => item.label);
                                const values = pageData.map(item => item.value);
                                const backgroundColors = pageData.map(item => item.backgroundColor);
                                const borderColors = pageData.map(item => item.borderColor);

                                if (chartInstance) {
                                    chartInstance.destroy();
                                }

                                chartInstance = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Emergency Readiness Level',
                                            data: values,
                                            backgroundColor: backgroundColors,
                                            borderColor: borderColors,
                                            borderWidth: 2,
                                            borderRadius: 8,
                                            barThickness: 40,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        barPercentage: 0.8,
                                        categoryPercentage: 0.9,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                                padding: 12,
                                                callbacks: {
                                                    label: function(context) {
                                                        const value = context.parsed.y;
                                                        let level = '';
                                                        if (value === 25) level = 'Not Prepared';
                                                        else if (value === 50) level = 'Building Readiness';
                                                        else if (value === 75) level = 'Well Prepared';
                                                        else if (value === 100) level = 'Fully Prepared';
                                                        const item = pageData[context.dataIndex];
                                                        const dateInfo = item.isFirst && item.fullDate ? ' (' + item.fullDate + ')' : '';
                                                        return level + ' - ' + value + '%' + dateInfo;
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                max: 100,
                                                ticks: {
                                                    callback: function(value) {
                                                        return value + '%';
                                                    }
                                                }
                                            },
                                            x: {
                                                ticks: {
                                                    font: function(context) {
                                                        const item = pageData[context.index];
                                                        if (item && item.isFirst) {
                                                            return {
                                                                weight: 'bold',
                                                                size: 11
                                                            };
                                                        }
                                                        return {};
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });

                                document.getElementById('emergencyReadinessPageInfo').textContent = 
                                    `Page ${page + 1} of ${totalPages}`;
                                
                                document.getElementById('emergencyReadinessPrev').disabled = page === 0;
                                document.getElementById('emergencyReadinessPrev').style.opacity = page === 0 ? '0.5' : '1';
                                document.getElementById('emergencyReadinessPrev').style.cursor = page === 0 ? 'not-allowed' : 'pointer';
                                
                                document.getElementById('emergencyReadinessNext').disabled = page >= totalPages - 1;
                                document.getElementById('emergencyReadinessNext').style.opacity = page >= totalPages - 1 ? '0.5' : '1';
                                document.getElementById('emergencyReadinessNext').style.cursor = page >= totalPages - 1 ? 'not-allowed' : 'pointer';
                            }

                            document.getElementById('emergencyReadinessPrev').addEventListener('click', function() {
                                if (currentPage > 0) {
                                    currentPage--;
                                    renderChart(currentPage);
                                }
                            });

                            document.getElementById('emergencyReadinessNext').addEventListener('click', function() {
                                const totalPages = Math.ceil(allData.length / itemsPerPage);
                                if (currentPage < totalPages - 1) {
                                    currentPage++;
                                    renderChart(currentPage);
                                }
                            });

                            renderChart(currentPage);
                        });
                        </script>
                    @else
                        <div class="confidence-circle">
                            @php
                                $erScore = $emergencyReadiness['score'] ?? 0;
                                $erDegree = round($erScore * 3.6);
                            @endphp
                            <div class="confidence-progress" style="background-image: conic-gradient(var(--success-green) 0deg {{ $erDegree }}deg, #E5E7EB {{ $erDegree }}deg 360deg)">
                                <span class="confidence-value">{{ $erScore }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="metric-card investing-card">
                    <div class="card-header-with-info">
                        <h3>Investing Habit / Contribution Readiness</h3>
                        <div class="info-indicator" data-tooltip="investing-habit">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                    @if($onboardings->count() > 0)
                        <div class="chart-container" style="height: 350px; padding: 1rem;">
                            <canvas id="investingHabitChart"></canvas>
                        </div>
                        <div class="chart-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                            <button id="investingHabitPrev" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: 0.25rem 0.5rem;" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="investingHabitPageInfo" style="font-size: 0.875rem; color: #6b7280;">Page 1</span>
                            <button id="investingHabitNext" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.5rem;">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('investingHabitChart');
                            if (!ctx) return;

                            const allData = @json($investingHabitChart);
                            const itemsPerPage = 4;
                            let currentPage = 0;
                            let chartInstance = null;

                            function renderChart(page) {
                                const totalPages = Math.ceil(allData.length / itemsPerPage);
                                const start = page * itemsPerPage;
                                const end = start + itemsPerPage;
                                const pageData = allData.slice(start, end);

                                const labels = pageData.map(item => item.label);
                                const values = pageData.map(item => item.value);
                                const backgroundColors = pageData.map(item => item.backgroundColor);
                                const borderColors = pageData.map(item => item.borderColor);

                                if (chartInstance) {
                                    chartInstance.destroy();
                                }

                                chartInstance = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Investing Habit Level',
                                            data: values,
                                            backgroundColor: backgroundColors,
                                            borderColor: borderColors,
                                            borderWidth: 2,
                                            borderRadius: 8,
                                            barThickness: 40,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        barPercentage: 0.8,
                                        categoryPercentage: 0.9,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                                padding: 12,
                                                callbacks: {
                                                    label: function(context) {
                                                        const value = context.parsed.y;
                                                        let level = '';
                                                        if (value === 0) level = 'Not Set';
                                                        else if (value === 33) level = 'Beginner';
                                                        else if (value === 66) level = 'Intermediate';
                                                        else if (value === 100) level = 'Advanced';
                                                        const item = pageData[context.dataIndex];
                                                        const dateInfo = item.isFirst && item.fullDate ? ' (' + item.fullDate + ')' : '';
                                                        return level + ' - ' + value + '%' + dateInfo;
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                max: 100,
                                                ticks: {
                                                    callback: function(value) {
                                                        return value + '%';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });

                                // Update pagination controls
                                document.getElementById('investingHabitPageInfo').textContent = `Page ${page + 1} of ${totalPages}`;
                                document.getElementById('investingHabitPrev').disabled = page === 0;
                                document.getElementById('investingHabitNext').disabled = page === totalPages - 1;
                                
                                document.getElementById('investingHabitPrev').style.opacity = page === 0 ? '0.5' : '1';
                                document.getElementById('investingHabitPrev').style.cursor = page === 0 ? 'not-allowed' : 'pointer';
                                document.getElementById('investingHabitNext').style.opacity = page === totalPages - 1 ? '0.5' : '1';
                                document.getElementById('investingHabitNext').style.cursor = page === totalPages - 1 ? 'not-allowed' : 'pointer';
                            }

                            document.getElementById('investingHabitPrev').addEventListener('click', function() {
                                if (currentPage > 0) {
                                    currentPage--;
                                    renderChart(currentPage);
                                }
                            });

                            document.getElementById('investingHabitNext').addEventListener('click', function() {
                                const totalPages = Math.ceil(allData.length / itemsPerPage);
                                if (currentPage < totalPages - 1) {
                                    currentPage++;
                                    renderChart(currentPage);
                                }
                            });

                            renderChart(currentPage);
                        });
                        </script>
                    @else
                        <div style="padding: 2rem; text-align: center; color: #9ca3af;">
                            <i class="fas fa-chart-bar" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                            <p>No onboarding data available yet.</p>
                        </div>
                    @endif
                </div>
            </div>

{{--            <div class="dashboard-sections two-columns">--}}
{{--                <div class="ai-insights">--}}
{{--                    <h2>AI Insights</h2>--}}
{{--                    <div class="insight-card">--}}
{{--                        <i class="fas fa-lightbulb"></i>--}}
{{--                        <p>Based on your spending patterns, you could save an additional $200/month by optimizing your--}}
{{--                            subscription services.</p>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <div class="recent-activity">--}}
{{--                    <h2>Recent Activity</h2>--}}
{{--                    <div class="activity-list">--}}
{{--                        @forelse($activities as $a)--}}
{{--                            @php--}}
{{--                                $event = (string) ($a->event ?? '');--}}
{{--                                $desc  = trim((string) ($a->description ?? ''));--}}
{{--                                $props = $a->properties ?? collect();--}}

{{--                                // Friendly text fallback--}}
{{--                                $text = $desc !== '' ? $desc : ($event !== '' ? ucfirst(str_replace(['.', '-', '_'], ' ', $event)) : ucfirst($a->log_name ?? 'activity'));--}}

{{--                                // Optional richer messages based on common events you log--}}
{{--                                if ($event === 'clients.card.emergency_fund.view' && isset($props['progress'])) {--}}
{{--                                    // e.g., "Emergency fund progress: 35%"--}}
{{--                                    $text = 'Emergency fund progress: ' . (int)$props['progress'] . '%';--}}
{{--                                } elseif ($event === 'clients.card.investing.view' && isset($props['score'])) {--}}
{{--                                    // e.g., "Investing contribution score: 72"--}}
{{--                                    $text = 'Investing contribution score: ' . (int)$props['score'];--}}
{{--                                } elseif ($event === 'onboarding_completed') {--}}
{{--                                    $text = 'Completed onboarding';--}}
{{--                                } elseif (str_contains($event, 'logout')) {--}}
{{--                                    $text = 'Signed out';--}}
{{--                                }--}}

{{--                                // Icon map--}}
{{--                                $icon = match (true) {--}}
{{--                                    str_contains($event, 'emergency_fund') => 'fas fa-piggy-bank',--}}
{{--                                    str_contains($event, 'invest')         => 'fas fa-chart-line',--}}
{{--                                    str_contains($event, 'primary_goals')  => 'fas fa-bullseye',--}}
{{--                                    str_contains($event, 'onboarding')     => 'fas fa-check-circle',--}}
{{--                                    str_contains($event, 'login')          => 'fas fa-sign-in-alt',--}}
{{--                                    str_contains($event, 'logout')         => 'fas fa-sign-out-alt',--}}
{{--                                    default                                => 'fas fa-clipboard-list',--}}
{{--                                };--}}
{{--                            @endphp--}}
{{--                            <div class="activity-item">--}}
{{--                                <i class="{{ $icon }}"></i>--}}
{{--                                <span>{{ $text }}</span>--}}
{{--                                <time>{{ $a->created_at?->timezone(config('app.timezone'))->diffForHumans() }}</time>--}}
{{--                            </div>--}}
{{--                        @empty--}}
{{--                            <div class="activity-item">--}}
{{--                                <i class="fas fa-clipboard-list"></i>--}}
{{--                                <span>No recent activity yet</span>--}}
{{--                                <time>—</time>--}}
{{--                            </div>--}}
{{--                        @endforelse--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

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
            window.WYNFULL.onboardingCompleted = @json(!auth()->user()->shouldShowOnboarding());
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
<script src="{{ asset('assets/js/script.js') }}"></script>
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
        • Each phase corresponds to your current financial learning focus:<br>
        &nbsp;&nbsp;- Reset & Rewire: "Building a stronger understanding of debt management"<br>
        &nbsp;&nbsp;- Take Control: "Learning how to manage cash flow more effectively" or "Improving savings habits and consistency"<br>
        &nbsp;&nbsp;- Grow: "Learning how investing works and how to get started" or "Improving savings habits and consistency"<br>
        &nbsp;&nbsp;- Sustain: "Strengthening long-term wealth-building skills" or "Exploring financial education broadly"<br>
        &nbsp;&nbsp;- Other: Custom learning focus specified by you.<br><br>

        <strong>Action Tip:</strong><br>
        Focus your next steps and conversations on the phase with the highest bar — that's where your current financial learning journey is centered.`
    },
    'primary-goals': {
        title: 'Primary Goals',
        content: `<strong>Purpose:</strong><br>
        Displays your main focus areas for this pilot phase of your financial journey.<br><br>

        <strong>How to Read It:</strong><br>
        • These goals are automatically set based on your onboarding questionnaire.<br>
        • They can be updated anytime in collaboration with your trainer.<br><br>

        <strong>Action Tip:</strong><br>
        Use your goals as your North Star — revisit and adjust as you hit milestones or refine your priorities.`
    },
    'confidence-score': {
        title: 'Budget Confidence Score',
        content: `<strong>Purpose:</strong><br>
        Reflects how confident you are in understanding how a personal budget works.<br><br>

        <strong>Scale:</strong><br>
        25 (Not confident) → 50 (Somewhat confident) → 75 (Confident) → 100 (Very confident)<br><br>

        <strong>How to Read It:</strong><br>
        • Based on your answer to the onboarding question about budget understanding.<br>
        • A lower score indicates you're still learning budget fundamentals.<br>
        • A higher score represents strong understanding of budgeting concepts.<br><br>

        <strong>Action Tip:</strong><br>
        Use Wynfull's budgeting resources and tools to strengthen your understanding and build confidence in managing your budget.`
    },
    'personal-finance-confidence': {
        title: 'Personal Finance Confidence Score',
        content: `<strong>Purpose:</strong><br>
        Reflects how confident you feel understanding core personal financial concepts.<br><br>

        <strong>Scale:</strong><br>
        25 (Not confident) → 50 (Somewhat confident) → 75 (Confident) → 100 (Very confident)<br><br>

        <strong>How to Read It:</strong><br>
        • Based on your answer to the onboarding question about understanding personal finance concepts.<br>
        • A lower score indicates you're still building foundational financial knowledge.<br>
        • A higher score represents strong grasp of core financial principles.<br><br>

        <strong>Action Tip:</strong><br>
        Explore Wynfull's educational resources and work with your trainer to strengthen your understanding of personal finance fundamentals.`
    },
    'debt-knowledge-journey': {
        title: 'Debt Knowledge Journey',
        content: `<strong>Purpose:</strong><br>
        Tracks your understanding and confidence in applying debt management strategies.<br><br>

        <strong>Stages:</strong><br>
        No Knowledge → Learning Basics → Applying Strategies → Expert Level<br><br>

        <strong>How to Read It:</strong><br>
        • Based on your answer to the onboarding question about debt management understanding.<br>
        • The progress bar shows your current knowledge level (10% to 100%).<br>
        • Your stage reflects how comfortable you are with debt management strategies.<br><br>

        <strong>Action Tip:</strong><br>
        Use Wynfull's debt management resources and work with your trainer to strengthen your understanding and confidence in applying debt strategies effectively.`
    },
    'investing-knowledge': {
        title: 'Investing Knowledge',
        content: `<strong>Purpose:</strong><br>
        Shows your current level of familiarity with investing concepts.<br><br>

        <strong>Levels:</strong><br>
        Not Familiar Yet (1/5) → Familiar with Basics (2/5) → Comfortable Applying (4/5) → Advanced Understanding (5/5)<br><br>

        <strong>How to Read It:</strong><br>
        • Based on your answer to the onboarding question about investing concepts familiarity.<br>
        • The dots represent your knowledge level from 1 to 5.<br>
        • More filled dots indicate greater familiarity with investing concepts.<br><br>

        <strong>Action Tip:</strong><br>
        Use Wynfull's Resource Library and your trainer's guidance to build your understanding of investing concepts and move to the next knowledge tier.`
    },
    'emergency-readiness': {
        title: 'Emergency Readiness Level',
        content: `<strong>Purpose:</strong><br>
        Reflects your confidence in understanding the steps involved in preparing for unexpected financial situations.<br><br>

        <strong>Scale:</strong><br>
        25 (Not Prepared) → 50 (Building Readiness) → 75 (Well Prepared) → 100 (Fully Prepared)<br><br>

        <strong>How to Read It:</strong><br>
        • Based on your answer to the onboarding question about emergency preparedness understanding.<br>
        • A lower score indicates you're still learning about emergency financial planning.<br>
        • A higher score represents strong understanding of how to prepare for unexpected situations.<br><br>

        <strong>Action Tip:</strong><br>
        Work with your trainer to build an emergency fund strategy and learn the essential steps for financial preparedness. Understanding comes before action!`
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
    'investing-habit': {
        title: 'Investing Habit / Contribution Readiness',
        content: `<strong>Purpose:</strong><br>
        Reflects your investing experience level and readiness to contribute to investment accounts.<br><br>

        <strong>Levels:</strong><br>
        Building Foundation (33%) → Growing Confidence (66%) → Experienced Investor (100%)<br><br>

        <strong>How to Read It:</strong><br>
        • Based on your answer to the onboarding question about investing experience.<br>
        • Beginner: Building foundation and learning the basics.<br>
        • Intermediate: Growing confidence with practical experience.<br>
        • Advanced: Experienced investor with strong knowledge.<br><br>

        <strong>Action Tip:</strong><br>
        Work with your trainer to develop investing habits that match your experience level. Start small, stay consistent, and grow your confidence over time.`
    }
}
</script>
@endpush
