<div class="d-flex flex-column h-100 py-3">
    {{-- Brand --}}
    <div class="navbar-brand text-center mb-4 px-3">
        <div class="d-flex align-items-center justify-content-center">
            <div class="bg-white rounded-circle p-2 me-3 shadow-sm">
                <i class="bi bi-people-fill text-primary fs-4"></i>
            </div>
            <div>
                <div class="text-uppercase fw-bold fs-5 text-white">Hariri</div>
                <div class="text-uppercase fw-bold fs-5 text-white">Foundation</div>
                <div class="text-white-50 small mt-1">Management System</div>
            </div>
        </div>
    </div>
    
    {{-- User Info --}}
    @auth('employee')
        @php
            $employee = auth()->guard('employee')->user();
            // Load role with module accesses
            if (!$employee->relationLoaded('role')) {
                $employee->load('role.moduleAccesses');
            }
            $hasFullAccess = $employee->hasFullAccess();
            
            // Debug: Show what modules the employee has access to
            $debugModules = $employee->role->moduleAccesses ?? collect();
            
            // Check specific module access with simpler logic
            $canAccessUsers = $employee->hasPermission('Users') || $hasFullAccess;
            $canAccessEmployees = $employee->hasPermission('Employees') || $hasFullAccess; // Employees use Users module
            $canAccessRoles = $employee->hasPermission('Employees') || $hasFullAccess;
            $canAccessPrograms = $employee->hasPermission('Programs') || $hasFullAccess;
            $canAccessProjects = $employee->hasPermission('Projects') || $hasFullAccess;
            $canAccessActivities = $employee->hasPermission('Activities') || $hasFullAccess;
            $canAccessReports = $employee->hasPermission('Reports') || $hasFullAccess;
            $canAccessDashboard = $employee->hasPermission('Dashboard') || $hasFullAccess;
            $canAccessActionPlans = $employee->hasPermission('Reports') || $hasFullAccess;
            $canAccessModuleAccess = $hasFullAccess; // Only full access for module access
        
        @endphp
        
       
        
        <div class="text-center mb-4 px-3">
            <div class="text-white small">
                <i class="bi bi-person-circle me-1"></i> 
                {{ $employee->first_name }} {{ $employee->last_name }}
            </div>
            <div class="text-white-50 x-small">
                {{ $employee->role->role_name ?? 'No role' }}
                @if($hasFullAccess)
                    <span class="badge bg-success ms-1">Super Admin</span>
                @endif
            </div>
        </div>
    @endauth
    
    {{-- Main Navigation --}}
    <div class="nav-header">Navigation</div>
    <ul class="nav flex-column mb-2">
        {{-- Dashboard --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessDashboard)
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            @endif
        @endauth
        
        {{-- Employees Directory --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessEmployees)
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" 
                   data-bs-toggle="collapse" href="#employeesDirectoryCollapse" role="button">
                    <span>
                        <i class="bi bi-person-badge me-2"></i> Employees Directory
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="collapse {{ request()->is('employees*') ? 'show' : '' }}" id="employeesDirectoryCollapse">
                    <ul class="nav flex-column sub-menu ms-4">
                        {{-- All Employees --}}
                        @if($hasFullAccess || $employee->hasPermission('Employees'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employees.index') ? 'active' : '' }}" 
                               href="{{ route('employees.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Employees
                            </a>
                        </li>
                        @endif
                        
                        {{-- Add New Employee --}}
                        @if($hasFullAccess || $employee->hasPermission('Employees'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employees.create') ? 'active' : '' }}" 
                               href="{{ route('employees.create') }}">
                                <i class="bi bi-person-plus me-2"></i> Add New Employee
                            </a>
                        </li>
                        @endif
                        
                       
                        
                       
                        
                     
                        


                        
                        
                       
                    </ul>
                </div>
            </li>
            @endif
        @endauth
        
        {{-- Roles Directory --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessRoles)
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" 
                   data-bs-toggle="collapse" href="#rolesDirectoryCollapse" role="button">
                    <span>
                        <i class="bi bi-shield-shaded me-2"></i> Roles Directory
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="collapse {{ request()->is('roles*') ? 'show' : '' }}" id="rolesDirectoryCollapse">
                    <ul class="nav flex-column sub-menu ms-4">
                        {{-- All Roles --}}
                        @if($hasFullAccess || $employee->hasPermission('Employees'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}" 
                               href="{{ route('roles.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Roles
                            </a>
                        </li>
                        @endif
                        
                        {{-- Create Role --}}
                        @if($hasFullAccess || $employee->hasPermission('Employees'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('roles.create') ? 'active' : '' }}" 
                               href="{{ route('roles.create') }}">
                                <i class="bi bi-plus-circle me-2"></i> Create Role
                            </a>
                        </li>
                        @endif
                        
                       
                    </ul>
                </div>
            </li>
            @endif
        @endauth
        
        {{-- User Directory --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessUsers)
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" 
                   data-bs-toggle="collapse" href="#userDirectoryCollapse" role="button">
                    <span>
                        <i class="bi bi-people me-2"></i> User Directory
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="collapse {{ request()->is('users*') ? 'show' : '' }}" id="userDirectoryCollapse">
                    <ul class="nav flex-column sub-menu ms-4">
                        {{-- User Management --}}
                        @if($hasFullAccess || $employee->hasPermission('Users'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" 
                               href="{{ route('users.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Users
                            </a>
                        </li>
                        @endif
                        
                        {{-- Add New User --}}
                        @if($hasFullAccess || $employee->hasPermission('Users'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}" 
                               href="{{ route('users.create') }}">
                                <i class="bi bi-person-plus me-2"></i> Add New User
                            </a>
                        </li>
                        @endif
                        
                        {{-- User Statistics --}}
                        @if($hasFullAccess || $employee->hasPermission('Users'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.statistics') ? 'active' : '' }}" 
                               href="{{ route('users.statistics') }}">
                                <i class="bi bi-bar-chart me-2"></i> User Statistics
                            </a>
                        </li>
                        @endif
                        
                        {{-- Import Users --}}
                        @if($hasFullAccess || $employee->hasPermission('Users'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.import.form') ? 'active' : '' }}" 
                               href="{{ route('users.import.form') }}">
                                <i class="bi bi-cloud-upload me-2"></i> Import Users
                            </a>
                        </li>
                        @endif
                        
                        {{-- Export Users --}}
                        @if($hasFullAccess || $employee->hasPermission('Users'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.export.excel') ? 'active' : '' }}" 
                               href="{{ route('users.export.excel') }}">
                                <i class="bi bi-download me-2"></i> Export Users
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
        @endauth

        

        {{-- Activity Directory --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessActivities)
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" 
                   data-bs-toggle="collapse" href="#activityDirectoryCollapse" role="button">
                    <span>
                        <i class="bi bi-calendar-event me-2"></i> Activity Directory
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="collapse {{ request()->is('activities*') ? 'show' : '' }}" id="activityDirectoryCollapse">
                    <ul class="nav flex-column sub-menu ms-4">
                        {{-- Activity Management --}}
                        @if($hasFullAccess || $employee->hasPermission('Activities'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('activities.index') ? 'active' : '' }}" 
                               href="{{ route('activities.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Activities
                            </a>
                        </li>
                        @endif
                        
                        {{-- Create Activity --}}
                        @if($hasFullAccess || $employee->hasPermission('Activities'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('activities.create') ? 'active' : '' }}" 
                               href="{{ route('activities.create') }}">
                                <i class="bi bi-plus-circle me-2"></i> Create Activity
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
        @endauth

        {{-- Action Plans --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessActionPlans || $canAccessReports)
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" 
                   data-bs-toggle="collapse" href="#actionPlansCollapse" role="button">
                    <span>
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i> Action Plans
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="collapse {{ request()->is('action-plans*') || request()->is('reporting*') ? 'show' : '' }}" id="actionPlansCollapse">
                    <ul class="nav flex-column sub-menu ms-4">
                        {{-- Excel Import --}}
                        @if($hasFullAccess || $employee->hasPermission('Reports'))
                        <div class="sub-header ms-2 mt-2">Excel Operations</div>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('reporting/import') ? 'active' : '' }}" 
                               href="{{ url('/reporting/import') }}">
                                <i class="bi bi-upload me-2"></i> Import Excel File
                            </a>
                        </li>
                        @endif
                        
                        {{-- Action Plan Management --}}
                        @if($hasFullAccess || $employee->hasPermission('Reports'))
                        <div class="sub-header ms-2 mt-2">Action Plans</div>
                        
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('action-plans.index') ? 'active' : '' }}" 
                               href="{{ route('action-plans.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Action Plans
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
        @endauth
                        
        {{-- Cross-Platform Analytics --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessReports)
            <div class="nav-header mt-4">Cross-Platform Analytics</div>
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-speedometer me-2"></i> Executive Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-pie-chart-fill me-2"></i> Overall Statistics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-graph-up-arrow me-2"></i> Performance Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-arrow-left-right me-2"></i> Data Integration
                    </a>
                </li>
            </ul>
            @endif
        @endauth

        {{-- Administration --}}
        @auth('employee')
            @if($hasFullAccess)
            <div class="nav-header mt-4">Administration</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-gear me-2"></i> System Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-shield-lock me-2"></i> Security & Permissions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-database me-2"></i> Data Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-upload me-2"></i> Bulk Operations
                    </a>
                </li>
            </ul>
            @endif
        @endauth
        
        {{-- Authentication Section --}}
        @auth('employee')
            {{-- Logout --}}
            <div class="mt-auto p-3">
                <div class="card bg-dark border-0">
                    <div class="card-body text-center p-3">
                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-light w-100">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Login --}}
            <div class="mt-auto p-3">
                <div class="card bg-dark border-0">
                    <div class="card-body text-center p-3">
                        <h6 class="text-white mb-2">Employee Access</h6>
                        <p class="text-white-50 small mb-3">Login to access protected features</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Employee Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
    </ul>
</div>

<style>
    .nav-header {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.6);
        padding: 0.5rem 1rem;
        margin-top: 0.5rem;
    }

    .sub-header {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.5);
        padding: 0.5rem 0;
        margin-top: 0.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sub-menu {
        border-left: 2px solid rgba(255, 255, 255, 0.1);
        padding-left: 0.5rem;
        margin: 0.25rem 0;
    }

    .sub-menu .nav-link {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .sub-menu .nav-link:hover,
    .sub-menu .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .collapse-icon {
        transition: transform 0.2s ease-in-out;
        font-size: 0.75rem;
    }

/* Better brand display */
.navbar-brand .text-uppercase {
    line-height: 1.1;
}

/* Badge styling */
.badge {
    font-size: 0.6rem;
    padding: 0.2rem 0.4rem;
}

/* Debug info */
.debug-info {
    font-size: 0.7rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}
</style>

<script>
    // Add collapse icon rotation
    document.addEventListener('DOMContentLoaded', function() {
        const collapseLinks = document.querySelectorAll('[data-bs-toggle="collapse"]');

        collapseLinks.forEach(link => {
            link.addEventListener('click', function() {
                const icon = this.querySelector('.collapse-icon');
                if (icon) {
                    icon.style.transform = icon.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            });

            // Set initial state for open collapses
            const targetId = link.getAttribute('href');
            const targetCollapse = document.querySelector(targetId);
            if (targetCollapse && targetCollapse.classList.contains('show')) {
                const icon = link.querySelector('.collapse-icon');
                if (icon) {
                    icon.style.transform = 'rotate(180deg)';
                }
            }
        });
    });
</script>