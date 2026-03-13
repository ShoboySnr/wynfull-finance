@if($onboardings->count() > 0)
<div class="budget-confidence-card">
    <div class="card-header">
        <div class="header-content">
            <h3><i class="fas fa-chart-line"></i> Budget Confidence Progress</h3>
            <p class="card-subtitle">Track your budget understanding over time</p>
        </div>
        @if($onboardings->count() > 1)
            <div class="improvement-badge">
                @php
                    $first = $budgetConfidenceChart['data'][0] ?? 0;
                    $latest = $budgetConfidenceChart['data'][count($budgetConfidenceChart['data']) - 1] ?? 0;
                    $improvement = $latest - $first;
                @endphp
                @if($improvement > 0)
                    <span class="badge badge-success">
                        <i class="fas fa-arrow-up"></i> +{{ $improvement }}% improvement
                    </span>
                @elseif($improvement < 0)
                    <span class="badge badge-warning">
                        <i class="fas fa-arrow-down"></i> {{ $improvement }}%
                    </span>
                @else
                    <span class="badge badge-info">
                        <i class="fas fa-minus"></i> Consistent
                    </span>
                @endif
            </div>
        @endif
    </div>

    <div class="chart-container">
        <canvas id="budgetConfidenceChart"></canvas>
    </div>

    <div class="chart-stats">
        <div class="stat-item">
            <span class="stat-label">Total Assessments</span>
            <span class="stat-value">{{ $onboardings->count() }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">First Assessment</span>
            <span class="stat-value">{{ $onboardings->first()->completed_at->format('M d, Y') }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Latest Assessment</span>
            <span class="stat-value">{{ $onboardings->last()->completed_at->format('M d, Y') }}</span>
        </div>
        <div class="stat-item">
            <span class="stat-label">Current Level</span>
            <span class="stat-value">
                @php
                    $latestLevel = $onboardings->last()->answers['primary_goal'] ?? '';
                    $levelText = match($latestLevel) {
                        'not-confident' => 'Not Confident',
                        'somewhat-confident' => 'Somewhat Confident',
                        'confident' => 'Confident',
                        'very-confident' => 'Very Confident',
                        default => 'N/A'
                    };
                @endphp
                {{ $levelText }}
            </span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('budgetConfidenceChart');
    if (!ctx) return;

    const labels = @json($budgetConfidenceChart['labels']);
    const data = @json($budgetConfidenceChart['data']);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Budget Confidence Level',
                data: data,
                backgroundColor: 'rgba(14, 77, 164, 0.8)',
                borderColor: 'rgba(14, 77, 164, 1)',
                borderWidth: 2,
                borderRadius: 8,
                barThickness: 50,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed.y;
                            let level = '';
                            if (value === 25) level = 'Not Confident';
                            else if (value === 50) level = 'Somewhat Confident';
                            else if (value === 75) level = 'Confident';
                            else if (value === 100) level = 'Very Confident';
                            return level + ' (' + value + '%)';
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
                        },
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>

<style>
.budget-confidence-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-content h3 {
    margin: 0 0 0.5rem 0;
    color: var(--text-primary, #1f2937);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.25rem;
}

.header-content h3 i {
    color: var(--wynfull-blue, #0E4DA4);
}

.card-subtitle {
    margin: 0;
    color: var(--text-secondary, #6b7280);
    font-size: 0.9rem;
}

.improvement-badge .badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
}

.chart-container {
    position: relative;
    height: 300px;
    margin-bottom: 1.5rem;
}

.chart-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color, #e5e7eb);
}

.stat-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--text-secondary, #6b7280);
}

.stat-value {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary, #1f2937);
}

/* Dark mode support */
[data-theme="dark"] .budget-confidence-card {
    background: var(--card-bg-dark, #1f2937);
}

[data-theme="dark"] .header-content h3,
[data-theme="dark"] .stat-value {
    color: var(--text-primary-dark, #f9fafb);
}

[data-theme="dark"] .card-subtitle,
[data-theme="dark"] .stat-label {
    color: var(--text-secondary-dark, #d1d5db);
}

[data-theme="dark"] .chart-stats {
    border-top-color: var(--border-color-dark, #374151);
}

/* Responsive design */
@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .chart-container {
        height: 250px;
    }
    
    .chart-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@else
<div class="no-data-card">
    <div class="no-data-content">
        <i class="fas fa-chart-line"></i>
        <h3>No Assessment Data Yet</h3>
        <p>Complete your first onboarding assessment to start tracking your budget confidence progress.</p>
    </div>
</div>

<style>
.no-data-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 3rem 1.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.no-data-content {
    text-align: center;
    color: var(--text-secondary, #6b7280);
}

.no-data-content i {
    font-size: 3rem;
    color: var(--wynfull-blue, #0E4DA4);
    opacity: 0.3;
    margin-bottom: 1rem;
}

.no-data-content h3 {
    margin: 0 0 0.5rem 0;
    color: var(--text-primary, #1f2937);
}

.no-data-content p {
    margin: 0;
    font-size: 0.95rem;
}

[data-theme="dark"] .no-data-card {
    background: var(--card-bg-dark, #1f2937);
}

[data-theme="dark"] .no-data-content h3 {
    color: var(--text-primary-dark, #f9fafb);
}

[data-theme="dark"] .no-data-content p {
    color: var(--text-secondary-dark, #d1d5db);
}
</style>
@endif
