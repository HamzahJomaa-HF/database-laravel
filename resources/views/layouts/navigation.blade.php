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
    
    {{-- Main Navigation --}}
    <div class="nav-header">Navigation</div>
    <ul class="nav flex-column mb-2">
        <li class="nav-item">
            <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        
        {{-- User Directory --}}
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
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" 
                           href="{{ route('users.index') }}">
                            <i class="bi bi-list-ul me-2"></i> All Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.create') ? 'active' : '' }}" 
                           href="{{ route('users.create') }}">
                            <i class="bi bi-person-plus me-2"></i> Add New User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.import.form') ? 'active' : '' }}" 
                           href="{{ route('users.import.form') }}">
                            <i class="bi bi-cloud-upload me-2"></i> Import Users
                        </a>
                    </li>
                    
                    
                        <a class="nav-link {{ request()->routeIs('users.statistics') ? 'active' : '' }}" 
                           href="{{ route('users.statistics') }}">
                            <i class="bi bi-bar-chart me-2"></i> User Statistics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.reports') ? 'active' : '' }}" 
                           href="{{ route('users.reports') }}">
                            <i class="bi bi-file-earmark-text me-2"></i> User Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.export.excel') ? 'active' : '' }}" 
                           href="{{ route('users.export.excel') }}">
                            <i class="bi bi-download me-2"></i> Export Users
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- Program Directory --}}
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" 
               data-bs-toggle="collapse" href="#programDirectoryCollapse" role="button">
                <span>
                    <i class="bi bi-journal-text me-2"></i> Program Directory
                </span>
                <i class="bi bi-chevron-down collapse-icon"></i>
            </a>
            <div class="collapse" id="programDirectoryCollapse">
                <ul class="nav flex-column sub-menu ms-4">
                    {{-- Program Management --}}
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-list-ul me-2"></i> All Programs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-plus-circle me-2"></i> Create Program
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-cloud-upload me-2"></i> Import Programs
                        </a>
                    </li>
                    
                    {{-- Program Analytics --}}
                    <li class="nav-item sub-header">Analytics</li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-bar-chart me-2"></i> Program Statistics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-file-earmark-text me-2"></i> Program Reports
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- Project Directory --}}
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" 
               data-bs-toggle="collapse" href="#projectDirectoryCollapse" role="button">
                <span>
                    <i class="bi bi-folder me-2"></i> Project Directory
                </span>
                <i class="bi bi-chevron-down collapse-icon"></i>
            </a>
            <div class="collapse" id="projectDirectoryCollapse">
                <ul class="nav flex-column sub-menu ms-4">
                    {{-- Project Management --}}
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-list-ul me-2"></i> All Projects
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-plus-circle me-2"></i> New Project
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-cloud-upload me-2"></i> Import Projects
                        </a>
                    </li>
                    
                    {{-- Project Analytics --}}
                    <li class="nav-item sub-header">Analytics</li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-bar-chart me-2"></i> Project Statistics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-file-earmark-text me-2"></i> Project Reports
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- Activity Directory --}}
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" 
               data-bs-toggle="collapse" href="#activityDirectoryCollapse" role="button">
                <span>
                    <i class="bi bi-calendar-event me-2"></i> Activity Directory
                </span>
                <i class="bi bi-chevron-down collapse-icon"></i>
            </a>
            <div class="collapse" id="activityDirectoryCollapse">
                <ul class="nav flex-column sub-menu ms-4">
                    {{-- Activity Management --}}
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-list-ul me-2"></i> All Activities
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-plus-circle me-2"></i> Schedule Activity
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-cloud-upload me-2"></i> Import Activities
                        </a>
                    </li>
                    
                    {{-- Activity Analytics --}}
                    <li class="nav-item sub-header">Analytics</li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-bar-chart me-2"></i> Activity Statistics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-file-earmark-text me-2"></i> Activity Reports
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>

    {{-- Cross-Platform Analytics --}}
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

    {{-- Administration --}}
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
    
    {{-- Help Section --}}
    <div class="mt-auto p-3">
        <div class="card bg-dark border-0">
            <div class="card-body text-center p-3">
                <i class="bi bi-question-circle fs-2 text-warning mb-2"></i>
                <h6 class="text-white mb-2">Need Assistance?</h6>
                <p class="text-white-50 small mb-3">Our support team is ready to help</p>
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-warning btn-sm">
                        <i class="bi bi-headset me-1"></i> Contact Support
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-book me-1"></i> Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>
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