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
            $employee->load('role.moduleAccesses');
            $hasFullAccess = $employee->hasFullAccess();

            // Returns true only if at least one concrete access level is granted for the module.
            // Using explicit level checks avoids the early-return in hasPermission(module, null)
            // which can fire even when no boxes are checked.
            $hasAny = function(string $module) use ($employee, $hasFullAccess): bool {
                if ($hasFullAccess) return true;
                foreach (['view', 'create', 'edit', 'delete', 'manage', 'full'] as $lvl) {
                    if ($employee->hasPermission($module, $lvl)) return true;
                }
                return false;
            };

            $canAccessUsers        = $hasAny('Users');
            $canAccessEmployees    = $hasAny('Employees');
            $canAccessRoles        = $hasAny('Roles');
            $canAccessPrograms     = $hasAny('Programs');
            $canAccessProjects     = $hasAny('Projects');
            $canAccessActivities   = $hasAny('Activities');
            $canAccessReports      = $hasAny('Reports');
            $canAccessDashboard    = $hasFullAccess || $canAccessUsers || $canAccessActivities
                                     || $hasAny('Programs') || $hasAny('Financials')
                                     || $hasAny('Employees') || $hasAny('ActivityUsers');
            $canAccessActionPlans  = $hasAny('Reports');
            $canAccessPortfolios   = $hasAny('Portfolios');
            $canAccessCOPs         = $hasAny('COPs');
            $canAccessModuleAccess = $hasFullAccess;
            $canAccessActivityUsers = $hasAny('ActivityUsers');
            $canAccessFinancials   = $hasAny('Financials');

            // Sub-level shortcut (keeps inline @if conditions readable)
            $can = function(string $module, string $level) use ($employee, $hasFullAccess): bool {
                if ($hasFullAccess) return true;
                return $employee->hasPermission($module, $level);
            };
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
                        @if($can('Employees', 'view') || $can('Employees', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('employees.index') ? 'active' : '' }}" 
                               href="{{ route('employees.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Employees
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Employees', 'create') || $can('Employees', 'full'))
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
                        @if($can('Roles', 'view') || $can('Roles', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}" 
                               href="{{ route('roles.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Roles
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Roles', 'create') || $can('Roles', 'full'))
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
                        @if($can('Users', 'view') || $can('Users', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" 
                               href="{{ route('users.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Users
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Users', 'create') || $can('Users', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}" 
                               href="{{ route('users.create') }}">
                                <i class="bi bi-person-plus me-2"></i> Add New User
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Users', 'view') || $can('Users', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.statistics') ? 'active' : '' }}" 
                               href="{{ route('users.statistics') }}">
                                <i class="bi bi-bar-chart me-2"></i> User Statistics
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Users', 'create') || $can('Users', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.import.form') ? 'active' : '' }}" 
                               href="{{ route('users.import.form') }}">
                                <i class="bi bi-cloud-upload me-2"></i> Import Users
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Users', 'view') || $can('Users', 'full'))
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

        {{-- PROGRAMS DIRECTORY --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessPrograms)
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" 
                   data-bs-toggle="collapse" href="#programsDirectoryCollapse" role="button">
                    <span>
                        <i class="bi bi-diagram-3 me-2"></i> Programs
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="collapse {{ request()->is('programs*') ? 'show' : '' }}" id="programsDirectoryCollapse">
                    <ul class="nav flex-column sub-menu ms-4">
                        @if($can('Programs', 'view') || $can('Programs', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('programs.index') ? 'active' : '' }}" 
                               href="{{ route('programs.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Programs
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Programs', 'create') || $can('Programs', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('createCenter') ? 'active' : '' }}" 
                               href="{{ route('createCenter') }}">
                                <i class="bi bi-building me-2"></i> Create Center
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Programs', 'create') || $can('Programs', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('create.flagshiplocal') ? 'active' : '' }}" 
                               href="{{ route('create.flagshiplocal') }}">
                                <i class="bi bi-flag me-2"></i> Create Flagship/Local
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Programs', 'create') || $can('Programs', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('create.subprogram') ? 'active' : '' }}" 
                               href="{{ route('create.subprogram') }}">
                                <i class="bi bi-project-diagram me-2"></i> Create Sub-Program
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
        @endauth

        {{-- PROJECTS DIRECTORY --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessProjects)
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" 
                   data-bs-toggle="collapse" href="#projectsDirectoryCollapse" role="button">
                    <span>
                        <i class="bi bi-kanban me-2"></i> Projects
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="collapse {{ request()->is('projects*') ? 'show' : '' }}" id="projectsDirectoryCollapse">
                    <ul class="nav flex-column sub-menu ms-4">
                        @if($can('Projects', 'view') || $can('Projects', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('projects.index') ? 'active' : '' }}" 
                               href="{{ route('projects.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Projects
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Projects', 'create') || $can('Projects', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('projects.create') ? 'active' : '' }}" 
                               href="{{ route('projects.create') }}">
                                <i class="bi bi-plus-circle me-2"></i> Create Project
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
        @endauth

        {{-- Portfolios Directory --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessPortfolios)
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" 
                   data-bs-toggle="collapse" href="#portfoliosDirectoryCollapse" role="button">
                    <span>
                        <i class="bi bi-briefcase me-2"></i> Portfolios
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="collapse {{ request()->is('portfolios*') ? 'show' : '' }}" id="portfoliosDirectoryCollapse">
                    <ul class="nav flex-column sub-menu ms-4">
                        @if($can('Portfolios', 'view') || $can('Portfolios', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('portfolios.index') ? 'active' : '' }}" 
                               href="{{ route('portfolios.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Portfolios
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Portfolios', 'create') || $can('Portfolios', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('portfolios.create') ? 'active' : '' }}" 
                               href="{{ route('portfolios.create') }}">
                                <i class="bi bi-plus-circle me-2"></i> Create Portfolio
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
        @endauth

        {{-- COPs Directory --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessCOPs)
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" 
                   data-bs-toggle="collapse" href="#copsDirectoryCollapse" role="button">
                    <span>
                        <i class="bi bi-people-fill me-2"></i> Community of Practice
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="collapse {{ request()->is('cops*') ? 'show' : '' }}" id="copsDirectoryCollapse">
                    <ul class="nav flex-column sub-menu ms-4">
                        @if($can('COPs', 'view') || $can('COPs', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('cops.index') ? 'active' : '' }}" 
                               href="{{ route('cops.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All COPs
                            </a>
                        </li>
                        @endif
                        
                        @if($can('COPs', 'create') || $can('COPs', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('cops.create') ? 'active' : '' }}" 
                               href="{{ route('cops.create') }}">
                                <i class="bi bi-plus-circle me-2"></i> Create COP
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
                        <i class="bi bi-calendar-event me-2"></i> Activities
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="collapse {{ request()->is('activities*') ? 'show' : '' }}" id="activityDirectoryCollapse">
                    <ul class="nav flex-column sub-menu ms-4">
                        @if($can('Activities', 'view') || $can('Activities', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('activities.index') ? 'active' : '' }}" 
                               href="{{ route('activities.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Activities
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Activities', 'create') || $can('Activities', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('activities.create') ? 'active' : '' }}" 
                               href="{{ route('activities.create') }}">
                                <i class="bi bi-plus-circle me-2"></i> Create Activity
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Activities', 'create') || $can('Activities', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('activities.import') ? 'active' : '' }}" 
                               href="{{ route('activities.import') }}">
                                <i class="bi bi-cloud-upload me-2"></i> Import Activities
                            </a>
                        </li>
                        @endif
                        
                        @if($can('Activities', 'view') || $can('Activities', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('activities.export') ? 'active' : '' }}" 
                               href="{{ route('activities.export') }}">
                                <i class="bi bi-download me-2"></i> Export
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
            @endif
        @endauth

        {{-- Activity User Directory --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessActivityUsers)
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" 
                   data-bs-toggle="collapse" href="#activityUserDirectoryCollapse" role="button">
                    <span>
                        <i class="bi bi-person-check me-2"></i> Activity User
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="collapse {{ request()->is('activity-users*') ? 'show' : '' }}" id="activityUserDirectoryCollapse">
                    <ul class="nav flex-column sub-menu ms-4">
                        @if($can('ActivityUsers', 'view') || $can('ActivityUsers', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('activity-users.index') ? 'active' : '' }}" 
                               href="{{ route('activity-users.index') }}">
                                <i class="bi bi-list-ul me-2"></i> All Activity Users
                            </a>
                        </li>
                        @endif
                        
                        @if($can('ActivityUsers', 'create') || $can('ActivityUsers', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('activity-users.create') ? 'active' : '' }}" 
                               href="{{ route('activity-users.create') }}">
                                <i class="bi bi-plus-circle me-2"></i> Create Activity User
                            </a>
                        </li>
                        @endif
                        
                        @if($can('ActivityUsers', 'create') || $can('ActivityUsers', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('activity-users.import.form') ? 'active' : '' }}" 
                               href="{{ route('activity-users.import.form') }}">
                                <i class="bi bi-cloud-upload me-2"></i> Import Activity Users
                            </a>
                        </li>
                        @endif
                        
                        @if($can('ActivityUsers', 'view') || $can('ActivityUsers', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('activity-users.export') ? 'active' : '' }}" 
                               href="{{ route('activity-users.export') }}">
                                <i class="bi bi-download me-2"></i> Export
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
                        @if($can('Reports', 'create') || $can('Reports', 'full'))
                        <div class="sub-header ms-2 mt-2">Excel Operations</div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('reporting/import') ? 'active' : '' }}"
                               href="{{ url('/reporting/import') }}">
                                <i class="bi bi-upload me-2"></i> Import Excel File
                            </a>
                        </li>
                        @endif

                        @if($can('Reports', 'view') || $can('Reports', 'full'))
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

        {{-- ============================================= --}}
        {{-- FINANCIALS SECTION - NEW WITH SEPARATE TABS --}}
        {{-- ============================================= --}}
        @auth('employee')
            @if($hasFullAccess || $canAccessFinancials)
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center"
                   data-bs-toggle="collapse" href="#financialsCollapse" role="button">
                    <span>
                        <i class="bi bi-coin me-2"></i> Financials
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>
                <div class="collapse {{ request()->is('financials*') ? 'show' : '' }}" id="financialsCollapse">
                    <ul class="nav flex-column sub-menu ms-4">
                        {{-- OMT Financials --}}
                        @if($can('Financials', 'view') || $can('Financials', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->get('financial_type') == 'omt' ? 'active' : '' }}"
                               href="{{ route('financials.index', ['financial_type' => 'omt']) }}">
                                <i class="bi bi-graph-up me-2"></i> OMT Financials
                            </a>
                        </li>
                        @endif

                        {{-- Medical Financials with Sub-menu for Medicine & Hospital --}}
@if($can('Financials', 'view') || $can('Financials', 'full'))
<li class="nav-item">
    <a class="nav-link d-flex justify-content-between align-items-center"
       data-bs-toggle="collapse" href="#medicalSubmenuCollapse" role="button">
        <span>
            <i class="bi bi-heart-pulse me-2"></i> Medical Financials
        </span>
        <i class="bi bi-chevron-down collapse-icon"></i>
    </a>
    <div class="collapse {{ request()->is('financials/medical*') ? 'show' : '' }}" id="medicalSubmenuCollapse">
        <ul class="nav flex-column sub-menu ms-4">
            {{-- Medicine Records --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('financials/medical/medicine*') ? 'active' : '' }}"
                   href="{{ route('financials.medical.medicine') }}">
                    <i class="bi bi-capsule me-2"></i> Medicine Records
                </a>
            </li>

            {{-- Hospital Records --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->is('financials/medical/hospital*') ? 'active' : '' }}"
                   href="{{ route('financials.medical.hospital') }}">
                    <i class="bi bi-hospital me-2"></i> Hospital Records
                </a>
            </li>
        </ul>
    </div>
</li>
@endif

                        {{-- Import Financials --}}
                        @if($can('Financials', 'create') || $can('Financials', 'full'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('financials.import.form') ? 'active' : '' }}"
                               href="{{ route('financials.import.form') }}">
                                <i class="bi bi-cloud-upload me-2"></i> Import Financials
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            </li>
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
    .nav-item > .nav-link {
        white-space: nowrap;
        overflow: hidden;
    }
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