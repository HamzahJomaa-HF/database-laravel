<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | FlowDash User System</title>
    
    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    {{-- Custom Styles --}}
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #273449;
            --sidebar-hover: #354761;
            --main-bg: #f4f6f9;
        }
        body { background-color: var(--main-bg); }

        .sidebar-wrapper { width: var(--sidebar-width); background-color: var(--sidebar-bg); color: #fff; height: 100vh; position: fixed; top: 0; left: 0; padding-top: 56px; overflow-y: auto; }
        .main-wrapper { margin-left: var(--sidebar-width); padding-top: 56px; min-height: 100vh; display: flex; flex-direction: column; transition: margin-left 0.3s; }
        .top-navbar { left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); background-color: #fff; z-index: 1060; }
        .badge-pro { background-color: #f7a83d; color: #fff; font-size: 0.6em; padding: .3em .6em; margin-left: 5px; line-height: 1; }
    </style>

    @yield('styles')
</head>
<body>

    {{-- Sidebar --}}
    <div class="sidebar-wrapper">
        @include('layouts.navigation')
    </div>

    {{-- Top Navbar --}}
    <nav class="navbar navbar-light shadow-sm fixed-top top-navbar">
        <div class="container-fluid py-1">
          <form class="d-flex me-auto" action="{{ route('users.index') }}" method="GET">
    <div class="input-group">
        <span class="input-group-text bg-light border-0">
            <i class="bi bi-search"></i>
        </span>
        <input name="name" class="form-control me-2 border-0 bg-light" type="search" 
               placeholder="Search users..." value="{{ request('name') }}">
    </div>
</form>


            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <li class="nav-item me-3"><a class="nav-link text-muted" href="#"><i class="bi bi-bell fs-5"></i></a></li>
                <li class="nav-item me-3"><a class="nav-link text-muted" href="#"><i class="bi bi-gear fs-5"></i></a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <span class="text-dark me-2 d-none d-md-inline">Adrian D.</span>
                        <img src="https://placehold.co/36x36/87CEFA/FFFFFF?text=AD" class="rounded-circle" alt="User Avatar">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    {{-- Main Content --}}
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

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>
