{{-- resources/views/partials/sidebar.blade.php --}}
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
            $accessibleModules = $employee->getAccessibleModules();
            $hasFullAccess = in_array('all', $accessibleModules);
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
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
        @endauth
        
        {{-- User Directory --}}
        @if(!auth()->guard('employee')->check() || 
            $hasFullAccess || 
            (auth()->guard('employee')->check() && $employee->hasModuleAccess('users', 'view')))
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" 
               data-bs-toggle="collapse" href="#userDirectoryCollapse" role="button">
                <span>
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i> Action Plans
                </span>
                <i class="bi bi-chevron-down collapse-icon"></i>
            </a>
            <div class="collapse {{ request()->is('action-plans*') || request()->is('reporting*') ? 'show' : '' }}" id="actionPlansCollapse">
                <ul class="nav flex-column sub-menu ms-4">
                    {{-- Excel Import --}}
                    <div class="sub-header ms-2 mt-2">Excel Operations</div>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reporting/import') ? 'active' : '' }}"
                            href="{{ url('/reporting/import') }}">
                            <i class="bi bi-upload me-2"></i> Import Excel File
                        </a>
                    </li>

                    {{-- Action Plan Management --}}
                    <div class="sub-header ms-2 mt-2">Action Plans</div>

                    {{-- ADD THIS: Link to All Action Plans --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('action-plans.index') ? 'active' : '' }}"
                            href="{{ route('action-plans.index') }}">
                            <i class="bi bi-list-ul me-2"></i> All Action Plans
                        </a>
                    </li>

                    {{-- Placeholder for other routes --}}
                    <li class="nav-item">
                        <a class="nav-link text-white-50 disabled" href="#">
                            <i class="bi bi-plus-circle me-2"></i> Create Action Plan
                        </a>
                    </li>
                    
                    {{-- Show "Add New User" only if logged in AND has create access --}}
                    @if(auth()->guard('employee')->check())
                        @if($hasFullAccess || $employee->hasModuleAccess('users', 'create'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}" 
                               href="{{ route('users.create') }}">
                                <i class="bi bi-person-plus me-2"></i> Add New User
                            </a>
                        </li>
                        @endif
                    @endif
                    
                    {{-- User Statistics --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.statistics') ? 'active' : '' }}" 
                           href="{{ route('users.statistics') }}">
                            <i class="bi bi-bar-chart me-2"></i> User Statistics
                        </a>
                    </li>
                    
                    {{-- Show "Import Users" only if logged in AND has manage access --}}
                    @if(auth()->guard('employee')->check())
                        @if($hasFullAccess || $employee->hasModuleAccess('users', 'manage'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.import.form') ? 'active' : '' }}" 
                               href="{{ route('users.import.form') }}">
                                <i class="bi bi-cloud-upload me-2"></i> Import Users
                            </a>
                        </li>
                        @endif
                    @endif
                    
                    {{-- Export Users --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.export.excel') ? 'active' : '' }}" 
                           href="{{ route('users.export.excel') }}">
                            <i class="bi bi-download me-2"></i> Export Users
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        {{-- Program Directory --}}
        @if(!auth()->guard('employee')->check() || 
            $hasFullAccess || 
            (auth()->guard('employee')->check() && $employee->hasModuleAccess('programs', 'view')))
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" 
               data-bs-toggle="collapse" href="#programDirectoryCollapse" role="button">
                <span>
                    <i class="bi bi-journal-text me-2"></i> Program Directory
                </span>
                <i class="bi bi-chevron-down collapse-icon"></i>
            </a>
            <div class="collapse {{ request()->is('programs*') ? 'show' : '' }}" id="programDirectoryCollapse">
                <ul class="nav flex-column sub-menu ms-4">
                    {{-- Program Management --}}
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-list-ul me-2"></i> All Programs
                        </a>
                    </li>
                    
                    {{-- Show "Add New Program" only if logged in AND has create access --}}
                    @if(auth()->guard('employee')->check())
                        @if($hasFullAccess || $employee->hasModuleAccess('programs', 'create'))
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-plus-circle me-2"></i> Add New Program
                            </a>
                        </li>
                        @endif
                    @endif
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-bar-chart me-2"></i> Program Statistics
                        </a>
                    </li>
                    
                    {{-- Show "Import Programs" only if logged in AND has manage access --}}
                    @if(auth()->guard('employee')->check())
                        @if($hasFullAccess || $employee->hasModuleAccess('programs', 'manage'))
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-cloud-upload me-2"></i> Import Programs
                            </a>
                        </li>
                        @endif
                    @endif
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-download me-2"></i> Export Programs
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        {{-- Project Directory --}}
        @if(!auth()->guard('employee')->check() || 
            $hasFullAccess || 
            (auth()->guard('employee')->check() && $employee->hasModuleAccess('projects', 'view')))
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" 
               data-bs-toggle="collapse" href="#projectDirectoryCollapse" role="button">
                <span>
                    <i class="bi bi-folder me-2"></i> Project Directory
                </span>
                <i class="bi bi-chevron-down collapse-icon"></i>
            </a>
            <div class="collapse {{ request()->is('projects*') ? 'show' : '' }}" id="projectDirectoryCollapse">
                <ul class="nav flex-column sub-menu ms-4">
                    {{-- Project Management --}}
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-list-ul me-2"></i> All Projects
                        </a>
                    </li>
                    
                    {{-- Show "Add New Project" only if logged in AND has create access --}}
                    @if(auth()->guard('employee')->check())
                        @if($hasFullAccess || $employee->hasModuleAccess('projects', 'create'))
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-plus-circle me-2"></i> Add New Project
                            </a>
                        </li>
                        @endif
                    @endif
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-bar-chart me-2"></i> Project Statistics
                        </a>
                    </li>
                    
                    {{-- Show "Import Projects" only if logged in AND has manage access --}}
                    @if(auth()->guard('employee')->check())
                        @if($hasFullAccess || $employee->hasModuleAccess('projects', 'manage'))
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-cloud-upload me-2"></i> Import Projects
                            </a>
                        </li>
                        @endif
                    @endif
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-download me-2"></i> Export Projects
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        {{-- Activity Directory --}}
        @if(!auth()->guard('employee')->check() || 
            $hasFullAccess || 
            (auth()->guard('employee')->check() && $employee->hasModuleAccess('activities', 'view')))
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
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('activities.index') ? 'active' : '' }}" 
                           href="{{ route('activities.index') }}">
                            <i class="bi bi-list-ul me-2"></i> All Activities
                        </a>
                    </li>
                    
                    {{-- Show "Create Activity" only if logged in AND has create access --}}
                    @if(auth()->guard('employee')->check())
                        @if($hasFullAccess || $employee->hasModuleAccess('activities', 'create'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('activities.create') }}">
                                <i class="bi bi-plus-circle me-2"></i> Create Activity
                            </a>
                        </li>
                        @endif
                    @endif
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-bar-chart me-2"></i> Activity Statistics
                        </a>
                    </li>
                    
                    {{-- Show "Import Activities" only if logged in AND has manage access --}}
                    @if(auth()->guard('employee')->check())
                        @if($hasFullAccess || $employee->hasModuleAccess('activities', 'manage'))
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-cloud-upload me-2"></i> Import Activities
                            </a>
                        </li>
                        @endif
                    @endif
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-download me-2"></i> Export Activities
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        @endif

        {{-- Action Plans --}}
        @if(!auth()->guard('employee')->check() || 
            $hasFullAccess || 
            (auth()->guard('employee')->check() && ($employee->hasModuleAccess('action_plans', 'view') || $employee->hasModuleAccess('reports', 'view'))))
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
                    <div class="sub-header ms-2 mt-2">Excel Operations</div>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reporting/import') ? 'active' : '' }}" 
                           href="{{ url('/reporting/import') }}">
                            <i class="bi bi-upload me-2"></i> Import Excel File
                        </a>
                    </li>
                    
                    {{-- Action Plan Management --}}
                    <div class="sub-header ms-2 mt-2">Action Plans</div>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('action-plans.index') ? 'active' : '' }}" 
                           href="{{ route('action-plans.index') }}">
                            <i class="bi bi-list-ul me-2"></i> All Action Plans
                        </a>
                    </li>
                    
                    {{-- Show "Create Action Plan" only if logged in AND has create access --}}
                    @if(auth()->guard('employee')->check())
                        @if($hasFullAccess || $employee->hasModuleAccess('action_plans', 'create'))
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-plus-circle me-2"></i> Create Action Plan
                            </a>
                        </li>
                        @endif
                    @endif
                    
                    {{-- Show "Download Template" only if logged in AND has view access --}}
                    @if(auth()->guard('employee')->check())
                        @if($hasFullAccess || $employee->hasModuleAccess('action_plans', 'view'))
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-download me-2"></i> Download Template
                            </a>
                        </li>
                        @endif
                    @endif
                    
                    {{-- Show "Export to Excel" only if logged in AND has manage access --}}
                    @if(auth()->guard('employee')->check())
                        @if($hasFullAccess || $employee->hasModuleAccess('action_plans', 'manage'))
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-file-earmark-excel me-2"></i> Export to Excel
                            </a>
                        </li>
                        @endif
                    @endif
                </ul>
            </div>
        </li>
        @endif
                        
        {{-- Cross-Platform Analytics --}}
        @if(!auth()->guard('employee')->check() || 
            $hasFullAccess || 
            (auth()->guard('employee')->check() && $employee->hasModuleAccess('reports', 'view')))
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

        {{-- Administration --}}
        @if(auth()->guard('employee')->check() && 
            ($hasFullAccess || $employee->hasModuleAccess('all', 'manage')))
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
        
        {{-- Authentication Section --}}
        @if(auth()->guard('employee')->check())
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
        @endif
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