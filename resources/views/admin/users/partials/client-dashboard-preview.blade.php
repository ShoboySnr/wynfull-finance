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
        @if($onboardings->count() > 0)
            <div class="chart-container" style="height: 350px; padding: 1rem;">
                <canvas id="budgetConfidenceChartAdmin"></canvas>
            </div>
            <div class="chart-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                <button id="budgetConfidenceAdminPrev" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: 0.25rem 0.5rem;" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span id="budgetConfidenceAdminPageInfo" style="font-size: 0.875rem; color: var(--text-secondary);">Page 1</span>
                <button id="budgetConfidenceAdminNext" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.5rem;">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('budgetConfidenceChartAdmin');
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

                    document.getElementById('budgetConfidenceAdminPageInfo').textContent = 
                        `Page ${page + 1} of ${totalPages}`;
                    
                    document.getElementById('budgetConfidenceAdminPrev').disabled = page === 0;
                    document.getElementById('budgetConfidenceAdminPrev').style.opacity = page === 0 ? '0.5' : '1';
                    document.getElementById('budgetConfidenceAdminPrev').style.cursor = page === 0 ? 'not-allowed' : 'pointer';
                    
                    document.getElementById('budgetConfidenceAdminNext').disabled = page >= totalPages - 1;
                    document.getElementById('budgetConfidenceAdminNext').style.opacity = page >= totalPages - 1 ? '0.5' : '1';
                    document.getElementById('budgetConfidenceAdminNext').style.cursor = page >= totalPages - 1 ? 'not-allowed' : 'pointer';
                }

                document.getElementById('budgetConfidenceAdminPrev').addEventListener('click', function() {
                    if (currentPage > 0) {
                        currentPage--;
                        renderChart(currentPage);
                    }
                });

                document.getElementById('budgetConfidenceAdminNext').addEventListener('click', function() {
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
                <canvas id="personalFinanceConfidenceChartAdmin"></canvas>
            </div>
            <div class="chart-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                <button id="pfConfidenceAdminPrev" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: 0.25rem 0.5rem;" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span id="pfConfidenceAdminPageInfo" style="font-size: 0.875rem; color: var(--text-secondary);">Page 1</span>
                <button id="pfConfidenceAdminNext" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.5rem;">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('personalFinanceConfidenceChartAdmin');
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

                    document.getElementById('pfConfidenceAdminPageInfo').textContent = 
                        `Page ${page + 1} of ${totalPages}`;
                    
                    document.getElementById('pfConfidenceAdminPrev').disabled = page === 0;
                    document.getElementById('pfConfidenceAdminPrev').style.opacity = page === 0 ? '0.5' : '1';
                    document.getElementById('pfConfidenceAdminPrev').style.cursor = page === 0 ? 'not-allowed' : 'pointer';
                    
                    document.getElementById('pfConfidenceAdminNext').disabled = page >= totalPages - 1;
                    document.getElementById('pfConfidenceAdminNext').style.opacity = page >= totalPages - 1 ? '0.5' : '1';
                    document.getElementById('pfConfidenceAdminNext').style.cursor = page >= totalPages - 1 ? 'not-allowed' : 'pointer';
                }

                document.getElementById('pfConfidenceAdminPrev').addEventListener('click', function() {
                    if (currentPage > 0) {
                        currentPage--;
                        renderChart(currentPage);
                    }
                });

                document.getElementById('pfConfidenceAdminNext').addEventListener('click', function() {
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
                <canvas id="debtKnowledgeChartAdmin"></canvas>
            </div>
            <div class="chart-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                <button id="debtKnowledgeAdminPrev" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: 0.25rem 0.5rem;" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span id="debtKnowledgeAdminPageInfo" style="font-size: 0.875rem; color: var(--text-secondary);">Page 1</span>
                <button id="debtKnowledgeAdminNext" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.5rem;">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('debtKnowledgeChartAdmin');
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

                    document.getElementById('debtKnowledgeAdminPageInfo').textContent = 
                        `Page ${page + 1} of ${totalPages}`;
                    
                    document.getElementById('debtKnowledgeAdminPrev').disabled = page === 0;
                    document.getElementById('debtKnowledgeAdminPrev').style.opacity = page === 0 ? '0.5' : '1';
                    document.getElementById('debtKnowledgeAdminPrev').style.cursor = page === 0 ? 'not-allowed' : 'pointer';
                    
                    document.getElementById('debtKnowledgeAdminNext').disabled = page >= totalPages - 1;
                    document.getElementById('debtKnowledgeAdminNext').style.opacity = page >= totalPages - 1 ? '0.5' : '1';
                    document.getElementById('debtKnowledgeAdminNext').style.cursor = page >= totalPages - 1 ? 'not-allowed' : 'pointer';
                }

                document.getElementById('debtKnowledgeAdminPrev').addEventListener('click', function() {
                    if (currentPage > 0) {
                        currentPage--;
                        renderChart(currentPage);
                    }
                });

                document.getElementById('debtKnowledgeAdminNext').addEventListener('click', function() {
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
                <canvas id="investingKnowledgeChartAdmin"></canvas>
            </div>
            <div class="chart-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                <button id="investingKnowledgeAdminPrev" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: 0.25rem 0.5rem;" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span id="investingKnowledgeAdminPageInfo" style="font-size: 0.875rem; color: var(--text-secondary);">Page 1</span>
                <button id="investingKnowledgeAdminNext" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.5rem;">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('investingKnowledgeChartAdmin');
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

                    document.getElementById('investingKnowledgeAdminPageInfo').textContent = 
                        `Page ${page + 1} of ${totalPages}`;
                    
                    document.getElementById('investingKnowledgeAdminPrev').disabled = page === 0;
                    document.getElementById('investingKnowledgeAdminPrev').style.opacity = page === 0 ? '0.5' : '1';
                    document.getElementById('investingKnowledgeAdminPrev').style.cursor = page === 0 ? 'not-allowed' : 'pointer';
                    
                    document.getElementById('investingKnowledgeAdminNext').disabled = page >= totalPages - 1;
                    document.getElementById('investingKnowledgeAdminNext').style.opacity = page >= totalPages - 1 ? '0.5' : '1';
                    document.getElementById('investingKnowledgeAdminNext').style.cursor = page >= totalPages - 1 ? 'not-allowed' : 'pointer';
                }

                document.getElementById('investingKnowledgeAdminPrev').addEventListener('click', function() {
                    if (currentPage > 0) {
                        currentPage--;
                        renderChart(currentPage);
                    }
                });

                document.getElementById('investingKnowledgeAdminNext').addEventListener('click', function() {
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
                <canvas id="emergencyReadinessChartAdmin"></canvas>
            </div>
            <div class="chart-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                <button id="emergencyReadinessAdminPrev" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: 0.25rem 0.5rem;" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span id="emergencyReadinessAdminPageInfo" style="font-size: 0.875rem; color: var(--text-secondary); padding-left: 0.5rem; padding-right: 0.5rem;">Page 1</span>
                <button id="emergencyReadinessAdminNext" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.5rem;">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('emergencyReadinessChartAdmin');
                if (!ctx) return;

                const allData = @json($emergencyReadinessChart);
                console.log('Admin Emergency Readiness Chart Data:', allData);
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

                    document.getElementById('emergencyReadinessAdminPageInfo').textContent = 
                        `Page ${page + 1} of ${totalPages}`;
                    
                    document.getElementById('emergencyReadinessAdminPrev').disabled = page === 0;
                    document.getElementById('emergencyReadinessAdminPrev').style.opacity = page === 0 ? '0.5' : '1';
                    document.getElementById('emergencyReadinessAdminPrev').style.cursor = page === 0 ? 'not-allowed' : 'pointer';
                    
                    document.getElementById('emergencyReadinessAdminNext').disabled = page >= totalPages - 1;
                    document.getElementById('emergencyReadinessAdminNext').style.opacity = page >= totalPages - 1 ? '0.5' : '1';
                    document.getElementById('emergencyReadinessAdminNext').style.cursor = page >= totalPages - 1 ? 'not-allowed' : 'pointer';
                }

                document.getElementById('emergencyReadinessAdminPrev').addEventListener('click', function() {
                    if (currentPage > 0) {
                        currentPage--;
                        renderChart(currentPage);
                    }
                });

                document.getElementById('emergencyReadinessAdminNext').addEventListener('click', function() {
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
                <canvas id="adminInvestingHabitChart"></canvas>
            </div>
            <div class="chart-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 1rem; margin-top: 0.5rem;">
                <button id="adminInvestingHabitPrev" class="btn btn-sm btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: 0.25rem 0.5rem;" disabled>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span id="adminInvestingHabitPageInfo" style="font-size: 0.875rem; color: #6b7280;">Page 1</span>
                <button id="adminInvestingHabitNext" class="btn btn-sm btn-secondary" style="padding: 0.25rem 0.5rem;">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('adminInvestingHabitChart');
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

                    // Update pagination controls
                    document.getElementById('adminInvestingHabitPageInfo').textContent = `Page ${page + 1} of ${totalPages}`;
                    document.getElementById('adminInvestingHabitPrev').disabled = page === 0;
                    document.getElementById('adminInvestingHabitNext').disabled = page === totalPages - 1;
                    
                    document.getElementById('adminInvestingHabitPrev').style.opacity = page === 0 ? '0.5' : '1';
                    document.getElementById('adminInvestingHabitPrev').style.cursor = page === 0 ? 'not-allowed' : 'pointer';
                    document.getElementById('adminInvestingHabitNext').style.opacity = page === totalPages - 1 ? '0.5' : '1';
                    document.getElementById('adminInvestingHabitNext').style.cursor = page === totalPages - 1 ? 'not-allowed' : 'pointer';
                }

                document.getElementById('adminInvestingHabitPrev').addEventListener('click', function() {
                    if (currentPage > 0) {
                        currentPage--;
                        renderChart(currentPage);
                    }
                });

                document.getElementById('adminInvestingHabitNext').addEventListener('click', function() {
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
