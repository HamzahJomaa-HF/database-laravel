<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hariri Foundation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-bg: #2c3e50;
            --sidebar-text: #ecf0f1;
            --primary-color: #3498db;
            --success-color: #2ecc71;
            --info-color: #17a2b8;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Sidebar-styled card headers */
        .card-header-sidebar {
            background: linear-gradient(135deg, var(--sidebar-bg) 0%, #34495e 100%);
            color: white;
            border: none;
            padding: 1rem 1.25rem;
            border-radius: 8px 8px 0 0;
        }

        .card-header-sidebar i {
            font-size: 1.2rem;
        }

        /* Dashboard cards */
        .dashboard-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
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

        /* Module grid with scroll */
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

        .module-grid-container::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.75rem;
        }

        .module-item {
            background: white;
            border-radius: 8px;
            padding: 0.875rem;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }

        .module-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Access level badges */
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
        .access-full { background-color: var(--success-color) !important; }

        /* Quick access cards */
        .quick-access-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s ease;
            height: 100%;
            text-decoration: none !important;
            color: inherit;
        }

        .quick-access-card:hover {
            border-color: var(--primary-color);
            background-color: #f8f9fa;
            transform: translateY(-3px);
        }

        .quick-access-card .card-body {
            padding: 1.25rem;
            text-align: center;
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

        /* User profile section */
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

        /* Navigation */
        .navbar {
            background: var(--sidebar-bg) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 0.75rem 0;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .module-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-access-card .card-body {
                padding: 1rem;
            }
            
            .user-avatar {
                width: 60px;
                height: 60px;
                font-size: 1.75rem;
            }
        }

        /* Equal height cards */
        .row.equal-height {
            display: flex;
            flex-wrap: wrap;
        }

        .row.equal-height > [class*='col-'] {
            display: flex;
            flex-direction: column;
        }

        /* Footer */
        footer {
            background: var(--sidebar-bg);
            color: white;
            margin-top: 2rem;
            padding: 1.25rem 0;
            font-size: 0.85rem;
        }

        /* Module counter badge */
        .module-count-badge {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        /* Compact text */
        .compact-text {
            font-size: 0.85rem;
            line-height: 1.4;
        }

        .compact-text-sm {
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="bi bi-building me-2"></i>Hariri Foundation
                <small class="d-block text-white-50 fs-6">Management System</small>
            </a>
            
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" 
                            type="button" 
                            id="userDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false">
                        <i class="bi bi-person-circle fs-4 me-2"></i>
                        <div class="text-start">
                            <div class="fw-bold">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                            <small class="d-block">
                                {{ $employee->role->role_name ?? 'No role assigned' }}
                                @if(in_array('all', $employee->getAccessibleModules()))
                                    <span class="badge bg-success ms-1">Super Admin</span>
                                @endif
                            </small>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><h6 class="dropdown-header">Employee Account</h6></li>
                        <li><span class="dropdown-item-text">{{ $employee->email }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="dropdown-item p-0">
                                @csrf
                                <button type="submit" class="btn btn-link text-decoration-none text-danger w-100 text-start p-2">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-3">
        <!-- Welcome Section -->
        <div class="row mb-3">
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

        <!-- Dashboard Content - Equal Height Row -->
        <div class="row equal-height g-3">
            <!-- Left Column - User Information -->
            <div class="col-lg-4">
                <div class="dashboard-card h-100">
                    <div class="card-header card-header-sidebar">
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
                                        @if($employee->is_active)
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Column - Module Access -->
            <div class="col-lg-8">
                <div class="dashboard-card h-100">
                    <div class="card-header card-header-sidebar d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-lock me-2"></i>
                            <h5 class="mb-0 compact-text">Module Access Permissions</h5>
                        </div>
                        @php
                            $accessibleModules = $employee->getAccessibleModules();
                            $moduleCount = count(array_filter($accessibleModules, fn($module) => $module !== 'all'));
                        @endphp
                        <span class="module-count-badge compact-text-sm">
                            <i class="bi bi-grid-1x2 me-1"></i>{{ $moduleCount }} modules
                        </span>
                    </div>
                    <div class="card-body">
                        @if(in_array('all', $accessibleModules))
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

                        @if(empty($accessibleModules))
                            <div class="alert alert-warning text-center py-3">
                                <i class="bi bi-exclamation-triangle fs-4 mb-2 d-block"></i>
                                <h6 class="mb-1">No Module Access</h6>
                                <p class="mb-0 compact-text-sm">No module access has been assigned to your account.</p>
                            </div>
                        @else
                            <p class="text-muted mb-2 compact-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Your current access permissions:
                            </p>
                            
                            <div class="module-grid-container">
                                <div class="module-grid">
                                    @foreach($accessibleModules as $module)
                                        @if($module !== 'all')
                                            @php
                                                $access = $employee->moduleAccess()
                                                    ->where('module', $module)
                                                    ->whereNull('resource_id')
                                                    ->first();
                                                $accessLevel = $access ? $access->access_level : 'view';
                                                
                                                // Map modules to icons
                                                $moduleIcons = [
                                                    'users' => 'bi-people',
                                                    'activities' => 'bi-calendar-event',
                                                    'programs' => 'bi-diagram-3',
                                                    'projects' => 'bi-kanban',
                                                    'action_plans' => 'bi-journal-text',
                                                    'surveys' => 'bi-clipboard-data',
                                                    'cops' => 'bi-building',
                                                    'portfolios' => 'bi-folder',
                                                    'reports' => 'bi-bar-chart',
                                                    'nationality' => 'bi-globe',
                                                    'diploma' => 'bi-award',
                                                    'roles' => 'bi-person-badge',
                                                    'employees' => 'bi-person-workspace',
                                                ];
                                            @endphp
                                            <div class="module-item">
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
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Section -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="dashboard-card">
                    <div class="card-header card-header-sidebar">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-lightning-charge me-2"></i>
                            <h5 class="mb-0 compact-text">Quick Access</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3 compact-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Direct access to your authorized modules:
                        </p>
                        
                        <div class="row g-3">
                            <!-- Activities -->
                            @if($employee->hasModuleAccess('activities', 'view'))
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <a href="{{ route('protected.activities.index') }}" 
                                   class="quick-access-card card">
                                    <div class="card-body">
                                        <div class="quick-access-icon">
                                            <i class="bi bi-calendar-event"></i>
                                        </div>
                                        <h6 class="card-title mb-2 compact-text">Activities</h6>
                                        @if($employee->hasModuleAccess('activities', 'create'))
                                            <span class="badge bg-success module-badge compact-text-sm">
                                                <i class="bi bi-plus-circle me-1"></i>Create
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            </div>
                            @endif
                            
                            <!-- Users -->
                            @if($employee->hasModuleAccess('users', 'view'))
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <a href="{{ route('protected.users.index') }}" 
                                   class="quick-access-card card">
                                    <div class="card-body">
                                        <div class="quick-access-icon">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <h6 class="card-title mb-2 compact-text">Users</h6>
                                        @if($employee->hasModuleAccess('users', 'create'))
                                            <span class="badge bg-success module-badge compact-text-sm">
                                                <i class="bi bi-plus-circle me-1"></i>Create
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            </div>
                            @endif
                            
                            <!-- Action Plans -->
                            @if($employee->hasModuleAccess('action_plans', 'view'))
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <a href="{{ route('action-plans.index') }}" 
                                   class="quick-access-card card">
                                    <div class="card-body">
                                        <div class="quick-access-icon">
                                            <i class="bi bi-journal-text"></i>
                                        </div>
                                        <h6 class="card-title mb-2 compact-text">Action Plans</h6>
                                    </div>
                                </a>
                            </div>
                            @endif
                            
                            <!-- Programs -->
                            @if($employee->hasModuleAccess('programs', 'view'))
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <a href="#" 
                                   class="quick-access-card card">
                                    <div class="card-body">
                                        <div class="quick-access-icon">
                                            <i class="bi bi-diagram-3"></i>
                                        </div>
                                        <h6 class="card-title mb-2 compact-text">Programs</h6>
                                    </div>
                                </a>
                            </div>
                            @endif
                            
                            <!-- Projects -->
                            @if($employee->hasModuleAccess('projects', 'view'))
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <a href="#" 
                                   class="quick-access-card card">
                                    <div class="card-body">
                                        <div class="quick-access-icon">
                                            <i class="bi bi-kanban"></i>
                                        </div>
                                        <h6 class="card-title mb-2 compact-text">Projects</h6>
                                    </div>
                                </a>
                            </div>
                            @endif
                            
                            <!-- Surveys -->
                            @if($employee->hasModuleAccess('surveys', 'view'))
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <a href="#" 
                                   class="quick-access-card card">
                                    <div class="card-body">
                                        <div class="quick-access-icon">
                                            <i class="bi bi-clipboard-data"></i>
                                        </div>
                                        <h6 class="card-title mb-2 compact-text">Surveys</h6>
                                    </div>
                                </a>
                            </div>
                            @endif
                            
                            <!-- Reporting -->
                            @if($employee->hasModuleAccess('reports', 'view') || $employee->hasModuleAccess('action_plans', 'view'))
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <a href="{{ url('/reporting/import') }}" 
                                   class="quick-access-card card">
                                    <div class="card-body">
                                        <div class="quick-access-icon">
                                            <i class="bi bi-bar-chart"></i>
                                        </div>
                                        <h6 class="card-title mb-2 compact-text">Reporting</h6>
                                    </div>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 compact-text">
                        <i class="bi bi-c-circle me-1"></i> {{ date('Y') }} Hariri Foundation. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 compact-text">
                        <i class="bi bi-clock me-1"></i>
                        Server Time: {{ now()->format('h:i A') }}
                        <span class="ms-3 d-inline-block">
                            <i class="bi bi-person-check me-1"></i>
                            {{ $employee->first_name }} {{ $employee->last_name }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>