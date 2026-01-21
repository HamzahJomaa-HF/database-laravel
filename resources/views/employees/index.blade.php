@extends('layouts.app')

@section('title', 'Employees Management')

@section('styles')
    <!-- Kendo UI CSS (for similar styling) -->
    <link href="https://kendo.cdn.telerik.com/themes/6.7.0/default/default-main.css" rel="stylesheet">
  
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --border-color: #e5e7eb;
            --table-header-bg: #f9fafb;
            --table-row-alt-bg: #f8fafc;
        }
        
        .page-title {
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        .main-div {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .filtering-bar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            gap: 1rem;
        }
        
        .search-input-container {
            flex: 1;
            max-width: 400px;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 0.625rem 2.5rem 0.625rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .search-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            width: 1.25rem;
            height: 1.25rem;
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
            background: none;
            border-bottom: 2px solid transparent;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        
        .scrollable-grid {
            overflow-x: auto;
        }
        
        .table-container {
            min-width: 1000px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table thead th {
            background-color: var(--table-header-bg);
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid var(--border-color);
            font-size: 0.875rem;
            white-space: nowrap;
        }
        
        .table tbody tr {
            border-bottom: 1px solid var(--border-color);
        }
        
        .table tbody tr:nth-child(even) {
            background-color: var(--table-row-alt-bg);
        }
        
        .table tbody td {
            padding: 1rem;
            font-size: 0.875rem;
            color: #4b5563;
            vertical-align: middle;
        }
        
        .toggle-container {
            display: inline-block;
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
            background-color: #10b981;
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
        
        .info-icon {
            color: #757575;
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .info-icon:hover {
            color: var(--primary-color);
        }
        
        .reset-button {
            padding: 0.5rem 1rem;
            background-color: #ef4444;
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .reset-button:hover {
            background-color: #dc2626;
        }
        
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
        
        .pagination-numbers {
            display: flex;
            gap: 0.5rem;
        }
        
        .pagination-button {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border-color);
            background-color: white;
            color: #374151;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .pagination-button:hover {
            background-color: #f3f4f6;
        }
        
        .pagination-button.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .pagination-nav-button {
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            background-color: white;
            color: #374151;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .pagination-nav-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .pagination-nav-button:hover:not(:disabled) {
            background-color: #f3f4f6;
        }
        
        .pagination-info {
            font-size: 0.875rem;
            color: var(--secondary-color);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }
        
        .empty-state-icon {
            width: 4rem;
            height: 4rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background-color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        
        .btn-outline {
            background-color: white;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-outline:hover {
            background-color: #eff6ff;
        }
        
        .filters-container {
            display: flex;
            gap: 1rem;
        }
        
        .filter-select {
            padding: 0.625rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            background-color: white;
            color: #374151;
            min-width: 150px;
        }
        
        @media (max-width: 768px) {
            .filtering-bar-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-input-container {
                max-width: 100%;
            }
            
            .filters-container {
                flex-wrap: wrap;
            }
            
            .table-container {
                min-width: auto;
            }
            
            .scrollable-grid {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
@endsection

@section('content')
<div class="dashboard-content">
    <div class="main-div">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="page-title">Employees</h4>
            <div class="action-buttons">
                <a href="{{ route('roles.index') }}" class="btn-outline">
                    <i class="fas fa-user-shield"></i> Roles
                </a>
                <a href="{{ route('employees.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Add Employee
                </a>
            </div>
        </div>

        <!-- Filtering Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="filtering-bar-container">
                    <div class="search-input-container">
                        <input type="text" 
                               class="search-input" 
                               placeholder="Search by name or email..." 
                               value="{{ request('search') }}"
                               id="searchInput">
                            <path d="M21.53 20.47l-3.66-3.66A8.98 8.98 0 0 0 20 11a9 9 0 1 0-9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66a.75.75 0 1 0 1.06-1.06zM3.5 11a7.5 7.5 0 1 1 15 0 7.5 7.5 0 0 1-15 0z"></path>
                        </svg>
                    </div>
                    
                    <div class="filters-container">
                        <select class="filter-select" id="roleFilter">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->role_id }}" {{ request('role') == $role->role_id ? 'selected' : '' }}>
                                    {{ $role->role_name }}
                                </option>
                            @endforeach
                        </select>
                        
                        <select class="filter-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        
                        <button class="btn-outline" onclick="resetFilters()">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#employees-tab">
                    Employees ({{ $employees->total() }})
                </button>
            </li>
        </ul>

        <!-- Table -->
        <div class="scrollable-grid mt-4">
            <div class="table-container">
                @if($employees->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Phone Number</th>
                                <th>Employee Type</th>
                                <th>Start Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                            <tr class="{{ $loop->even ? 'table-row-alt' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            <span class="avatar-text">
                                                {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                            <div class="text-muted small">{{ $employee->external_id ?? 'No ID' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $employee->email }}</td>
                                <td>
                                    @if($employee->role)
                                        <span class="badge bg-primary">{{ $employee->role->role_name }}</span>
                                    @else
                                        <span class="badge bg-secondary">No Role</span>
                                    @endif
                                </td>
                                <td>
                                    @if($employee->credentials && $employee->credentials->is_active)
                                        <div class="toggle-container">
                                            <div class="toggle toggle-on" onclick="toggleStatus('{{ $employee->employee_id }}')">
                                                <div class="toggle-knob"></div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="toggle-container">
                                            <div class="toggle" onclick="toggleStatus('{{ $employee->employee_id }}')">
                                                <div class="toggle-knob"></div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $employee->phone_number ?? '-' }}</td>
                                <td>{{ $employee->employee_type ?? '-' }}</td>
                                <td>{{ $employee->start_date ? $employee->start_date->format('Y-m-d') : '-' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="viewEmployee('{{ $employee->employee_id }}')"
                                                title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary"
                                                onclick="editEmployee('{{ $employee->employee_id }}')"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="reset-button btn-sm"
                                                onclick="resetPassword('{{ $employee->employee_id }}')">
                                            Reset Password
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="fas fa-users empty-state-icon"></i>
                        <h5 class="mb-2">No employees found</h5>
                        <p class="text-muted">Add your first employee to get started</p>
                        <a href="{{ route('employees.create') }}" class="btn-primary mt-3">
                            <i class="fas fa-plus"></i> Add Employee
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
        <div class="pagination-container">
            <div class="pagination-numbers">
                @if($employees->onFirstPage())
                    <button class="pagination-nav-button" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $employees->previousPageUrl() }}" class="pagination-nav-button">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif
                
                @foreach(range(1, min(5, $employees->lastPage())) as $page)
                    <a href="{{ $employees->url($page) }}" 
                       class="pagination-button {{ $employees->currentPage() == $page ? 'active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach
                
                @if($employees->hasMorePages())
                    <a href="{{ $employees->nextPageUrl() }}" class="pagination-nav-button">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <button class="pagination-nav-button" disabled>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                @endif
            </div>
            
            <div class="pagination-info">
                Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }} of {{ $employees->total() }} entries
            </div>
            
            <div>
                <select class="filter-select" onchange="changePerPage(this.value)">
                    <option value="10" {{ $employees->perPage() == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $employees->perPage() == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $employees->perPage() == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $employees->perPage() == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const roleFilter = document.getElementById('roleFilter');
        const statusFilter = document.getElementById('statusFilter');
        
        let searchTimeout;
        
        searchInput.addEventListener('keyup', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });
        
        roleFilter.addEventListener('change', applyFilters);
        statusFilter.addEventListener('change', applyFilters);
        
        function applyFilters() {
            const params = new URLSearchParams();
            
            if (searchInput.value) {
                params.set('search', searchInput.value);
            }
            
            if (roleFilter.value) {
                params.set('role', roleFilter.value);
            }
            
            if (statusFilter.value) {
                params.set('status', statusFilter.value);
            }
            
            window.location.href = '{{ route("employees.index") }}?' + params.toString();
        }
        
        function resetFilters() {
            window.location.href = '{{ route("employees.index") }}';
        }
        
        function changePerPage(value) {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', value);
            window.location.href = '{{ route("employees.index") }}?' + params.toString();
        }
        
        function toggleStatus(employeeId) {
            if (confirm('Are you sure you want to toggle the employee status?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/employees/${employeeId}/toggle-status`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function viewEmployee(employeeId) {
            window.location.href = `/employees/${employeeId}`;
        }
        
        function editEmployee(employeeId) {
            window.location.href = `/employees/${employeeId}/edit`;
        }
        
        function resetPassword(employeeId) {
            if (confirm('Are you sure you want to reset this employee\'s password? A new password will be generated and sent to their email.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/credentials/${employeeId}/reset-password`;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Initialize page with current filters
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.get('search')) {
                searchInput.value = urlParams.get('search');
            }
        });
    </script>
@endsection