@extends('layouts.app')

@section('title', 'Employees Management - Add Role')

@section('styles')
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
    
    /* Permission checkboxes container */
    .permission-checkboxes {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .permission-checkbox-item {
        flex: 0 0 calc(50% - 0.375rem);
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
        
        .permission-checkbox-item {
            flex: 0 0 100%;
        }
    }
</style>
@endsection

@section('content')
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

            @php
                $permissionsByModule = $moduleAccesses->groupBy('module');

                $accessLabels = [
                    'none'   => 'No Access',
                    'view'   => 'Can View Only',
                    'create' => 'Can Create',
                    'edit'   => 'Can Edit',
                    'delete' => 'Can Delete',
                    'manage' => 'Can Manage (Full CRUD)',
                    'full'   => 'Full Access (Admin)',
                ];
            @endphp
            <!-- Permissions Tab -->
            <div class="tab-pane fade" id="permissions" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-4">Select the permissions this role should have for each module</p>
                        
                            <div class="module-group">
                                <h3 class="module-group-title">Core Modules</h3>

                                <div class="permissions-container">
                                    @foreach($permissionsByModule as $module => $permissions)
                                        <div class="resource-container">
                                            <div class="permission-title">{{ $module }}</div>

                                            <div class="permission-options">
                                                <div class="permission-checkboxes">
                                                    @foreach($permissions as $permission)
                                                        @php
                                                            $level = $permission->access_level;
                                                            $id = \Illuminate\Support\Str::slug($module, '_') . '_' . $level;
                                                        @endphp

                                                        <div class="permission-checkbox-item">
                                                            <div class="form-check">
                                                                <input type="checkbox"
                                                                    name="module_access_ids[]"
                                                                    id="{{ $id }}"
                                                                    value="{{ $permission->access_id }}"
                                                                    class="form-check-input permission-checkbox">

                                                                <label class="form-check-label" for="{{ $id }}">
                                                                    {{ $accessLabels[$level] ?? ucfirst($level) }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            </div>


                        <!-- Additional Modules -->
                        <div class="module-group">
                            <h3 class="module-group-title">Additional Modules</h3>
                            <div class="permissions-container">
                                <!-- Dashboard -->
                                <div class="resource-container">
                                    <div class="permission-title">Dashboard</div>
                                    <div class="permission-options">
                                        <div class="permission-checkboxes">
                                            @php
                                                $dashboardPermissions = \App\Models\ModuleAccess::where('module', 'Dashboard')->get();
                                            @endphp
                                            @foreach($dashboardPermissions as $permission)
                                            <div class="permission-checkbox-item">
                                                <div class="form-check">
                                                    <input type="checkbox" name="module_access_ids[]" 
                                                           id="dashboard_{{ $permission->access_level }}" 
                                                           value="{{ $permission->access_id }}"
                                                           class="form-check-input permission-checkbox">
                                                    <label class="form-check-label" for="dashboard_{{ $permission->access_level }}">
                                                        {{ $permission->access_level === 'none' ? 'No Access' : 
                                                           ($permission->access_level === 'view' ? 'Can View Dashboard' :
                                                           ($permission->access_level === 'full' ? 'Full Dashboard Access' : $permission->access_level)) }}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Reports -->
                                <div class="resource-container">
                                    <div class="permission-title">Reports</div>
                                    <div class="permission-options">
                                        <div class="permission-checkboxes">
                                            @php
                                                $reportsPermissions = \App\Models\ModuleAccess::where('module', 'Reports')->get();
                                            @endphp
                                            @foreach($reportsPermissions as $permission)
                                            <div class="permission-checkbox-item">
                                                <div class="form-check">
                                                    <input type="checkbox" name="module_access_ids[]" 
                                                           id="reports_{{ $permission->access_level }}" 
                                                           value="{{ $permission->access_id }}"
                                                           class="form-check-input permission-checkbox">
                                                    <label class="form-check-label" for="reports_{{ $permission->access_level }}">
                                                        {{ $permission->access_level === 'none' ? 'No Access' : 
                                                           ($permission->access_level === 'view' ? 'Can View Reports' :
                                                           ($permission->access_level === 'create' ? 'Can Generate Reports' :
                                                           ($permission->access_level === 'full' ? 'Full Reports Access' : $permission->access_level))) }}
                                                    </label>
                                                </div>
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
@endsection

@section('scripts')
<script>
    // Quick permission selection
    function selectAllPermissions(accessLevel) {
        // Get all permissions with the specified access level
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        
        checkboxes.forEach(checkbox => {
            // Extract access level from label text
            const label = checkbox.closest('.form-check').querySelector('.form-check-label').textContent.trim();
            const checkboxAccessLevel = getAccessLevelFromLabel(label);
            
            // Check if this checkbox matches the selected access level
            if (checkboxAccessLevel === accessLevel) {
                checkbox.checked = true;
            } else {
                checkbox.checked = false;
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
    
    // Helper function to extract access level from label text
    function getAccessLevelFromLabel(label) {
        if (label.includes('No Access')) return 'none';
        if (label.includes('View Only') || label.includes('View Dashboard') || label.includes('View Reports')) return 'view';
        if (label.includes('Can Create') || label.includes('Generate Reports')) return 'create';
        if (label.includes('Can Edit')) return 'edit';
        if (label.includes('Can Delete')) return 'delete';
        if (label.includes('Full CRUD')) return 'manage';
        if (label.includes('Admin') || label.includes('Full Dashboard') || label.includes('Full Reports')) return 'full';
        return 'none';
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
        
        // Check if at least one permission is selected
        const checkedPermissions = document.querySelectorAll('input[name="module_access_ids[]"]:checked');
        
        if (checkedPermissions.length === 0) {
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
@endsection