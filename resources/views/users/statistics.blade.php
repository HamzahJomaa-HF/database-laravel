@extends('layouts.app')

@section('title', 'User Statistics')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Users</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Statistics</li>
                        </ol>
                    </nav>
                    <h1 class="h2 fw-bold mb-1">
                        <i class="bi bi-bar-chart me-2 text-primary"></i>User Statistics
                    </h1>
                    <p class="text-muted mb-0">Comprehensive analytics and metrics for user data</p>
                </div>
               <div class="d-flex gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Key Performance Indicators --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted fw-normal">Total Users</h6>
                            <h3 class="fw-bold text-primary">{{ $stats['total_users'] ?? 0 }}</h3>
                            <small class="text-muted">All time registrations</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1 text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted fw-normal">Active This Month</h6>
                            <h3 class="fw-bold text-success">{{ $stats['new_this_month'] ?? 0 }}</h3>
                            <small class="text-muted">New registrations this month</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-month fs-1 text-success opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted fw-normal">Weekly Growth</h6>
                            <h3 class="fw-bold text-warning">{{ $stats['new_this_week'] ?? 0 }}</h3>
                            <small class="text-muted">
                                This week • 
                                <span class="{{ ($stats['weekly_growth'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ ($stats['weekly_growth'] ?? 0) >= 0 ? '+' : '' }}{{ $stats['weekly_growth'] ?? 0 }}%
                                </span>
                            </small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-graph-up-arrow fs-1 text-warning opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted fw-normal">Avg. Daily Rate</h6>
                            <h3 class="fw-bold text-info">{{ $stats['avg_daily_registrations'] ?? 0 }}</h3>
                            <small class="text-muted">Registrations per day</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-speedometer2 fs-1 text-info opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="row mb-4">
        {{-- Doughnut Chart: User Type Distribution --}}
        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-tags me-2 text-primary"></i>User Type Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 250px;">
                        <canvas id="userTypeChart"></canvas>
                    </div>
                    <div class="chart-legend mt-3" id="userTypeChartLegend"></div>
                </div>
            </div>
        </div>

        {{-- Doughnut Chart: Scope Distribution --}}
        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-globe me-2 text-primary"></i>Scope Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 250px;">
                        <canvas id="scopeChart"></canvas>
                    </div>
                    <div class="chart-legend mt-3" id="scopeChartLegend"></div>
                </div>
            </div>
        </div>

        {{-- Doughnut Chart: High Profile Distribution --}}
        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom py-3">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-star me-2 text-primary"></i>High Profile Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 250px;">
                        <canvas id="profileChart"></canvas>
                    </div>
                    <div class="chart-legend mt-3" id="profileChartLegend"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-people-fill fs-1 text-primary"></i>
                    </div>
                    <h3 class="fw-bold">{{ $stats['beneficiary_count'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Beneficiaries</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-briefcase-fill fs-1 text-success"></i>
                    </div>
                    <h3 class="fw-bold">{{ $stats['stakeholder_count'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Stakeholders</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-gender-male fs-1 text-info"></i>
                    </div>
                    <h3 class="fw-bold">{{ $stats['male_count'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Male Users</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-gender-female fs-1 text-pink"></i>
                    </div>
                    <h3 class="fw-bold">{{ $stats['female_count'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Female Users</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-calendar-check fs-1 text-warning"></i>
                    </div>
                    <h3 class="fw-bold">{{ $stats['today_registrations'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Today's Registrations</p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-calculator fs-1 text-secondary"></i>
                    </div>
                    <h3 class="fw-bold">{{ $stats['avg_age'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Average Age</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- PAGE-SPECIFIC STYLES --}}
@section('styles')
<style>
.card {
    border-radius: 10px;
    border: 1px solid #dee2e6;
    overflow: hidden;
}

.border-start {
    border-left-width: 4px !important;
}

.chart-legend {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
    margin-top: 15px;
}

.legend-item {
    display: flex;
    align-items: center;
    margin-right: 15px;
    margin-bottom: 5px;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
    margin-right: 5px;
}

.legend-label {
    font-size: 0.8rem;
    color: #6c757d;
}

.text-pink {
    color: #e83e8c !important;
}

.table th {
    background: #f8f9fa;
    border-bottom: 2px solid #e9ecef;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.table td {
    padding: 0.75rem;
    border-color: #f8f9fa;
    vertical-align: middle;
}
</style>
@endsection

{{-- PAGE-SPECIFIC SCRIPTS --}}
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Type Distribution
    // Real counts can be extremely skewed (e.g. 39,173 vs 23), which renders as an
    // invisible sliver on a doughnut chart. We keep the true counts for the tooltip
    // (via actualData) but floor the rendered slice so every non-zero type stays visible.
    const userTypeActual = [{{ $stats['beneficiary_count'] ?? 0 }}, {{ $stats['stakeholder_count'] ?? 0 }}];
    const userTypeData = {
        labels: ['Beneficiary', 'Stakeholder'],
        datasets: [{
            data: applyMinimumSlice(userTypeActual, 5),
            actualData: userTypeActual,
            backgroundColor: [
                '#1cc88a', // Green for Beneficiary
                '#4e73df'  // Blue for Stakeholder
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    };

    // Scope Distribution - using real data from controller
    const scopeData = {
        labels: ['International', 'Regional', 'National', 'Local'],
        datasets: [{
            data: [
                {{ $stats['scope_distribution']->where('scope', 'International')->first()?->count ?? 0 }},
                {{ $stats['scope_distribution']->where('scope', 'Regional')->first()?->count ?? 0 }},
                {{ $stats['scope_distribution']->where('scope', 'National')->first()?->count ?? 0 }},
                {{ $stats['scope_distribution']->where('scope', 'Local')->first()?->count ?? 0 }}
            ],
            backgroundColor: [
                '#4e73df', // Blue for International
                '#1cc88a', // Green for Regional
                '#f6c23e', // Yellow for National
                '#36b9cc'  // Teal for Local
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    };

    // High Profile Distribution - using real data
    const profileData = {
        labels: ['High Profile', 'Regular'],
        datasets: [{
            data: [
                {{ $stats['high_profile_count'] ?? 0 }},
                {{ $stats['regular_profile_count'] ?? 0 }}
            ],
            backgroundColor: [
                '#e74a3b', // Red for High Profile
                '#858796'  // Gray for Regular
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    };

    // Floors any non-zero value below minPercent of the total up to that minimum,
    // so it still renders as a visible slice on a doughnut chart.
    function applyMinimumSlice(rawValues, minPercent = 5) {
        const total = rawValues.reduce((a, b) => a + b, 0);
        if (total <= 0) return rawValues.slice();
        const minValue = total * (minPercent / 100);
        return rawValues.map(v => (v > 0 && v < minValue) ? minValue : v);
    }

    // Create Doughnut Chart
    function createDoughnutChart(canvasId, data, legendId) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;
        
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const dataset = context.dataset;
                                // Use real (unfloored) counts for the tooltip when available,
                                // so a visually-boosted slice still reports its true count/percentage.
                                const values = dataset.actualData || dataset.data;
                                const value = values[context.dataIndex] || 0;
                                const total = values.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Create custom legend
        createLegend(legendId, data.labels, data.datasets[0].backgroundColor);
        
        return chart;
    }

    // Create custom legend
    function createLegend(containerId, labels, colors) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        container.innerHTML = '';
        
        labels.forEach((label, index) => {
            const legendItem = document.createElement('div');
            legendItem.className = 'legend-item';
            
            const colorBox = document.createElement('div');
            colorBox.className = 'legend-color';
            colorBox.style.backgroundColor = colors[index];
            
            const labelSpan = document.createElement('span');
            labelSpan.className = 'legend-label';
            labelSpan.textContent = `${label}`;
            
            legendItem.appendChild(colorBox);
            legendItem.appendChild(labelSpan);
            container.appendChild(legendItem);
        });
    }

    // Initialize all charts
    try {
        const userTypeChart = createDoughnutChart('userTypeChart', userTypeData, 'userTypeChartLegend');
        const scopeChart = createDoughnutChart('scopeChart', scopeData, 'scopeChartLegend');
        const profileChart = createDoughnutChart('profileChart', profileData, 'profileChartLegend');

        if (!userTypeChart || !scopeChart || !profileChart) {
            console.error('One or more charts failed to initialize');
        }
    } catch (error) {
        console.error('Error initializing charts:', error);
    }
});
</script>
@endsection