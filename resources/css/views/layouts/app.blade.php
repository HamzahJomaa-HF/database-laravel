<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | FlowDash User System</title>
    
    {{-- Bootstrap CSS 5.3.2 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    {{-- Bootstrap Icons CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" integrity="sha384-405073e1c6e18f2f5c22f0c2a20a9a3b2b4d9a6f7b1d9c0e5a9c0c8b2f9f1f0a" crossorigin="anonymous">

    {{-- Font Awesome for additional icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Custom Styles for Sidebar and Layout --}}
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #273449; /* Dark Blue from the example */
            --sidebar-hover: #354761;
            --primary-color: #2563eb;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #dc2626;
        }
        body {
            background-color: #f4f6f9; /* Light gray background */
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }
        /* Fixed Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: #fff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            padding-top: 56px; /* Space for the top Navbar */
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        /* Main Content Area */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            padding-top: 56px; /* Space for the top Navbar */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        /* Sidebar Nav Links */
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 20px;
            font-size: 0.95rem;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background-color: var(--sidebar-hover);
            color: #fff;
            border-left-color: var(--primary-color);
        }
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            font-size: 1.1rem;
        }
        .sidebar .nav-header {
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 20px 20px 8px;
            margin-top: 10px;
        }
        .navbar {
            z-index: 1060; /* Higher than sidebar */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        /* Profile Dropdown Styling */
        .profile-dropdown {
            min-width: 280px;
            padding: 0;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
            color: white;
            padding: 20px;
            text-align: center;
        }
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            margin: 0 auto 15px;
            border: 4px solid rgba(255,255,255,0.2);
        }
        .profile-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .profile-role {
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-block;
            margin-bottom: 10px;
        }
        .profile-email {
            font-size: 0.85rem;
            opacity: 0.9;
        }
        .profile-stats {
            display: flex;
            justify-content: space-around;
            padding: 15px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        .stat-label {
            font-size: 0.75rem;
            color: #6c757d;
            text-transform: uppercase;
        }
        .profile-menu-item {
            padding: 12px 20px;
            border-bottom: 1px solid #e9ecef;
            color: #495057;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .profile-menu-item:hover {
            background-color: #f8f9fa;
            color: var(--primary-color);
            text-decoration: none;
        }
        .profile-menu-item i {
            width: 20px;
            color: var(--primary-color);
        }
        .last-login {
            font-size: 0.75rem;
            color: #6c757d;
            padding: 10px 20px;
            background: #f8f9fa;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            gap: 5px;
        }
        .status-active {
            background-color: rgba(16, 185, 129, 0.1);
            color: #065f46;
        }
        .status-inactive {
            background-color: rgba(239, 68, 68, 0.1);
            color: #991b1b;
        }
        
        /* Navbar customization */
        .search-input {
            border-radius: 20px;
            border: 1px solid #dee2e6;
            padding-left: 40px;
        }
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .sidebar-open .sidebar {
                transform: translateX(0);
            }
            .sidebar-open .main-wrapper {
                margin-left: var(--sidebar-width);
            }
            .navbar-brand {
                width: auto !important;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <?php
    // Get the logged-in employee from session or auth
    use Illuminate\Support\Facades\Auth;
    $loggedInEmployee = Auth::user()->employee ?? null;
    $credentials = $loggedInEmployee->credentials ?? null;
    ?>
    
    {{-- 1. Fixed Top Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container-fluid">
            {{-- Mobile menu toggle --}}
            <button class="navbar-toggler d-lg-none me-2" type="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            {{-- Brand --}}
            <a class="navbar-brand d-none d-lg-flex align-items-center" href="{{ url('/') }}" style="width: var(--sidebar-width); padding-left: 1rem;">
                <i class="bi bi-water me-2 text-primary fs-4"></i>
                <span class="fw-bold">FlowDash</span>
            </a>
            
            {{-- Search Bar --}}
            <form class="d-flex me-auto ms-lg-3 position-relative">
                <i class="bi bi-search search-icon"></i>
                <input class="form-control search-input" type="search" placeholder="Search anything..." aria-label="Search" style="width: 300px;">
            </form>

            {{-- User/Profile --}}
            <ul class="navbar-nav ms-auto align-items-center">
                {{-- Notifications --}}
                <li class="nav-item me-3 position-relative">
                    <a class="nav-link" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            3
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 300px;">
                        <div class="dropdown-header bg-light border-bottom py-3">
                            <h6 class="mb-0">Notifications</h6>
                        </div>
                        <div class="dropdown-body p-0">
                            <a href="#" class="dropdown-item py-3 border-bottom">
                                <div class="d-flex">
                                    <i class="bi bi-person-plus text-success me-3"></i>
                                    <div>
                                        <div class="fw-medium">New user registered</div>
                                        <div class="text-muted small">5 minutes ago</div>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="dropdown-item py-3 border-bottom">
                                <div class="d-flex">
                                    <i class="bi bi-shield-check text-primary me-3"></i>
                                    <div>
                                        <div class="fw-medium">Security alert</div>
                                        <div class="text-muted small">2 hours ago</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="dropdown-footer text-center py-2">
                            <a href="#" class="text-decoration-none small">View all notifications</a>
                        </div>
                    </div>
                </li>
                
                {{-- Settings --}}
                <li class="nav-item me-3">
                    <a class="nav-link" href="{{ route('settings.index') }}">
                        <i class="bi bi-gear fs-5"></i>
                    </a>
                </li>
                
                {{-- Profile Dropdown --}}
                @if($loggedInEmployee)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        @if($loggedInEmployee->avatar)
                            <img src="{{ asset('storage/' . $loggedInEmployee->avatar) }}" class="rounded-circle me-2" alt="{{ $loggedInEmployee->first_name }}" width="36" height="36">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px; font-weight: 600;">
                                {{ substr($loggedInEmployee->first_name, 0, 1) }}{{ substr($loggedInEmployee->last_name, 0, 1) }}
                            </div>
                        @endif
                        <span class="d-none d-md-inline">
                            {{ $loggedInEmployee->first_name }} {{ $loggedInEmployee->last_name }}
                            <i class="bi bi-chevron-down ms-1 small"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end profile-dropdown" aria-labelledby="userDropdown">
                        {{-- Profile Header --}}
                        <li class="profile-header">
                            <div class="profile-avatar">
                                @if($loggedInEmployee->avatar)
                                    <img src="{{ asset('storage/' . $loggedInEmployee->avatar) }}" alt="{{ $loggedInEmployee->first_name }}" class="w-100 h-100 rounded-circle">
                                @else
                                    {{ substr($loggedInEmployee->first_name, 0, 1) }}{{ substr($loggedInEmployee->last_name, 0, 1) }}
                                @endif
                            </div>
                            <div class="profile-name">{{ $loggedInEmployee->first_name }} {{ $loggedInEmployee->last_name }}</div>
                            @if($loggedInEmployee->role)
                                <div class="profile-role">{{ $loggedInEmployee->role->role_name }}</div>
                            @endif
                            <div class="profile-email">{{ $loggedInEmployee->email }}</div>
                            @if($credentials)
                                <div class="mt-2">
                                    @if($credentials->is_active)
                                        <span class="status-badge status-active">
                                            <i class="bi bi-circle-fill"></i> Active
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="bi bi-circle-fill"></i> Inactive
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </li>
                        
                        {{-- Quick Stats --}}
                        <li class="profile-stats">
                            <div class="stat-item">
                                <div class="stat-value">{{ $loggedInEmployee->projects->count() ?? 0 }}</div>
                                <div class="stat-label">Projects</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ $loggedInEmployee->role ? $loggedInEmployee->role->employees_count ?? 0 : 0 }}</div>
                                <div class="stat-label">Team</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ $credentials ? \Carbon\Carbon::parse($credentials->last_login_at)->diffForHumans() : 'Never' }}</div>
                                <div class="stat-label">Last Login</div>
                            </div>
                        </li>
                        
                        {{-- Menu Items --}}
                        <li><a class="profile-menu-item" href="{{ route('profile.show', $loggedInEmployee->employee_id) }}">
                            <i class="bi bi-person"></i> My Profile
                        </a></li>
                        <li><a class="profile-menu-item" href="{{ route('profile.edit', $loggedInEmployee->employee_id) }}">
                            <i class="bi bi-pencil-square"></i> Edit Profile
                        </a></li>
                        <li><a class="profile-menu-item" href="{{ route('profile.security', $loggedInEmployee->employee_id) }}">
                            <i class="bi bi-shield-lock"></i> Security Settings
                        </a></li>
                        <li><a class="profile-menu-item" href="{{ route('notifications.index') }}">
                            <i class="bi bi-bell"></i> Notifications
                        </a></li>
                        
                        {{-- Last Login --}}
                        @if($credentials && $credentials->last_login_at)
                            <li class="last-login">
                                <i class="bi bi-clock-history me-1"></i>
                                Last login: {{ \Carbon\Carbon::parse($credentials->last_login_at)->format('M d, Y \a\t h:i A') }}
                            </li>
                        @endif
                        
                        {{-- Logout --}}
                        <li class="border-top">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="profile-menu-item w-100 text-start bg-transparent border-0">
                                    <i class="bi bi-box-arrow-right text-danger"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                {{-- Fallback if no employee is logged in --}}
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </nav>

    {{-- 2. Fixed Sidebar Navigation --}}
    <nav class="sidebar">
        <div class="d-flex flex-column h-100">
            <ul class="nav flex-column mt-3">
                <li class="nav-item nav-header">MENU</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('employees*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                        <i class="bi bi-people me-2"></i> Employees
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('roles*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                        <i class="bi bi-person-badge me-2"></i> Roles & Permissions
                    </a>
                </li>
                
                {{-- Projects Section --}}
                <li class="nav-item nav-header mt-4">PROJECTS</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('projects*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                        <i class="bi bi-folder me-2"></i> All Projects
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('projects/create') ? 'active' : '' }}" href="{{ route('projects.create') }}">
                        <i class="bi bi-plus-circle me-2"></i> New Project
                    </a>
                </li>
                
                {{-- Reports Section --}}
                <li class="nav-item nav-header mt-4">REPORTS</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('reports*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                        <i class="bi bi-bar-chart me-2"></i> Analytics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('audit-logs*') ? 'active' : '' }}" href="{{ route('audit-logs.index') }}">
                        <i class="bi bi-clock-history me-2"></i> Audit Logs
                    </a>
                </li>
                
                {{-- Settings Section --}}
                <li class="nav-item nav-header mt-4">SETTINGS</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('settings*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                        <i class="bi bi-gear me-2"></i> System Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('modules*') ? 'active' : '' }}" href="{{ route('modules.index') }}">
                        <i class="bi bi-puzzle me-2"></i> Module Access
                    </a>
                </li>
            </ul>
            
            {{-- Sidebar Footer --}}
            <div class="mt-auto p-3 border-top border-secondary">
                <div class="text-center small text-muted">
                    <div>FlowDash v1.4.0</div>
                    <div class="mt-1">© {{ date('Y') }}</div>
                </div>
            </div>
        </div>
    </nav>
    
    {{-- 3. Main Content Wrapper --}}
    <div class="main-wrapper">
        <main role="main" class="flex-grow-1 p-4">
            <div class="container-fluid"> 
                @yield('content')
            </div>
        </main>

        <footer class="footer bg-white border-top mt-auto py-3">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    © {{ date('Y') }} FlowDash User System. All rights reserved.
                </span>
                <span class="text-muted small">
                    Logged in as: {{ $loggedInEmployee->email ?? 'Guest' }}
                </span>
            </div>
        </footer>
    </div>

    {{-- Bootstrap JS Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6C82uI6F5C7C8f6v4G0G2c9nF6jI5V5iF1T2n8O9m9M9v7wV1M0B3F5B4B2P1P1F5B4B2P1P1" crossorigin="anonymous"></script>

    <script>
        // Mobile sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-open');
        });
        
        // Auto-hide sidebar on mobile when clicking outside
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            if (window.innerWidth < 992 && 
                !sidebar.contains(event.target) && 
                event.target !== toggleBtn && 
                !toggleBtn?.contains(event.target)) {
                document.body.classList.remove('sidebar-open');
            }
        });
        
        // Update user status indicators
        document.addEventListener('DOMContentLoaded', function() {
            // Update last login time
            const lastLoginElement = document.querySelector('.last-login');
            if (lastLoginElement) {
                setInterval(() => {
                    const lastLoginTime = lastLoginElement.getAttribute('data-timestamp');
                    if (lastLoginTime) {
                        const timeAgo = timeSince(new Date(lastLoginTime));
                        lastLoginElement.querySelector('span').textContent = `Last login: ${timeAgo}`;
                    }
                }, 60000); // Update every minute
            }
            
            // Mark active menu items
            const currentPath = window.location.pathname;
            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });
        
        // Helper function for time ago
        function timeSince(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            let interval = Math.floor(seconds / 31536000);
            
            if (interval > 1) return interval + " years ago";
            interval = Math.floor(seconds / 2592000);
            if (interval > 1) return interval + " months ago";
            interval = Math.floor(seconds / 86400);
            if (interval > 1) return interval + " days ago";
            interval = Math.floor(seconds / 3600);
            if (interval > 1) return interval + " hours ago";
            interval = Math.floor(seconds / 60);
            if (interval > 1) return interval + " minutes ago";
            return Math.floor(seconds) + " seconds ago";
        }
    </script>

    @yield('scripts')
</body>
</html>