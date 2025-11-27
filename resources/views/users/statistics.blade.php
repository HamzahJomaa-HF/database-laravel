@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-primary mb-1">
                        <i class="bi bi-bar-chart me-2"></i>User Statistics
                    </h1>
                    <p class="text-muted mb-0">Comprehensive analytics and metrics for user data</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.reports') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                        <i class="bi bi-file-earmark-text me-1"></i> View Reports
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center">
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
                            <h3 class="fw-bold text-primary">{{ $stats['total_users'] }}</h3>
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
                            <h6 class="text-muted fw-normal">Active Growth</h6>
                            <h3 class="fw-bold text-success">{{ $stats['new_this_week'] }}</h3>
                            <small class="text-muted">This week • 
                                <span class="{{ $stats['weekly_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $stats['weekly_growth'] >= 0 ? '+' : '' }}{{ $stats['weekly_growth'] }}%
                                </span>
                            </small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-plus fs-1 text-success opacity-25"></i>
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
                            <h6 class="text-muted fw-normal">Monthly Growth</h6>
                            <h3 class="fw-bold text-warning">{{ $stats['new_this_month'] }}</h3>
                            <small class="text-muted">This month • 
                                <span class="{{ $stats['monthly_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $stats['monthly_growth'] >= 0 ? '+' : '' }}{{ $stats['monthly_growth'] }}%
                                </span>
                            </small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-month fs-1 text-warning opacity-25"></i>
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
                            <h3 class="fw-bold text-info">{{ $stats['avg_daily_registrations'] }}</h3>
                            <small class="text-muted">Registrations per day</small>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-graph-up fs-1 text-info opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Growth Analytics --}}
    <div class="row mb-4">
        <div class="col-xl-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-graph-up-arrow me-2"></i>User Growth Trends (Last 12 Months)
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($stats['yearly_growth']) && count($stats['yearly_growth']) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-end">Registrations</th>
                                        <th class="text-end">Monthly Growth</th>
                                        <th class="text-end">Cumulative</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cumulative = 0;
                                        $previousMonth = null;
                                    @endphp
                                    @foreach($stats['yearly_growth'] as $growth)
                                        @php
                                            $cumulative += $growth->count;
                                            $monthlyGrowth = $previousMonth !== null ? 
                                                round((($growth->count - $previousMonth) / $previousMonth) * 100, 1) : 0;
                                            $previousMonth = $growth->count;
                                        @endphp
                                        <tr>
                                            <td>{{ $growth->month }}</td>
                                            <td class="text-end fw-semibold">{{ $growth->count }}</td>
                                            <td class="text-end {{ $monthlyGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                                @if($monthlyGrowth > 0)
                                                    <i class="bi bi-arrow-up"></i>
                                                @elseif($monthlyGrowth < 0)
                                                    <i class="bi bi-arrow-down"></i>
                                                @endif
                                                {{ abs($monthlyGrowth) }}%
                                            </td>
                                            <td class="text-end text-muted">{{ $cumulative }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No yearly growth data available</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-speedometer2 me-2"></i>Performance Indicators
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Registration Velocity</span>
                            <span class="fw-semibold {{ $stats['registration_velocity'] >= 5 ? 'text-success' : ($stats['registration_velocity'] >= 2 ? 'text-warning' : 'text-danger') }}">
                                {{ $stats['registration_velocity'] }}/day
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $stats['registration_velocity'] >= 5 ? 'bg-success' : ($stats['registration_velocity'] >= 2 ? 'bg-warning' : 'bg-danger') }}" 
                                 style="width: {{ min($stats['registration_velocity'] * 20, 100) }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Data Health Score</span>
                            <span class="fw-semibold {{ $stats['data_health_score'] >= 80 ? 'text-success' : ($stats['data_health_score'] >= 60 ? 'text-warning' : 'text-danger') }}">
                                {{ $stats['data_health_score'] }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $stats['data_health_score'] >= 80 ? 'bg-success' : ($stats['data_health_score'] >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                 style="width: {{ $stats['data_health_score'] }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">User Engagement</span>
                            <span class="fw-semibold {{ $stats['user_engagement'] >= 70 ? 'text-success' : ($stats['user_engagement'] >= 40 ? 'text-warning' : 'text-danger') }}">
                                {{ $stats['user_engagement'] }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $stats['user_engagement'] >= 70 ? 'bg-success' : ($stats['user_engagement'] >= 40 ? 'bg-warning' : 'bg-danger') }}" 
                                 style="width: {{ $stats['user_engagement'] }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Retention Rate</span>
                            <span class="fw-semibold {{ $stats['retention_rate'] >= 85 ? 'text-success' : ($stats['retention_rate'] >= 70 ? 'text-warning' : 'text-danger') }}">
                                {{ $stats['retention_rate'] }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $stats['retention_rate'] >= 85 ? 'bg-success' : ($stats['retention_rate'] >= 70 ? 'bg-warning' : 'bg-danger') }}" 
                                 style="width: {{ $stats['retention_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- User Composition --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-pie-chart me-2"></i>User Composition Analysis
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Gender Distribution --}}
                        <div class="col-xl-4 col-md-6 mb-4">
                            <h6 class="fw-semibold mb-3 text-center">
                                <i class="bi bi-gender-ambiguous me-2"></i>Gender Distribution
                            </h6>
                            @if($stats['gender_distribution']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            @foreach($stats['gender_distribution'] as $gender)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                                        {{ $gender->gender ?? 'Not Specified' }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-semibold">{{ $gender->count }}</td>
                                                <td class="text-end text-muted">
                                                    {{ round(($gender->count / $stats['total_users']) * 100, 1) }}%
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center mb-0">No gender data available</p>
                            @endif
                        </div>

                        {{-- User Type --}}
                        <div class="col-xl-4 col-md-6 mb-4">
                            <h6 class="fw-semibold mb-3 text-center">
                                <i class="bi bi-tags me-2"></i>User Type
                            </h6>
                            @if($stats['type_distribution']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            @foreach($stats['type_distribution'] as $type)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-info bg-opacity-10 text-info">
                                                        {{ $type->type ?? 'Not Specified' }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-semibold">{{ $type->count }}</td>
                                                <td class="text-end text-muted">
                                                    {{ round(($type->count / $stats['total_users']) * 100, 1) }}%
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center mb-0">No type data available</p>
                            @endif
                        </div>

                        {{-- Employment Status --}}
                        <div class="col-xl-4 col-md-6 mb-4">
                            <h6 class="fw-semibold mb-3 text-center">
                                <i class="bi bi-briefcase me-2"></i>Employment Status
                            </h6>
                            @if($stats['employment_distribution']->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            @foreach($stats['employment_distribution'] as $employment)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                                        {{ $employment->employment_status ?? 'Not Specified' }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-semibold">{{ $employment->count }}</td>
                                                <td class="text-end text-muted">
                                                    {{ round(($employment->count / $stats['total_users']) * 100, 1) }}%
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted text-center mb-0">No employment data available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Metrics --}}
    <div class="row mb-4">
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-heart me-2"></i>Marital Status Distribution
                    </h6>
                </div>
                <div class="card-body">
                    @if($stats['marital_status_distribution']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['marital_status_distribution'] as $marital)
                                    <tr>
                                        <td>{{ $marital->marital_status ?? 'Not Specified' }}</td>
                                        <td class="text-end fw-semibold">{{ $marital->count }}</td>
                                        <td class="text-end text-muted">
                                            {{ round(($marital->count / $stats['total_users']) * 100, 1) }}%
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No marital status data available</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Peak Registration Times --}}
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2"></i>Peak Registration Times
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($stats['peak_registration_times']) && count($stats['peak_registration_times']) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Time Period</th>
                                        <th class="text-end">Registrations</th>
                                        <th class="text-end">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['peak_registration_times'] as $peak)
                                    <tr>
                                        <td>{{ $peak->time_period }}</td>
                                        <td class="text-end fw-semibold">{{ $peak->count }}</td>
                                        <td class="text-end text-muted">
                                            {{ round(($peak->count / $stats['total_users']) * 100, 1) }}%
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No peak time data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Insights --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-lightbulb me-2"></i>Quick Insights
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <div class="fs-2 fw-bold text-primary">{{ $stats['beneficiary_ratio'] }}%</div>
                                <div class="text-muted">Beneficiary Ratio</div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <div class="fs-2 fw-bold text-success">{{ $stats['active_ratio'] }}%</div>
                                <div class="text-muted">Active User Ratio</div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <div class="fs-2 fw-bold text-info">{{ $stats['completion_ratio'] }}%</div>
                                <div class="text-muted">Profile Completion</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 8px;
    border: 1px solid #e9ecef;
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

.badge {
    font-size: 0.75rem;
    padding: 0.4em 0.65em;
    font-weight: 500;
    border-radius: 4px;
}

.progress {
    border-radius: 4px;
}

.border-start {
    border-left-width: 4px !important;
}
</style>
@endsection