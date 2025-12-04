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

    {{-- Custom Styles for Sidebar and Layout --}}
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #273449; /* Dark Blue from the example */
            --sidebar-hover: #354761;
        }
        body {
            background-color: #f4f6f9; /* Light gray background */
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
        }
        /* Main Content Area */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            padding-top: 56px; /* Space for the top Navbar */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        /* Sidebar Nav Links */
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 10px 15px;
        }
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background-color: var(--sidebar-hover);
            color: #fff;
        }
        .sidebar .nav-header {
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            font-size: 0.7rem;
            padding: 10px 15px 5px;
        }
        .navbar {
            z-index: 1060; /* Higher than sidebar */
        }
    </style>
    
    @yield('styles')
</head>
<body>

    {{-- 1. Fixed Top Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container-fluid">
            {{-- Brand placeholder (needs to align with sidebar) --}}
            <a class="navbar-brand d-none d-lg-block" href="#" style="width: var(--sidebar-width); padding-left: 1rem;">
                <span class="fw-bold text-dark"><i class="bi bi-water me-2 text-primary"></i>FlowDash</span>
            </a>
            
            {{-- Search Bar --}}
            <form class="d-flex me-auto ms-lg-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                    <input class="form-control me-2 border-0 bg-light" type="search" placeholder="Search Anything" aria-label="Search" style="width: 250px;">
                </div>
            </form>

            {{-- User/Profile --}}
            <ul class="navbar-nav ms-auto">
                <li class="nav-item me-2"><a class="nav-link" href="#"><i class="bi bi-bell"></i></a></li>
                <li class="nav-item me-3"><a class="nav-link" href="#"><i class="bi bi-gear"></i></a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://via.placeholder.com/30/87CEFA/FFFFFF?text=AD" class="rounded-circle me-1" alt="Adrian D." width="30">
                        <span class="d-none d-md-inline">Adrian D.</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    {{-- 2. Fixed Sidebar Navigation --}}
    <nav class="sidebar">
        <div class="d-flex flex-column h-100">
            <ul class="nav flex-column mt-3">
                <li class="nav-item nav-header">MENU</li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ url('/dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboards
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.index') }}">
                        <i class="bi bi-person-lines-fill me-2"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('users.create') }}">
                        <i class="bi bi-plus-circle me-2"></i> Add New User
                    </a>
                </li>
                
                {{-- UI Component Section Placeholder --}}
                <li class="nav-item nav-header mt-3">UI COMPONENTS</li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-ui-checks me-2"></i> Forms</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-sliders me-2"></i> Range Sliders</a></li>
            </ul>
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
            <div class="container-fluid text-center">
                <span class="text-muted small">© {{ date('Y') }} FlowDash User System.</span>
            </div>
        </footer>
    </div>

    {{-- Bootstrap JS Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6C82uI6F5C7C8f6v4G0G2c9nF6jI5V5iF1T2n8O9m9M9v7wV1M0B3F5B4B2P1P1F5B4B2P1P1" crossorigin="anonymous"></script>

    @yield('scripts')
</body>
</html>