<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Roles Management</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Kendo UI Styles (if needed) -->
    <link href="https://kendo.cdn.telerik.com/themes/6.7.0/default/default-main.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --border-color: #e5e7eb;
            --card-bg: #ffffff;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #dc2626;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
            color: #374151;
            margin: 0;
            padding: 0;
        }
        
        .main-container {
            display: flex;
            min-height: 100vh;
        }
        
        .navigation-bar {
            width: 250px;
            background-color: white;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }
        
        .nav-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .logo {
            width: 160px;
            height: 57px;
            margin-bottom: 1rem;
        }
        
        .environment {
            background-color: #dc2626;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .environment .version {
            font-size: 0.7rem;
            opacity: 0.9;
        }
        
        .dashboard-content-container {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .main-nav {
            background-color: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: flex-end;
        }
        
        .dashboard-content {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
        }
        
        .page-title {
            font-weight: bold;
            color: var(--primary-color);
            margin: 0;
            font-size: 1.5rem;
        }
        
        .buttons-wrapper {
            display: flex;
            gap: 0.75rem;
        }
        
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid transparent;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
        
        .btn-outline-primary {
            background-color: white;
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: #eff6ff;
        }
        
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .filter-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background-color: white;
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }
        
        .search-input-container {
            flex: 1;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 0.625rem 0.875rem 0.625rem 2.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }
        
        .search-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }
        
        .nav-tabs {
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: var(--secondary-color);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            background-color: transparent;
        }
        
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table th {
            background-color: #f9fafb;
            padding: 1rem;
            font-weight: 600;
            color: #4b5563;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
        }
        
        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: #f9fafb;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .toggle-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .toggle {
            width: 3.5rem;
            height: 2rem;
            background-color: #d1d5db;
            border-radius: 1rem;
            position: relative;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .toggle-on {
            background-color: var(--success-color);
        }
        
        .toggle-knob {
            position: absolute;
            top: 0.25rem;
            left: 0.25rem;
            width: 1.5rem;
            height: 1.5rem;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .toggle-on .toggle-knob {
            transform: translateX(1.5rem);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        
        .page-link {
            padding: 0.5rem 0.875rem;
            border: 1px solid var(--border-color);
            background-color: white;
            color: var(--secondary-color);
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .page-link:hover {
            background-color: #f9fafb;
        }
        
        .page-link.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--secondary-color);
        }
        
        .empty-state-icon {
            font-size: 3rem;
            color: var(--border-color);
            margin-bottom: 1rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .action-btn {
            padding: 0.375rem;
            border: none;
            background: none;
            color: var(--secondary-color);
            cursor: pointer;
            border-radius: 0.25rem;
            transition: all 0.2s;
        }
        
        .action-btn:hover {
            background-color: #f9fafb;
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }
            
            .navigation-bar {
                width: 100%;
                height: auto;
            }
            
            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .buttons-wrapper {
                flex-direction: column;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Navigation Sidebar -->
        <div class="navigation-bar">
            <div class="nav-header">
                <div class="nav-button-responsive">
                    <div class="collapse-button">
                        <button type="button" class="btn btn-none">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                </div>
                <a href="/">
                    <img src="/logo.svg" alt="logo" class="logo">
                </a>
                <div class="environment">
                    PROD <span class="version">v1.4.0</span>
                </div>
                <button type="button" class="collapse-btn">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>
            
            <!-- Navigation Links -->
            <div class="nav-links">
                <a class="navigation-link" href="/">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a class="navigation-link" href="/employees">
                    <i class="fas fa-users"></i> Employees
                </a>
                <a class="navigation-link active" href="/roles">
                    <i class="fas fa-user-tag"></i> Roles
                </a>
                <!-- Add more navigation links as needed -->
            </div>
        </div>

        <!-- Main Content -->
        <div class="dashboard-content-container">
            <!-- Top Navigation -->
            <nav class="main-nav">
                <div class="dropdown">
                    <button type="button" class="dropdown-toggle btn btn-primary">
                        <div class="img-container">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="caret-container">
                            Admin <i class="fas fa-chevron-down"></i>
                        </div>
                    </button>
                </div>
            </nav>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Page Header -->
                <div class="container-fluid p-4">
                    <div class="d-flex flex-row w-100 justify-content-between mb-4">
                        <h1 class="page-title">Roles Management</h1>
                        <div class="buttons-wrapper">
                            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Role
                            </a>
                        </div>
                    </div>

                    <!-- Filter Row -->
                    <div class="filter-row">
                        <div class="search-input-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" 
                                   class="search-input" 
                                   placeholder="Search roles..." 
                                   id="searchInput">
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">All</a></li>
                                <li><a class="dropdown-item" href="#">Active</a></li>
                                <li><a class="dropdown-item" href="#">Inactive</a></li>
                            </ul>
                        </div>
                        
                        <button class="btn btn-none">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>

                    <!-- Tabs -->
                    <ul class="nav nav-tabs" id="rolesTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="roles-tab" data-bs-toggle="tab" data-bs-target="#roles" type="button">
                                Roles
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button">
                                Permissions
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="rolesTabContent">
                        <!-- Roles Tab -->
                        <div class="tab-pane fade show active" id="roles" role="tabpanel">
                            @if($roles->count() > 0)
                                <div class="card">
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Role Name</th>
                                                        <th>Description</th>
                                                        <th>Permissions</th>
                                                        <th>Assigned Users</th>
                                                        <th>Status</th>
                                                        <th>Created At</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($roles as $role)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-user-tag text-primary me-2"></i>
                                                                    <strong>{{ $role->role_name }}</strong>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="text-muted">
                                                                    {{ $role->description ?: 'No description' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($role->moduleAccesses && $role->moduleAccesses->count() > 0)
                                                                    <div class="d-flex flex-wrap gap-1">
                                                                        @foreach($role->moduleAccesses->take(3) as $access)
                                                                            <span class="badge badge-info">
                                                                                {{ $access->module }}
                                                                            </span>
                                                                        @endforeach
                                                                        @if($role->moduleAccesses->count() > 3)
                                                                            <span class="badge">
                                                                                +{{ $role->moduleAccesses->count() - 3 }} more
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">No permissions</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-success">
                                                                    {{ $role->employees_count }} users
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="toggle-container">
                                                                    <span class="text-success">
                                                                        <i class="fas fa-circle"></i> Active
                                                                    </span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                {{ $role->created_at->format('d/m/Y') }}
                                                            </td>
                                                            <td>
                                                                <div class="action-buttons">
                                                                    <a href="{{ route('roles.show', $role) }}" 
                                                                       class="action-btn" 
                                                                       title="View">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('roles.edit', $role) }}" 
                                                                       class="action-btn" 
                                                                       title="Edit">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <a href="{{ route('roles.permissions', $role) }}" 
                                                                       class="action-btn" 
                                                                       title="Permissions">
                                                                        <i class="fas fa-key"></i>
                                                                    </a>
                                                                    <form action="{{ route('roles.destroy', $role) }}" 
                                                                          method="POST" 
                                                                          class="d-inline"
                                                                          onsubmit="return confirm('Are you sure you want to delete this role?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="action-btn text-danger" 
                                                                                title="Delete">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pagination -->
                                <div class="pagination">
                                    {{ $roles->links() }}
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-user-tag"></i>
                                    </div>
                                    <h4>No roles found</h4>
                                    <p>Create your first role to get started</p>
                                    <a href="{{ route('roles.create') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus"></i> Create Role
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Permissions Tab -->
                        <div class="tab-pane fade" id="permissions" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Module Permissions</h5>
                                    <p class="text-muted">Manage module access permissions for different roles</p>
                                    <!-- Add permissions management content here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Search functionality
        $('#searchInput').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            $('table tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // Toggle functionality
        document.querySelectorAll('.toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                this.classList.toggle('toggle-on');
            });
        });

        // Tab switching
        const triggerTabList = document.querySelectorAll('#rolesTab button')
        triggerTabList.forEach(triggerEl => {
            const tabTrigger = new bootstrap.Tab(triggerEl)
            
            triggerEl.addEventListener('click', event => {
                event.preventDefault()
                tabTrigger.show()
            })
        });

        // Delete confirmation
        function confirmDelete(event) {
            if (!confirm('Are you sure you want to delete this role?')) {
                event.preventDefault();
                return false;
            }
            return true;
        }
    </script>
</body>
</html>