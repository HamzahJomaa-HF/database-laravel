<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Management - Edit Employee</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 for enhanced dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --border-color: #e5e7eb;
            --card-bg: #ffffff;
            --warning-color: #f59e0b;
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
            display: flex;
            justify-content: center;
            align-items: flex-start;
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
        
        .btn-warning {
            background-color: var(--warning-color);
            color: white;
            border-color: var(--warning-color);
        }
        
        .btn-warning:hover {
            background-color: #d97706;
            border-color: #d97706;
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
        
        .form-label {
            font-weight: 500;
            color: #4b5563;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            display: block;
        }
        
        .form-control {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .form-control:disabled {
            background-color: #f9fafb;
            cursor: not-allowed;
        }
        
        .phone-input-container {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            overflow: hidden;
        }
        
        .phone-country {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 0.875rem;
            background-color: #f9fafb;
            border-right: 1px solid var(--border-color);
        }
        
        .phone-country-select {
            border: none;
            background: none;
            font-size: 0.875rem;
            color: #374151;
            min-width: 40px;
        }
        
        .phone-input {
            flex: 1;
            border: none;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
        }
        
        .phone-input:focus {
            outline: none;
        }
        
        .password-field {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            padding: 0.25rem;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        
        .form-check-input {
            width: 1.1em;
            height: 1.1em;
            margin-top: 0;
        }
        
        .form-check-label {
            font-size: 0.875rem;
            color: #4b5563;
            cursor: pointer;
        }
        
        .required::after {
            content: " *";
            color: #dc2626;
        }
        
        .optional-label {
            font-size: 0.75rem;
            color: var(--secondary-color);
            font-weight: normal;
            margin-left: 0.25rem;
        }
        
        .select2-container {
            width: 100% !important;
        }
        
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            min-height: 42px;
            padding: 0.375rem;
        }
        
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default .select2-selection--multiple:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .error-message {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        /* Multi-select chips styling */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #e0f2fe;
            border: 1px solid #bae6fd;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            color: #0369a1;
            margin-right: 0.25rem;
            margin-top: 0.25rem;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #0369a1;
            margin-right: 0.25rem;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #dc2626;
        }
        
        .projects-info {
            font-size: 0.75rem;
            color: var(--secondary-color);
            margin-top: 0.5rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .form-container {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
        }
        
        .current-info {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .current-info-title {
            font-weight: 600;
            color: #0369a1;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .current-info-content {
            font-size: 0.875rem;
            color: #374151;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
        
        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }
        
       
        
        .password-change-toggle {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.875rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            margin-bottom: 0.5rem;
        }
        
        .password-change-toggle:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }
            
            .navigation-bar {
                width: 100%;
                height: auto;
            }
            
            .buttons-wrapper {
                flex-direction: column;
                width: 100%;
            }
            
            .buttons-wrapper .btn {
                width: 100%;
            }
            
            .form-container {
                max-width: 100%;
                padding: 0 15px;
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
        </div>

        <!-- Dashboard Content Container -->
        <div class="dashboard-content-container">
            <!-- Main Navigation -->
            <div class="main-nav">
                <!-- User profile or other nav items can go here -->
            </div>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <div class="form-container">
                    <!-- Page Header -->
                    <div class="d-flex flex-row w-100 justify-content-between mb-4">
                        <h1 class="page-title">
                            Edit Employee
                           
                        </h1>
                        <div class="buttons-wrapper">
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            @if($employee->is_active)
                                <form action="{{ route('employees.deactivate', $employee->employee_id) }}" method="POST" class="d-inline" id="deactivateForm">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning" onclick="return confirmDeactivate()">
                                        <i class="fas fa-user-slash"></i> Deactivate
                                    </button>
                                </form>
                            @else
                               
                            @endif
                            <button type="submit" form="editEmployeeForm" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </div>

                    <!-- Current Employee Information -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="current-info">
                                <div class="current-info-title">
                                    <i class="fas fa-info-circle"></i> Current Information
                                </div>
                                <div class="current-info-content">
                                    <strong>Name:</strong> {{ $employee->first_name }} {{ $employee->last_name }}<br>
                                    <strong>Email:</strong> {{ $employee->email }}<br>
                                    <strong>Role:</strong> {{ $employee->role->role_name ?? 'Not assigned' }}<br>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form id="editEmployeeForm" action="{{ route('employees.update', $employee->employee_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="card">
                            <div class="card-body">
                                <!-- Employee Information -->
                                <div class="form-section">
                                    <h3 class="form-section-title">Employee Information</h3>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required" for="first_name">First Name</label>
                                                <input type="text" 
                                                       name="first_name" 
                                                       id="first_name" 
                                                       class="form-control" 
                                                       placeholder="Enter first name" 
                                                       value="{{ old('first_name', $employee->first_name) }}"
                                                       required>
                                                @error('first_name')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label required" for="last_name">Last Name</label>
                                                <input type="text" 
                                                       name="last_name" 
                                                       id="last_name" 
                                                       class="form-control" 
                                                       placeholder="Enter last name" 
                                                       value="{{ old('last_name', $employee->last_name) }}"
                                                       required>
                                                @error('last_name')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required" for="email">Email</label>
                                                <input type="email" 
                                                       name="email" 
                                                       id="email" 
                                                       class="form-control" 
                                                       placeholder="Enter email" 
                                                       value="{{ old('email', $employee->email) }}"
                                                       required>
                                                @error('email')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="phone_number">Mobile Number</label>
                                                <div class="phone-input-container">
                                                    <div class="phone-country">
                                                        <select name="phone_country" class="phone-country-select">
                                                            @php
                                                                $countryCode = old('phone_country', $employee->phone_country ?? 'LB');
                                                            @endphp
                                                            <option value="LB" {{ $countryCode == 'LB' ? 'selected' : '' }}>LB (+961)</option>
                                                            <option value="US" {{ $countryCode == 'US' ? 'selected' : '' }}>US (+1)</option>
                                                            <option value="GB" {{ $countryCode == 'GB' ? 'selected' : '' }}>GB (+44)</option>
                                                            <option value="SA" {{ $countryCode == 'SA' ? 'selected' : '' }}>SA (+966)</option>
                                                            <option value="AE" {{ $countryCode == 'AE' ? 'selected' : '' }}>AE (+971)</option>
                                                        </select>
                                                        @php
                                                            $flagMap = [
                                                                'LB' => '🇱🇧',
                                                                'US' => '🇺🇸',
                                                                'GB' => '🇬🇧',
                                                                'SA' => '🇸🇦',
                                                                'AE' => '🇦🇪'
                                                            ];
                                                        @endphp
                                                        <span>{{ $flagMap[$countryCode] ?? '🇱🇧' }}</span>
                                                    </div>
                                                    <input type="tel" 
                                                           name="phone_number" 
                                                           id="phone_number" 
                                                           class="phone-input" 
                                                           placeholder="Enter mobile number" 
                                                           value="{{ old('phone_number', $employee->phone_number) }}">
                                                </div>
                                                @error('phone_number')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required" for="role_id">Role</label>
                                                <select name="role_id" 
                                                        id="role_id" 
                                                        class="form-control select2" 
                                                        required>
                                                    <option value="">Select role</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->role_id }}" 
                                                            {{ old('role_id', $employee->role_id) == $role->role_id ? 'selected' : '' }}>
                                                            {{ $role->role_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('role_id')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                            
                                </div>

                                <!-- Password Section -->
                                <div class="form-section">
                                    <h3 class="form-section-title">Account Security</h3>
                                    <div class="mb-3">
                                        <a href="#" class="password-change-toggle" id="togglePasswordChange">
                                            <i class="fas fa-key"></i> Change Password (Optional)
                                        </a>
                                    </div>
                                    
                                    <div id="passwordChangeSection" style="display: none;">
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <div class="mb-3 password-field">
                                                    <label class="form-label" for="password">New Password</label>
                                                    <input type="password" 
                                                           name="password" 
                                                           id="password" 
                                                           class="form-control" 
                                                           placeholder="Leave blank to keep current password">
                                                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @error('password')
                                                        <div class="error-message">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3 password-field">
                                                    <label class="form-label" for="password_confirmation">Confirm New Password</label>
                                                    <input type="password" 
                                                           name="password_confirmation" 
                                                           id="password_confirmation" 
                                                           class="form-control" 
                                                           placeholder="Confirm new password">
                                                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="alert alert-info">
                                            <small>
                                                <i class="fas fa-info-circle"></i> Password must be at least 8 characters long. 
                                                Leave both fields blank to keep the current password.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="form-section">
                                    <h3 class="form-section-title">Additional Information</h3>
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="start_date">Start Date</label>
                                                <input type="date" 
                                                       name="start_date" 
                                                       id="start_date" 
                                                       class="form-control" 
                                                       value="{{ old('start_date', $employee->start_date ? \Carbon\Carbon::parse($employee->start_date)->format('Y-m-d') : '') }}">
                                                @error('start_date')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="end_date">End Date</label>
                                                <input type="date" 
                                                       name="end_date" 
                                                       id="end_date" 
                                                       class="form-control" 
                                                       value="{{ old('end_date', $employee->end_date ? \Carbon\Carbon::parse($employee->end_date)->format('Y-m-d') : '') }}">
                                                @error('end_date')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label" for="project_ids">Project Assignments</label>
                                                <select name="project_ids[]" id="project_ids" class="form-control select2" multiple="multiple">
                                                    @php
                                                        $oldProjectIds = old('project_ids', $employee->projects->pluck('project_id')->toArray());
                                                    @endphp
                                                    
                                                    @foreach($projects as $project)
                                                        <option value="{{ $project->project_id }}" 
                                                            {{ in_array($project->project_id, $oldProjectIds) ? 'selected' : '' }}>
                                                            {{ $project->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="projects-info">
                                                    <i class="fas fa-info-circle"></i> Hold Ctrl/Cmd to select multiple projects
                                                </div>
                                                @error('project_ids')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                                @error('project_ids.*')
                                                    <div class="error-message">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Initialize Select2
        $(document).ready(function() {
            // Single select for role
            $('#role_id').select2({
                placeholder: "Select a role",
                allowClear: true
            });
            
            // Multiple select for projects
            $('#project_ids').select2({
                placeholder: "Select projects (optional)",
                allowClear: true,
                closeOnSelect: false,
                width: '100%',
                tags: false,
                maximumSelectionLength: 10
            });
        });

        // Password visibility toggle
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggleBtn = field.nextElementSibling;
            const icon = toggleBtn.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Toggle password change section
        document.getElementById('togglePasswordChange').addEventListener('click', function(e) {
            e.preventDefault();
            const section = document.getElementById('passwordChangeSection');
            const toggleIcon = this.querySelector('i');
            
            if (section.style.display === 'none' || section.style.display === '') {
                section.style.display = 'block';
                toggleIcon.classList.remove('fa-key');
                toggleIcon.classList.add('fa-times');
                this.innerHTML = '<i class="fas fa-times"></i> Cancel Password Change';
            } else {
                section.style.display = 'none';
                toggleIcon.classList.remove('fa-times');
                toggleIcon.classList.add('fa-key');
                this.innerHTML = '<i class="fas fa-key"></i> Change Password (Optional)';
                // Clear password fields when hiding
                document.getElementById('password').value = '';
                document.getElementById('password_confirmation').value = '';
            }
        });

        // Phone country code update
        const phoneCountrySelect = document.querySelector('.phone-country-select');
        const phoneCountryFlag = phoneCountrySelect.nextElementSibling;

        phoneCountrySelect.addEventListener('change', function() {
            const countryCode = this.value;
            const flagMap = {
                'LB': '🇱🇧',
                'US': '🇺🇸',
                'GB': '🇬🇧',
                'SA': '🇸🇦',
                'AE': '🇦🇪'
            };
            
            phoneCountryFlag.textContent = flagMap[countryCode] || '🇺🇳';
        });

        // Form validation
        document.getElementById('editEmployeeForm').addEventListener('submit', function(e) {
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;
            const roleId = document.getElementById('role_id').value;
            const projectIds = $('#project_ids').val();
            
            let isValid = true;
            let errorMessage = '';
            
            // Basic validation
            if (!firstName) {
                isValid = false;
                errorMessage = 'First name is required';
            } else if (!lastName) {
                isValid = false;
                errorMessage = 'Last name is required';
            } else if (!email) {
                isValid = false;
                errorMessage = 'Email is required';
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            } else if (!roleId) {
                isValid = false;
                errorMessage = 'Please select a role';
            }
            
            // Password validation (if password is being changed)
            const passwordSection = document.getElementById('passwordChangeSection');
            if (passwordSection.style.display === 'block') {
                if (password && password.length < 8) {
                    isValid = false;
                    errorMessage = 'Password must be at least 8 characters long';
                } else if (password && password !== passwordConfirm) {
                    isValid = false;
                    errorMessage = 'Passwords do not match';
                }
            }
            
            // Optional: Validate max projects
            if (projectIds && projectIds.length > 10) {
                isValid = false;
                errorMessage = 'Maximum 10 projects allowed per employee';
            }
            
            if (!isValid) {
                e.preventDefault();
                alert(errorMessage);
                return false;
            }
            
            return true;
        });

        // Confirmation for deactivate/activate
        function confirmDeactivate() {
            return confirm('Are you sure you want to deactivate this employee? They will no longer be able to access the system.');
        }

        function confirmActivate() {
            return confirm('Are you sure you want to activate this employee? They will regain access to the system.');
        }
    </script>
</body>
</html>