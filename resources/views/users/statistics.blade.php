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
                    <a href="{{ route('users.reports') }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-text me-1"></i> View Reports
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Key Metrics --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted fw-normal">Total Users</h6>
                            <h3 class="fw-bold text-primary">{{ $stats['total_users'] }}</h3>
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
                            <h6 class="text-muted fw-normal">New This Week</h6>
                            <h3 class="fw-bold text-success">{{ $stats['new_this_week'] }}</h3>
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
                            <h6 class="text-muted fw-normal">New This Month</h6>
                            <h3 class="fw-bold text-warning">{{ $stats['new_this_month'] }}</h3>
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
                            <h6 class="text-muted fw-normal">Avg. Daily</h6>
                            <h3 class="fw-bold text-info">{{ $stats['avg_daily_registrations'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-graph-up fs-1 text-info opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Distribution Charts --}}
    <div class="row mb-4">
        {{-- Gender Distribution --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-gender-ambiguous me-2"></i>Gender Distribution
                    </h6>
                </div>
                <div class="card-body">
                    @if($stats['gender_distribution']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    @foreach($stats['gender_distribution'] as $gender)
                                    <tr>
                                        <td>{{ $gender->gender ?? 'Not Specified' }}</td>
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
            </div>
        </div>

        {{-- Employment Status --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-briefcase me-2"></i>Employment Status
                    </h6>
                </div>
                <div class="card-body">
                    @if($stats['employment_distribution']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    @foreach($stats['employment_distribution'] as $employment)
                                    <tr>
                                        <td>{{ $employment->employment_status ?? 'Not Specified' }}</td>
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

        {{-- User Type --}}
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-tags me-2"></i>User Type
                    </h6>
                </div>
                <div class="card-body">
                    @if($stats['type_distribution']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    @foreach($stats['type_distribution'] as $type)
                                    <tr>
                                        <td>{{ $type->type ?? 'Not Specified' }}</td>
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
            </div>
        </div>
    </div>

    {{-- Marital Status --}}
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

        {{-- Registration Trends --}}
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-graph-up me-2"></i>Registration Trends (Last 30 Days)
                    </h6>
                </div>
                <div class="card-body">
                    @if($stats['registration_trends']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-end">Registrations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['registration_trends'] as $trend)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($trend->date)->format('M d, Y') }}</td>
                                        <td class="text-end fw-semibold">{{ $trend->count }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No registration data available for the last 30 days</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection