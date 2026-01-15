<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Management - Add Employee</title>
    
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
            --toggle-off-bg: #d1d5db;
            --toggle-on-bg: #10b981;
            --toggle-knob: #ffffff;
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
        
        .toggle-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
        }
        
        .toggle-label {
            font-weight: 500;
            color: #4b5563;
            font-size: 0.875rem;
        }
        
        .toggle {
            width: 3.5rem;
            height: 2rem;
            background-color: var(--toggle-off-bg);
            border-radius: 1rem;
            position: relative;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .toggle-on {
            background-color: var(--toggle-on-bg);
        }
        
        .toggle-knob {
            position: absolute;
            top: 0.25rem;
            left: 0.25rem;
            width: 1.5rem;
            height: 1.5rem;
            background-color: var(--toggle-knob);
            border-radius: 50%;
            transition: transform 0.3s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .toggle-on .toggle-knob {
            transform: translateX(1.5rem);
        }
        
        .photo-upload-container {
            text-align: center;
        }
        
        .photo-preview {
            width: 128px;
            height: 128px;
            border: 2px dashed var(--border-color);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            background-color: #f9fafb;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .photo-preview:hover {
            border-color: var(--primary-color);
            background-color: #eff6ff;
        }
        
        .photo-preview-icon {
            color: var(--secondary-color);
            font-size: 2rem;
        }
        
        .photo-info {
            font-size: 0.75rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .photo-upload-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .photo-input {
            display: none;
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
        
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }
            
            .navigation-bar {
                width: 100%;
                height: auto;
            }
            
            .row {
                flex-direction: column;
            }
            
            .col {
                width: 100%;
                margin-bottom: 1rem;
            }
            
            .buttons-wrapper {
                flex-direction: column;
                width: 100%;
            }
            
            .buttons-wrapper .btn {
                width: 100%;
            }
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
                        <h1 class="page-title">Add Employee</h1>
                        <div class="buttons-wrapper">
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-primary">
                                Cancel
                            </a>
                            <button type="submit" form="createEmployeeForm" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </div>

                    <!-- Form -->
                    <form id="createEmployeeForm" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Left Column: Employee Information -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
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
                                                               value="{{ old('first_name') }}"
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
                                                               value="{{ old('last_name') }}"
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
                                                               value="{{ old('email') }}"
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
                                                                    <option value="LB">LB (+961)</option>
                                                                    <option value="US">US (+1)</option>
                                                                    <option value="GB">GB (+44)</option>
                                                                    <option value="SA">SA (+966)</option>
                                                                    <option value="AE">AE (+971)</option>
                                                                </select>
                                                                <span>🇱🇧</span>
                                                            </div>
                                                            <input type="tel" 
                                                                   name="phone_number" 
                                                                   id="phone_number" 
                                                                   class="phone-input" 
                                                                   placeholder="Enter mobile number" 
                                                                   value="{{ old('phone_number') }}">
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
                                                                <option value="{{ $role->role_id }}" {{ old('role_id') == $role->role_id ? 'selected' : '' }}>
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

                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="toggle-container">
                                                        <span class="toggle-label">Employee Type</span>
                                                        <div class="toggle" id="employeeTypeToggle">
                                                            <div class="toggle-knob"></div>
                                                        </div>
                                                        <input type="hidden" name="employee_type" id="employee_type" value="regular">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Password Section -->
                                        <div class="form-section">
                                            <h3 class="form-section-title">Account Security</h3>
                                            
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <div class="mb-3 password-field">
                                                        <label class="form-label required" for="password">Password</label>
                                                        <input type="password" 
                                                               name="password" 
                                                               id="password" 
                                                               class="form-control" 
                                                               placeholder="Enter password" 
                                                               required>
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
                                                        <label class="form-label required" for="password_confirmation">Confirm Password</label>
                                                        <input type="password" 
                                                               name="password_confirmation" 
                                                               id="password_confirmation" 
                                                               class="form-control" 
                                                               placeholder="Confirm password" 
                                                               required>
                                                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
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
                                                               value="{{ old('start_date') }}">
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
                                                               value="{{ old('end_date') }}">
                                                        @error('end_date')
                                                            <div class="error-message">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label" for="project_id">Project Assignment</label>
                                                        <select name="project_id" 
                                                                id="project_id" 
                                                                class="form-control select2">
                                                            <option value="">Select project (optional)</option>
                                                            @if(isset($projects) && $projects->count() > 0)
                                                                @foreach($projects as $project)
                                                                    <option value="{{ $project->project_id }}" {{ old('project_id') == $project->project_id ? 'selected' : '' }}>
                                                                        {{ $project->name }}
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('project_id')
                                                            <div class="error-message">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Photo & Status -->
                            <div class="col-md-4">
                                <!-- Photo Upload -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="photo-upload-container">
                                            <label class="form-label">Photo</label>
                                            <div class="photo-preview" id="photoPreview" onclick="document.getElementById('photo').click()">
                                                <i class="fas fa-plus photo-preview-icon"></i>
                                            </div>
                                            <div class="photo-info">
                                                Aspect Ratio: 1:1 • JPG or JPEG or WEBP or SVG • Maximum 5MB
                                            </div>
                                            <button type="button" class="btn btn-outline-primary photo-upload-btn" onclick="document.getElementById('photo').click()">
                                                <i class="fas fa-upload"></i> Upload Image
                                            </button>
                                            <input type="file" 
                                                   name="photo" 
                                                   id="photo" 
                                                   class="photo-input" 
                                                   accept=".jpg,.jpeg,.webp,.svg"
                                                   onchange="previewImage(event)">
                                            @error('photo')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Account Status -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="toggle-container">
                                            <span class="toggle-label">Active Account</span>
                                            <div class="toggle toggle-on" id="statusToggle">
                                                <div class="toggle-knob"></div>
                                            </div>
                                            <input type="hidden" name="is_active" id="is_active" value="1">
                                        </div>
                                        <p class="text-muted small mt-2">User can log in if active</p>
                                    </div>
                                </div>

                                <!-- Email Notification -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   name="send_welcome_email" 
                                                   id="send_welcome_email" 
                                                   class="form-check-input" 
                                                   value="1" 
                                                   checked>
                                            <label class="form-check-label" for="send_welcome_email">
                                                Send welcome email with login instructions
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Role Permissions Preview -->
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="form-label mb-3">Role Permissions Preview</h6>
                                        <div id="rolePermissions" class="small text-muted">
                                            Select a role to see permissions...
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
            $('.select2').select2({
                placeholder: "Select an option",
                allowClear: true
            });
        });

        // Toggle functionality
        const employeeTypeToggle = document.getElementById('employeeTypeToggle');
        const employeeTypeInput = document.getElementById('employee_type');
        const statusToggle = document.getElementById('statusToggle');
        const statusInput = document.getElementById('is_active');

        employeeTypeToggle.addEventListener('click', function() {
            this.classList.toggle('toggle-on');
            employeeTypeInput.value = this.classList.contains('toggle-on') ? 'expert' : 'regular';
        });

        statusToggle.addEventListener('click', function() {
            this.classList.toggle('toggle-on');
            statusInput.value = this.classList.contains('toggle-on') ? '1' : '0';
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

        // Image preview
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('photoPreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.375rem;">`;
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Role permissions preview
        const roleSelect = document.getElementById('role_id');
        const permissionsDiv = document.getElementById('rolePermissions');

        roleSelect.addEventListener('change', function() {
            const roleId = this.value;
            
            if (roleId) {
                // Fetch role permissions via AJAX (you'll need to implement this endpoint)
                fetch(`/api/roles/${roleId}/permissions`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.permissions && data.permissions.length > 0) {
                            let html = '<ul class="list-unstyled mb-0">';
                            data.permissions.forEach(perm => {
                                html += `<li class="mb-1">
                                            <i class="fas fa-check text-success me-2"></i>
                                            ${perm}
                                        </li>`;
                            });
                            html += '</ul>';
                            permissionsDiv.innerHTML = html;
                        } else {
                            permissionsDiv.innerHTML = '<p class="text-muted mb-0">No specific permissions defined for this role.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching permissions:', error);
                        permissionsDiv.innerHTML = '<p class="text-muted mb-0">Unable to load permissions.</p>';
                    });
            } else {
                permissionsDiv.innerHTML = '<p class="text-muted mb-0">Select a role to see permissions...</p>';
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
        document.getElementById('createEmployeeForm').addEventListener('submit', function(e) {
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;
            const roleId = document.getElementById('role_id').value;
            
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
            } else if (!password) {
                isValid = false;
                errorMessage = 'Password is required';
            } else if (password.length < 8) {
                isValid = false;
                errorMessage = 'Password must be at least 8 characters long';
            } else if (password !== passwordConfirm) {
                isValid = false;
                errorMessage = 'Passwords do not match';
            } else if (!roleId) {
                isValid = false;
                errorMessage = 'Please select a role';
            }
            
            if (!isValid) {
                e.preventDefault();
                alert(errorMessage);
                return false;
            }
            
            return true;
        });

        // Auto-generate external ID (for display only)
        document.getElementById('first_name').addEventListener('blur', function() {
            const firstName = this.value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            
            if (firstName && lastName) {
                const year = new Date().getFullYear();
                const month = String(new Date().getMonth() + 1).padStart(2, '0');
                const initials = firstName.charAt(0) + lastName.charAt(0);
                
                // This is just for display, the actual ID will be generated in the model
                console.log(`Suggested ID: EMP_${year}_${month}_${initials.toUpperCase()}`);
            }
        });
    </script>
</body>
</html>