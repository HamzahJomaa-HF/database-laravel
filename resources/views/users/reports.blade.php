@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 fw-bold text-primary mb-1">
                        <i class="bi bi-file-earmark-text me-2"></i>User Reports
                    </h1>
                    <p class="text-muted mb-0">Detailed user reports and data analysis</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.export.excel') }}" class="btn btn-success btn-sm d-flex align-items-center">
                        <i class="bi bi-download me-1"></i> Export to Excel
                    </a>
                    <a href="{{ route('users.statistics') }}" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                        <i class="bi bi-bar-chart me-1"></i> View Statistics
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Metrics --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="fw-bold text-primary">{{ $reports['detailed_users']->count() }}</h3>
                    <p class="text-muted mb-0">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                <div class="card-body text-center">
                    @php
                        $beneficiaries = $reports['registration_patterns']['by_type']->where('type', 'Beneficiary')->first();
                    @endphp
                    <h3 class="fw-bold text-success">{{ $beneficiaries->count ?? 0 }}</h3>
                    <p class="text-muted mb-0">Beneficiaries</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                <div class="card-body text-center">
                    @php
                        $stakeholders = $reports['registration_patterns']['by_type']->where('type', 'Stakeholder')->first();
                    @endphp
                    <h3 class="fw-bold text-info">{{ $stakeholders->count ?? 0 }}</h3>
                    <p class="text-muted mb-0">Stakeholders</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    @php
                        $employed = $reports['demographic_breakdown']['by_employment']->where('employment_status', 'Employed')->first();
                    $totalUsers = $reports['detailed_users']->count();
                        $employedCount = $employed->count ?? 0;
                        $employedPercentage = $totalUsers > 0 ? round(($employedCount / $totalUsers) * 100, 1) : 0;
                    @endphp
                    <h3 class="fw-bold text-warning">{{ $employedCount }}</h3>
                    <p class="text-muted mb-0">Employed ({{ $employedPercentage }}%)</p>
                </div>
            </div>
        </div>
    </div>

    

    {{-- Registration Timeline --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-calendar-range me-2 text-primary"></i>Registration Timeline (Last 6 Months)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">New Registrations</th>
                                    <th class="text-end">Growth %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($reports['registration_timeline']) && $reports['registration_timeline']->count() > 0)
                                    @php
                                        $previousCount = null;
                                    @endphp
                                    @foreach($reports['registration_timeline'] as $timeline)
                                        @php
                                            $growth = $previousCount !== null && $previousCount > 0 
                                                ? round((($timeline->count - $previousCount) / $previousCount) * 100, 1)
                                                : 0;
                                            $previousCount = $timeline->count;
                                        @endphp
                                        <tr>
                                            <td>{{ $timeline->month }}</td>
                                            <td class="text-end fw-semibold">{{ $timeline->count }}</td>
                                            <td class="text-end {{ $growth > 0 ? 'text-success' : ($growth < 0 ? 'text-danger' : 'text-muted') }}">
                                                @if($growth > 0)
                                                    <i class="bi bi-arrow-up"></i>
                                                @elseif($growth < 0)
                                                    <i class="bi bi-arrow-down"></i>
                                                @endif
                                                {{ abs($growth) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">
                                            No registration timeline data available
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity & Data Quality --}}
    <div class="row mb-4">
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Recent Activity (Last 30 Days)
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($reports['recent_activity']) && $reports['recent_activity']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th class="text-end">Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports['recent_activity'] as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <span class="text-primary fw-bold" style="font-size: 0.7rem;">
                                                            {{ strtoupper(substr($user->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                                        <small class="text-muted">{{ $user->type ?? 'Stakeholder' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <small class="text-muted">
                                                    {{ $user->updated_at->format('M d, Y') }}<br>
                                                    <span class="very-small">{{ $user->updated_at->format('h:i A') }}</span>
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No recent activity found</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-exclamation-triangle me-2 text-primary"></i>Data Completeness
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $totalUsers = $reports['detailed_users']->count();
                        $completionRates = $reports['data_completeness'] ?? [];
                    @endphp
                    
                    @foreach($completionRates as $field => $rate)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $field)) }}</span>
                                <span class="fw-semibold {{ $rate >= 80 ? 'text-success' : ($rate >= 60 ? 'text-warning' : 'text-danger') }}">
                                    {{ $rate }}%
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar {{ $rate >= 80 ? 'bg-success' : ($rate >= 60 ? 'bg-warning' : 'bg-danger') }}" 
                                     role="progressbar" style="width: {{ $rate }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    
                    @if(empty($completionRates))
                        <p class="text-muted text-center mb-0">No completeness data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Registration Patterns Section --}}
    <div class="row mb-4">
        {{-- Registration by User Type --}}
        @if(isset($reports['registration_patterns']['by_type']) && $reports['registration_patterns']['by_type']->count() > 0)
        <div class="col-xl-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-tags me-2 text-primary"></i>Registration by User Type
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>User Type</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalByType = $reports['registration_patterns']['by_type']->sum('count');
                                @endphp
                                @foreach($reports['registration_patterns']['by_type'] as $type)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            {{ $type->type ?? 'Not Specified' }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold">{{ $type->count }}</td>
                                    <td class="text-end text-muted">
                                        {{ $totalByType > 0 ? round(($type->count / $totalByType) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @endforeach
                                @if($totalByType > 0)
                                <tr class="table-light">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end fw-bold">{{ $totalByType }}</td>
                                    <td class="text-end fw-bold">100%</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Demographic Breakdown Section --}}
    <div class="row mb-4">
        {{-- Gender Breakdown --}}
        @if(isset($reports['demographic_breakdown']['by_gender']) && $reports['demographic_breakdown']['by_gender']->count() > 0)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-gender-ambiguous me-2 text-primary"></i>Gender Breakdown
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Gender</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalGender = $reports['demographic_breakdown']['by_gender']->sum('count');
                                @endphp
                                @foreach($reports['demographic_breakdown']['by_gender'] as $gender)
                                <tr>
                                    <td>{{ $gender->gender ?? 'Not Specified' }}</td>
                                    <td class="text-end fw-semibold">{{ $gender->count }}</td>
                                    <td class="text-end text-muted">
                                        {{ $totalGender > 0 ? round(($gender->count / $totalGender) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @endforeach
                                @if($totalGender > 0)
                                <tr class="table-light">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end fw-bold">{{ $totalGender }}</td>
                                    <td class="text-end fw-bold">100%</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Marital Status Breakdown --}}
        @if(isset($reports['demographic_breakdown']['by_marital_status']) && $reports['demographic_breakdown']['by_marital_status']->count() > 0)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-heart me-2 text-primary"></i>Marital Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalMarital = $reports['demographic_breakdown']['by_marital_status']->sum('count');
                                @endphp
                                @foreach($reports['demographic_breakdown']['by_marital_status'] as $marital)
                                <tr>
                                    <td>{{ $marital->marital_status ?? 'Not Specified' }}</td>
                                    <td class="text-end fw-semibold">{{ $marital->count }}</td>
                                    <td class="text-end text-muted">
                                        {{ $totalMarital > 0 ? round(($marital->count / $totalMarital) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @endforeach
                                @if($totalMarital > 0)
                                <tr class="table-light">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end fw-bold">{{ $totalMarital }}</td>
                                    <td class="text-end fw-bold">100%</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Employment Breakdown --}}
        @if(isset($reports['demographic_breakdown']['by_employment']) && $reports['demographic_breakdown']['by_employment']->count() > 0)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-briefcase me-2 text-primary"></i>Employment Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Status</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalEmployment = $reports['demographic_breakdown']['by_employment']->sum('count');
                                @endphp
                                @foreach($reports['demographic_breakdown']['by_employment'] as $employment)
                                <tr>
                                    <td>{{ $employment->employment_status ?? 'Not Specified' }}</td>
                                    <td class="text-end fw-semibold">{{ $employment->count }}</td>
                                    <td class="text-end text-muted">
                                        {{ $totalEmployment > 0 ? round(($employment->count / $totalEmployment) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @endforeach
                                @if($totalEmployment > 0)
                                <tr class="table-light">
                                    <td><strong>Total</strong></td>
                                    <td class="text-end fw-bold">{{ $totalEmployment }}</td>
                                    <td class="text-end fw-bold">100%</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Detailed User Report --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-list-ul me-2 text-primary"></i>Detailed User Report
                    </h6>
                    <span class="badge bg-primary">{{ $reports['detailed_users']->count() }} users</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">User Information</th>
                                    <th>Contact Details</th>
                                    <th>Personal Info</th>
                                    <th>Status</th>
                                    <th>Registration Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports['detailed_users'] as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <span class="text-primary fw-bold fs-6">
                                                    {{ strtoupper(substr($user->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</h6>
                                                @if($user->middle_name)
                                                <small class="text-muted">{{ $user->middle_name }}</small>
                                                @endif
                                                <div class="small text-muted">
                                                    ID: {{ $user->identification_id ?? 'Not provided' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            @if($user->phone_number)
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="bi bi-telephone text-muted me-2"></i>
                                                <span>{{ $user->phone_number }}</span>
                                            </div>
                                            @endif
                                            @if($user->email)
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-envelope text-muted me-2"></i>
                                                <span>{{ $user->email }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            @if($user->dob)
                                            <div class="mb-1">
                                                <strong class="text-muted">DOB:</strong> 
                                                {{ \Carbon\Carbon::parse($user->dob)->format('M d, Y') }}
                                            </div>
                                            @endif
                                            @if($user->gender)
                                            <div class="mb-1">
                                                <strong class="text-muted">Gender:</strong> {{ $user->gender }}
                                            </div>
                                            @endif
                                            @if($user->marital_status)
                                            <div>
                                                <strong class="text-muted">Marital:</strong> {{ $user->marital_status }}
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-primary">{{ $user->type ?? 'Stakeholder' }}</span>
                                            <span class="badge bg-secondary">{{ $user->employment_status ?? 'Not specified' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </small>
                                        <div class="very-small text-muted">
                                            {{ $user->created_at->format('h:i A') }}
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-people display-4 d-block mb-3 opacity-50 text-muted"></i>
                                        <h5 class="text-muted">No users found</h5>
                                        <p class="text-muted mb-0">No user data available for reporting.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.875rem;
}

.avatar-xs {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.75rem;
}

.very-small {
    font-size: 0.75rem;
}

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
    padding: 1rem 0.75rem;
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
    border-radius: 3px;
}
</style>
@endsection