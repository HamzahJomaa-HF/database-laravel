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
                    <a href="{{ route('users.export.excel') }}" class="btn btn-success">
                        <i class="bi bi-download me-1"></i> Export to Excel
                    </a>
                    <a href="{{ route('users.statistics') }}" class="btn btn-outline-primary">
                        <i class="bi bi-bar-chart me-1"></i> View Statistics
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Registration Patterns Section --}}
    <div class="row mb-4">
        {{-- Registration by Day of Week --}}
        @if(isset($reports['registration_patterns']['by_day_of_week']) && $reports['registration_patterns']['by_day_of_week']->count() > 0)
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-calendar-week me-2"></i>Registration by Day of Week
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th class="text-end">Registrations</th>
                                    <th class="text-end">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalRegistrations = $reports['registration_patterns']['by_day_of_week']->sum('count');
                                @endphp
                                @foreach($reports['registration_patterns']['by_day_of_week'] as $day)
                                <tr>
                                    <td>{{ $day->day }}</td>
                                    <td class="text-end fw-semibold">{{ $day->count }}</td>
                                    <td class="text-end text-muted">
                                        {{ $totalRegistrations > 0 ? round(($day->count / $totalRegistrations) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Registration by User Type --}}
        @if(isset($reports['registration_patterns']['by_type']) && $reports['registration_patterns']['by_type']->count() > 0)
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-tags me-2"></i>Registration by User Type
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
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
                                    <td>{{ $type->type ?? 'Not Specified' }}</td>
                                    <td class="text-end fw-semibold">{{ $type->count }}</td>
                                    <td class="text-end text-muted">
                                        {{ $totalByType > 0 ? round(($type->count / $totalByType) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Registration by Month --}}
        @if(isset($reports['registration_patterns']['by_month']) && $reports['registration_patterns']['by_month']->count() > 0)
        <div class="col-xl-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-calendar-month me-2"></i>Registration by Month
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Registrations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reports['registration_patterns']['by_month'] as $month)
                                <tr>
                                    <td>{{ trim($month->month) }}</td>
                                    <td class="text-end fw-semibold">{{ $month->count }}</td>
                                </tr>
                                @endforeach
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
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-gender-ambiguous me-2"></i>Gender Breakdown
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                @foreach($reports['demographic_breakdown']['by_gender'] as $gender)
                                <tr>
                                    <td>{{ $gender->gender ?? 'Not Specified' }}</td>
                                    <td class="text-end fw-semibold">{{ $gender->count }}</td>
                                </tr>
                                @endforeach
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
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-heart me-2"></i>Marital Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                @foreach($reports['demographic_breakdown']['by_marital_status'] as $marital)
                                <tr>
                                    <td>{{ $marital->marital_status ?? 'Not Specified' }}</td>
                                    <td class="text-end fw-semibold">{{ $marital->count }}</td>
                                </tr>
                                @endforeach
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
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-briefcase me-2"></i>Employment Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                @foreach($reports['demographic_breakdown']['by_employment'] as $employment)
                                <tr>
                                    <td>{{ $employment->employment_status ?? 'Not Specified' }}</td>
                                    <td class="text-end fw-semibold">{{ $employment->count }}</td>
                                </tr>
                                @endforeach
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
            <div class="card shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-list-ul me-2"></i>Detailed User Report
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
                                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
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
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-info">{{ $user->type ?? 'Stakeholder' }}</span>
                                            <span class="badge bg-secondary">{{ $user->employment_status ?? 'Not specified' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class="bi bi-people display-4 d-block mb-3 opacity-50"></i>
                                        <h5>No users found</h5>
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
@endsection