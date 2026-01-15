
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Management - Add Role</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9fafb;
            color: #374151;
        }
        
        .container-fluid {
            padding: 1.5rem;
        }
        
        .card {
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            box-shadow: var(--shadow-sm);
            background-color: white;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #4b5563;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        
        .form-control {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .form-check-input {
            width: 1.1em;
            height: 1.1em;
            margin-top: 0.2em;
            border: 1px solid #d1d5db;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .form-check-label {
            font-size: 0.875rem;
            color: #4b5563;
            cursor: pointer;
        }
        
        .permissions-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .resource-container {
            background-color: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.25rem;
        }
        
        .permission-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
            font-size: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .permission-options {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0;
        }
        
        .btn {
            font-weight: 500;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background-color: transparent;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .buttons-wrapper {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.875rem;
            margin: 0;
        }
        
        .nav-tabs {
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }
        
        .nav-link {
            color: var(--secondary-color);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border: none;
            background: none;
        }
        
        .nav-link.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }
        
        /* Quick select buttons */
        .quick-select-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        
        .quick-select-btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid var(--border-color);
            background-color: white;
            color: var(--secondary-color);
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .quick-select-btn:hover {
            background-color: #f3f4f6;
        }
        
        .quick-select-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        /* Module groups */
        .module-group {
            margin-bottom: 2rem;
        }
        
        .module-group-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .permissions-container {
                grid-template-columns: 1fr;
            }
            
            .container-fluid {
                padding: 1rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .buttons-wrapper {
                flex-direction: column;
            }
            
            .buttons-wrapper .btn {
                width: 100%;
            }
        }
        
        /* Custom checkbox styling */
        .custom-checkbox {
            position: relative;
            padding-left: 1.75rem;
            cursor: pointer;
        }
        
        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }
        
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 1.25rem;
            width: 1.25rem;
            background-color: white;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
        }
        
        .custom-checkbox:hover input ~ .checkmark {
            background-color: #f3f4f6;
        }
        
        .custom-checkbox input:checked ~ .checkmark {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }
        
        .custom-checkbox input:checked ~ .checkmark:after {
            display: block;
            left: 7px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Add Role</h1>
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Roles
                </a>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs" id="roleTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                    <i class="fas fa-info-circle me-2"></i>Basic Information
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button" role="tab">
                    <i class="fas fa-shield-alt me-2"></i>Permissions
                </button>
            </li>
        </ul>

        <!-- Form -->
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="tab-content" id="roleTabContent">
                <!-- Basic Information Tab -->
                <div class="tab-pane fade show active" id="basic" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label" for="role_name">Role Name *</label>
                                        <input type="text" name="role_name" id="role_name" class="form-control" 
                                               placeholder="Enter role name" value="{{ old('role_name') }}" required>
                                        @error('role_name')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label class="form-label" for="description">Description</label>
                                        <input type="text" name="description" id="description" class="form-control" 
                                               placeholder="Enter role description" value="{{ old('description') }}">
                                        @error('description')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Quick Permission Templates</label>
                                        <div class="quick-select-buttons">
                                            <button type="button" class="quick-select-btn" onclick="selectAllPermissions('none')">
                                                No Access
                                            </button>
                                            <button type="button" class="quick-select-btn" onclick="selectAllPermissions('view')">
                                                View Only
                                            </button>
                                            <button type="button" class="quick-select-btn" onclick="selectAllPermissions('manage')">
                                                Full Manage
                                            </button>
                                            <button type="button" class="quick-select-btn" onclick="selectAllPermissions('full')">
                                                Admin Access
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Tab -->
                <div class="tab-pane fade" id="permissions" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-4">Select the permissions this role should have for each module</p>
                            
                            <!-- Your 4 Main Modules -->
                            <div class="module-group">
                                <h3 class="module-group-title">Core Modules</h3>
                                <div class="permissions-container">
                                    <!-- Programs -->
                                    <div class="resource-container">
                                        <div class="permission-title">Programs</div>
                                        <div class="permission-options">
                                            @foreach(['none', 'view', 'create', 'edit', 'delete', 'manage', 'full'] as $access)
                                            <div class="form-check">
                                                <input type="radio" name="module_access[Programs]" 
                                                       id="Programs_{{ $access }}" value="{{ $access }}"
                                                       class="form-check-input" {{ $access == 'none' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="Programs_{{ $access }}">
                                                    @switch($access)
                                                        @case('none') No Access @break
                                                        @case('view') Can View Only @break
                                                        @case('create') Can Create @break
                                                        @case('edit') Can Edit @break
                                                        @case('delete') Can Delete @break
                                                        @case('manage') Can Manage (Full CRUD) @break
                                                        @case('full') Full Access (Admin) @break
                                                    @endswitch
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Projects -->
                                    <div class="resource-container">
                                        <div class="permission-title">Projects</div>
                                        <div class="permission-options">
                                            @foreach(['none', 'view', 'create', 'edit', 'delete', 'manage', 'full'] as $access)
                                            <div class="form-check">
                                                <input type="radio" name="module_access[Projects]" 
                                                       id="Projects_{{ $access }}" value="{{ $access }}"
                                                       class="form-check-input" {{ $access == 'none' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="Projects_{{ $access }}">
                                                    @switch($access)
                                                        @case('none') No Access @break
                                                        @case('view') Can View Only @break
                                                        @case('create') Can Create @break
                                                        @case('edit') Can Edit @break
                                                        @case('delete') Can Delete @break
                                                        @case('manage') Can Manage (Full CRUD) @break
                                                        @case('full') Full Access (Admin) @break
                                                    @endswitch
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Users -->
                                    <div class="resource-container">
                                        <div class="permission-title">Users (Employees)</div>
                                        <div class="permission-options">
                                            @foreach(['none', 'view', 'create', 'edit', 'delete', 'manage', 'full'] as $access)
                                            <div class="form-check">
                                                <input type="radio" name="module_access[Users]" 
                                                       id="Users_{{ $access }}" value="{{ $access }}"
                                                       class="form-check-input" {{ $access == 'none' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="Users_{{ $access }}">
                                                    @switch($access)
                                                        @case('none') No Access @break
                                                        @case('view') Can View Only @break
                                                        @case('create') Can Create @break
                                                        @case('edit') Can Edit @break
                                                        @case('delete') Can Delete @break
                                                        @case('manage') Can Manage (Full CRUD) @break
                                                        @case('full') Full Access (Admin) @break
                                                    @endswitch
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Activities -->
                                    <div class="resource-container">
                                        <div class="permission-title">Activities</div>
                                        <div class="permission-options">
                                            @foreach(['none', 'view', 'create', 'edit', 'delete', 'manage', 'full'] as $access)
                                            <div class="form-check">
                                                <input type="radio" name="module_access[Activities]" 
                                                       id="Activities_{{ $access }}" value="{{ $access }}"
                                                       class="form-check-input" {{ $access == 'none' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="Activities_{{ $access }}">
                                                    @switch($access)
                                                        @case('none') No Access @break
                                                        @case('view') Can View Only @break
                                                        @case('create') Can Create @break
                                                        @case('edit') Can Edit @break
                                                        @case('delete') Can Delete @break
                                                        @case('manage') Can Manage (Full CRUD) @break
                                                        @case('full') Full Access (Admin) @break
                                                    @endswitch
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Modules (if needed) -->
                            <div class="module-group">
                                <h3 class="module-group-title">Additional Modules</h3>
                                <div class="permissions-container">
                                    <!-- Dashboard -->
                                    <div class="resource-container">
                                        <div class="permission-title">Dashboard</div>
                                        <div class="permission-options">
                                            @foreach(['none', 'view', 'full'] as $access)
                                            <div class="form-check">
                                                <input type="radio" name="module_access[Dashboard]" 
                                                       id="Dashboard_{{ $access }}" value="{{ $access }}"
                                                       class="form-check-input" {{ $access == 'none' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="Dashboard_{{ $access }}">
                                                    @switch($access)
                                                        @case('none') No Access @break
                                                        @case('view') Can View Dashboard @break
                                                        @case('full') Full Dashboard Access @break
                                                    @endswitch
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Reports -->
                                    <div class="resource-container">
                                        <div class="permission-title">Reports</div>
                                        <div class="permission-options">
                                            @foreach(['none', 'view', 'create', 'full'] as $access)
                                            <div class="form-check">
                                                <input type="radio" name="module_access[Reports]" 
                                                       id="Reports_{{ $access }}" value="{{ $access }}"
                                                       class="form-check-input" {{ $access == 'none' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="Reports_{{ $access }}">
                                                    @switch($access)
                                                        @case('none') No Access @break
                                                        @case('view') Can View Reports @break
                                                        @case('create') Can Generate Reports @break
                                                        @case('full') Full Reports Access @break
                                                    @endswitch
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="buttons-wrapper">
                <a href="{{ route('roles.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-times me-2"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check me-2"></i>Create Role
                </button>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Quick permission selection
        function selectAllPermissions(accessLevel) {
            // Select all radio buttons with the given access level
            const modules = ['Programs', 'Projects', 'Users', 'Activities', 'Dashboard', 'Reports'];
            
            modules.forEach(module => {
                const radio = document.querySelector(`input[name="module_access[${module}]"][value="${accessLevel}"]`);
                if (radio) {
                    radio.checked = true;
                }
            });
            
            // Update quick select button states
            document.querySelectorAll('.quick-select-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Highlight the clicked button
            const clickedBtn = document.querySelector(`.quick-select-btn[onclick*="${accessLevel}"]`);
            if (clickedBtn) {
                clickedBtn.classList.add('active');
            }
            
            // Show success message
            showToast(`${accessLevel.charAt(0).toUpperCase() + accessLevel.slice(1)} permissions applied to all modules`);
        }
        
        // Initialize tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabTriggers = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabTriggers.forEach(trigger => {
                trigger.addEventListener('shown.bs.tab', function(event) {
                    // Save active tab to session storage
                    sessionStorage.setItem('activeRoleTab', event.target.id);
                });
            });
            
            // Restore active tab
            const activeTab = sessionStorage.getItem('activeRoleTab');
            if (activeTab) {
                const tabTrigger = document.querySelector(`#${activeTab}`);
                if (tabTrigger) {
                    new bootstrap.Tab(tabTrigger).show();
                }
            }
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const roleName = document.getElementById('role_name').value.trim();
            if (!roleName) {
                e.preventDefault();
                alert('Please enter a role name');
                document.getElementById('role_name').focus();
                return false;
            }
            
            // Check if at least one permission is selected (not all 'none')
            let hasPermission = false;
            const permissionRadios = document.querySelectorAll('input[type="radio"]:checked');
            
            permissionRadios.forEach(radio => {
                if (radio.value !== 'none') {
                    hasPermission = true;
                }
            });
            
            if (!hasPermission) {
                e.preventDefault();
                if (confirm('This role has no permissions. Are you sure you want to create it?')) {
                    return true;
                }
                return false;
            }
            
            return true;
        });
        
        // Toast notification function
        function showToast(message, type = 'success') {
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3`;
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Initialize and show toast
            const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
            bsToast.show();
            
            // Remove toast after it's hidden
            toast.addEventListener('hidden.bs.toast', function() {
                document.body.removeChild(toast);
            });
        }
    </script>
</body>
</html>