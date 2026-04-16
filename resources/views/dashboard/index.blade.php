{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - Hariri Foundation')

@section('styles')
    <style>
        :root {
            --sidebar-text: #ecf0f1;
            --primary-color: #3498db;
            --success-color: #2ecc71;
            --info-color: #17a2b8;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }

        /* Override any conflicting styles */
        .main-wrapper {
            background-color: #f8f9fa;
        }

        .card-header-sidebar {
            background: linear-gradient(135deg, var(--sidebar-bg) 0%, #34495e 100%);
            color: white;
            border: none;
            padding: 1rem 1.25rem;
            border-radius: 8px 8px 0 0;
        }

        .dashboard-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            background-color: white;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .dashboard-card .card-body {
            padding: 1.5rem;
            flex: 1;
            overflow: hidden;
        }

        .module-grid-container {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 1rem;
            padding-right: 5px;
        }

        .module-grid-container::-webkit-scrollbar {
            width: 6px;
        }

        .module-grid-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .module-grid-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .access-level-badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
        }

        .access-view { background-color: #95a5a6 !important; }
        .access-create { background-color: var(--info-color) !important; }
        .access-edit { background-color: var(--warning-color) !important; }
        .access-delete { background-color: var(--danger-color) !important; }
        .access-manage { background-color: var(--primary-color) !important; }

        .quick-access-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s ease;
            height: 100%;
            text-decoration: none !important;
            color: inherit;
            background-color: white;
        }

        .quick-access-card:hover {
            border-color: var(--primary-color);
            background-color: #f8f9fa;
            transform: translateY(-3px);
        }

        .quick-access-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            color: white;
            font-size: 1.25rem;
        }

        .user-profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            padding: 1.25rem;
            color: white;
            margin-bottom: 1.5rem;
        }

        .user-avatar {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-right: 1rem;
        }

        .compact-text {
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .compact-text-sm {
            font-size: 0.8rem;
        }

        .module-count-badge {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .row.equal-height {
            display: flex;
            flex-wrap: wrap;
        }

        .row.equal-height > [class*='col-'] {
            display: flex;
            flex-direction: column;
        }

        @media (max-width: 768px) {
            .quick-access-card .card-body {
                padding: 1rem;
            }
            
            .user-avatar {
                width: 60px;
                height: 60px;
                font-size: 1.75rem;
            }
        }
    </style>
@endsection

@section('content')
<div class="dashboard-content">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="user-profile-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <h1 class="h4 mb-1">Welcome back, {{ $employee->first_name }}!</h1>
                            <p class="mb-0 opacity-90 compact-text">
                                <i class="bi bi-calendar-check me-1"></i>
                                {{ now()->format('l, F j, Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-light text-dark p-2 compact-text">
                            <i class="bi bi-shield-check me-1"></i>
                            Employee Access
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="row equal-height g-4">
        <!-- Left Column - User Information -->
        <div class="col-lg-4">
            <div class="dashboard-card h-100">
                <div class="card-header-sidebar">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-badge me-2"></i>
                        <h5 class="mb-0 compact-text">Employee Profile</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-light rounded-circle p-2 d-inline-block">
                            <i class="bi bi-person fs-2 text-primary"></i>
                        </div>
                        <h4 class="mt-2 mb-1 h5">{{ $employee->first_name }} {{ $employee->last_name }}</h4>
                        <p class="text-muted compact-text-sm">{{ $employee->email }}</p>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <label class="form-label text-muted small mb-1">Employee ID</label>
                                <div class="fw-bold compact-text">{{ $employee->external_id ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <label class="form-label text-muted small mb-1">Status</label>
                                <div>
                                    @php
                                        if (!$employee->relationLoaded('credentials')) {
                                            $employee->load('credentials');
                                        }
                                    @endphp
                                    @if($employee->isActive())
                                        <span class="badge bg-success compact-text-sm">
                                            <i class="bi bi-check-circle me-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger compact-text-sm">
                                            <i class="bi bi-x-circle me-1"></i>Inactive
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label text-muted small mb-2">Additional Information</label>
                        <div class="list-group list-group-flush compact-text">
                            <div class="list-group-item d-flex justify-content-between px-0 py-1">
                                <span>Employee Type:</span>
                                <span class="fw-bold">{{ $employee->employee_type ?? 'N/A' }}</span>
                            </div>
                            @if($employee->start_date)
                            <div class="list-group-item d-flex justify-content-between px-0 py-1">
                                <span>Start Date:</span>
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($employee->start_date)->format('M d, Y') }}</span>
                            </div>
                            @endif
                            @if($employee->role)
                            <div class="list-group-item d-flex justify-content-between px-0 py-1">
                                <span>Role:</span>
                                <span class="fw-bold">{{ $employee->role->role_name }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Column - Module Access -->
        <div class="col-lg-8">
            <div class="dashboard-card h-100">
                <div class="card-header-sidebar d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shield-lock me-2"></i>
                        <h5 class="mb-0 compact-text">Module Access Permissions</h5>
                    </div>
                    @php
                        $accessibleModules = $employee->getAccessibleModules();
                        $moduleCount = $accessibleModules->count();
                    @endphp
                    <span class="module-count-badge compact-text-sm">
                        <i class="bi bi-grid-1x2 me-1"></i>{{ $moduleCount }} modules
                    </span>
                </div>
                <div class="card-body">
                    @if($employee->hasFullAccess())
                        <div class="alert alert-success mb-3 py-2">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-stars fs-5 me-2"></i>
                                <div class="compact-text">
                                    <strong>Full System Access</strong>
                                    <p class="mb-0">You have complete access to all system modules as a Super Administrator.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($accessibleModules->isEmpty())
                        <div class="alert alert-warning text-center py-3">
                            <i class="bi bi-exclamation-triangle fs-4 mb-2 d-block"></i>
                            <h6 class="mb-1">No Module Access</h6>
                            <p class="mb-0 compact-text-sm">No module access has been assigned to your account.</p>
                        </div>
                    @else
                        <p class="text-muted mb-3 compact-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Your current access permissions:
                        </p>
                        
                        <div class="module-grid-container">
                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                @foreach($accessibleModules as $moduleAccess)
                                    @if($moduleAccess->module !== 'all')
                                        @php
                                            $module = $moduleAccess->module;
                                            $accessLevel = $moduleAccess->access_level ?? 'view';
                                            
                                            $moduleIcons = [
                                                'Users' => 'bi-people',
                                                'Activities' => 'bi-calendar-event',
                                                'Programs' => 'bi-diagram-3',
                                                'Projects' => 'bi-kanban',
                                                'Action_plans' => 'bi-journal-text',
                                                'Surveys' => 'bi-clipboard-data',
                                                'COPs' => 'bi-building',
                                                'Portfolios' => 'bi-folder',
                                                'Reports' => 'bi-bar-chart',
                                                'Nationality' => 'bi-globe',
                                                'Diploma' => 'bi-award',
                                                'Roles' => 'bi-person-badge',
                                                'Employees' => 'bi-person-workspace',
                                                'Dashboard' => 'bi-speedometer2',
                                            ];
                                        @endphp
                                        <div class="col">
                                            <div class="module-item border rounded p-3 bg-white shadow-sm">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi {{ $moduleIcons[$module] ?? 'bi-grid' }} me-1 text-primary"></i>
                                                        <h6 class="mb-0 text-capitalize compact-text">{{ str_replace('_', ' ', $module) }}</h6>
                                                    </div>
                                                    <span class="access-level-badge access-{{ $accessLevel }}">
                                                        {{ $accessLevel }}
                                                    </span>
                                                </div>
                                                <p class="text-muted small mb-0 compact-text-sm">
                                                    Access: <span class="fw-bold text-capitalize">{{ $accessLevel }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    
</div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard loaded');
        });
    </script>
@endsection