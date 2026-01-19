@extends('layouts.app')

@section('title', 'Employees Management - Add Employee')

@section('styles')
    <!-- Select2 for enhanced dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --border-color: #e5e7eb;
            --card-bg: #ffffff;
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
        
        .card-footer {
            padding: 1rem 1.5rem;
            background-color: #f9fafb;
            border-top: 1px solid var(--border-color);
            border-radius: 0 0 0.5rem 0.5rem;
            display: flex;
            justify-content: flex-end;
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
        
        .required::after {
            content: " *";
            color: #dc2626;
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
        
        @media (max-width: 768px) {
            .buttons-wrapper {
                flex-direction: column;
                width: 100%;
            }
            
            .buttons-wrapper .btn {
                width: 100%;
            }
            
            .card-footer {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>
@endsection

@section('content')
<div class="dashboard-content">
    <div class="form-container">
        <!-- Page Header -->
        <div class="d-flex flex-row w-100 justify-content-between mb-4">
            <h1 class="page-title">Add Employee</h1>
        </div>

        <!-- Form -->
        <form id="createEmployeeForm" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
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
                                    <label class="form-label" for="project_ids">Project Assignments</label>
                                    <select name="project_ids[]" id="project_ids" class="form-control select2" multiple="multiple">
                                        @php
                                            $oldProjectIds = old('project_ids', session('old_project_ids', []));
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
                
                <!-- FOOTER WITH BUTTONS -->
                <div class="card-footer">
                    <div class="buttons-wrapper">
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-primary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    </script>
@endsection