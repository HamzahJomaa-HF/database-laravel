<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | FlowDash User Management</title>
    
    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Custom Styles --}}
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #1a237e;
            --sidebar-hover: #283593;
            --main-bg: #f8f9fa;
            --primary-color: #4361ee;
            --success-color: #06d6a0;
            --warning-color: #ffd166;
            --danger-color: #ef476f;
        }
        
        body { 
            background-color: var(--main-bg); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar-wrapper { 
            width: var(--sidebar-width); 
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #0d1b2a 100%);
            color: #fff; 
            height: 100vh; 
            position: fixed; 
            top: 0; 
            left: 0; 
            padding-top: 70px; 
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .main-wrapper { 
            margin-left: var(--sidebar-width); 
            padding-top: 70px; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
            transition: margin-left 0.3s; 
        }
        
        .top-navbar { 
            left: var(--sidebar-width); 
            width: calc(100% - var(--sidebar-width)); 
            background: linear-gradient(90deg, #ffffff 0%, #f8f9fa 100%);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e9ecef;
            z-index: 1050; 
            height: 70px;
        }
        
        .badge-pro { 
            background: linear-gradient(45deg, #ff6b6b, #ffd166);
            color: #fff; 
            font-size: 0.65em; 
            padding: .35em .7em; 
            margin-left: 5px; 
            line-height: 1;
            border-radius: 12px;
        }
        
        .nav-header {
            color: #adb5bd;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem 1.5rem 0.5rem;
            margin-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .nav-link {
            color: #e9ecef;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            margin: 0.1rem 0.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
            transform: translateX(5px);
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-success {
            background: linear-gradient(45deg, var(--success-color), #04a380);
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), #3a56d4);
            border: none;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: #6c757d;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        /* Custom scrollbar */
        .sidebar-wrapper::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar-wrapper::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar-wrapper::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 2px;
        }
        
        /* Loading animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Search bar styling */
        .search-container {
            width: 300px;
            margin-left: 3.5rem;
        }
        
        .search-input-group {
            position: relative;
        }
        
        .search-input {
            padding-left: 2.5rem;
            padding-right: 2.5rem;
            border-radius: 20px;
            background-color: #f1f3f9;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
         .navbar .search-input::placeholder {
        color: #6c757d !important;
        opacity: 0.7 !important;
     }

        
        .search-input:focus {
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            border-color: var(--primary-color);
        }
        
        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
        }
        
        .search-clear {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
            background: none;
            border: none;
            display: none;
        }
        
        .search-clear:hover {
            color: #ef476f;
        }
    </style>

    @yield('styles')
</head>
<body>

    {{-- Sidebar --}}
    <div class="sidebar-wrapper">
        @include('layouts.navigation')
    </div>

    {{-- Top Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm fixed-top top-navbar">
        <div class="container-fluid">
            <div class="d-flex align-items-center w-100">
               

                <div class="ms-auto d-flex align-items-center">
                    <ul class="navbar-nav flex-row align-items-center">
                        <!-- <li class="nav-item me-3 position-relative">
                            <a class="nav-link text-muted position-relative" href="#">
                                <i class="bi bi-bell fs-5"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    3
                                </span>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item me-3">
                            <a class="nav-link text-muted" href="#">
                                <i class="bi bi-gear fs-5"></i>
                            </a>
                        </li> -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="me-2 text-end d-none d-md-block">
                                    <div class="fw-semibold text-dark" style="font-size: 0.9rem;">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                                    <div class="text-muted small">{{ Auth::user()->employee_type }}</div>
                                </div>
                                <div class="position-relative">
                                    <img src="https://placehold.co/40x40/4361ee/FFFFFF?text=AD" class="rounded-circle border border-2 border-white shadow" alt="User Avatar">
                                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle">
                                        <span class="visually-hidden">Online</span>
                                    </span>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="min-width: 200px;">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>My Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-lock me-2"></i>Account Settings</a></li>
                                <li><hr class="dropdown-divider my-2"></li>
                                <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <div class="main-wrapper">
        <main role="main" class="flex-grow-1 p-4 fade-in">
            <div class="container-fluid px-4"> 
                {{-- THIS IS WHERE YOUR IMPORT AND STATISTICS PAGES WILL LOAD --}}
                @yield('content')
            </div>
        </main>

        <footer class="footer bg-white border-top mt-auto py-3">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span class="text-muted small">© {{ date('Y') }} Hariri Foundation Management System. All rights reserved.</span>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="text-muted small">v1.0.0 • Built with <i class="bi bi-heart-fill text-danger"></i></span>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Search functionality --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.search-input');
            const searchClear = document.querySelector('.search-clear');
            
            // Show/hide clear button based on input
            searchInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    searchClear.style.display = 'block';
                } else {
                    searchClear.style.display = 'none';
                }
            });
            
            // Clear search input
            searchClear.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.focus();
                this.style.display = 'none';
            });
            
            // Search functionality (example)
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    const query = this.value.trim();
                    if (query) {
                        // Here you would implement your search logic
                        console.log('Searching for:', query);
                        // Example: window.location.href = `/search?q=${encodeURIComponent(query)}`;
                    }
                }
            });
        });
    </script>

    {{-- THIS IS WHERE YOUR IMPORT AND STATISTICS PAGES WILL LOAD THEIR SCRIPTS --}}
    @yield('scripts')
</body>
</html>