@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Users</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit User</li>
                        </ol>
                    </nav>
                    <h1 class="h2 fw-bold mb-1">Edit User</h1>
                    <p class="text-muted mb-0">Update user information for {{ $user->first_name }} {{ $user->last_name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mb-0 fw-semibold">User Information Form</h5>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-light text-dark">Required *</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle-fill me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Please correct the following errors:</h6>
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>
                                                @if(str_contains($error, 'User creation requires'))
                                                    Please fill in the required personal information fields marked with *
                                                @else
                                                    {{ $error }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <i class="bi bi-check-circle-fill me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Success!</h6>
                                    <p class="mb-0">{{ session('success') }}</p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('users.update', $user->user_id) }}" method="POST" class="needs-validation" novalidate id="editUserForm">
                        @csrf
                        @method('PUT')
                        
                        {{-- Personal Information --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Personal Information</h6>
                                <span class="text-muted small">Basic user details</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="first_name" class="form-label fw-semibold">
                                            First Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="first_name" id="first_name" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
                                               value="{{ old('first_name', $user->first_name) }}" 
                                               placeholder="Enter first name" required>
                                        @error('first_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="middle_name" class="form-label fw-semibold">Middle Name</label>
                                        <input type="text" name="middle_name" id="middle_name" 
                                               class="form-control @error('middle_name') is-invalid @enderror" 
                                               value="{{ old('middle_name', $user->middle_name) }}" 
                                               placeholder="Enter middle name">
                                        @error('middle_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="last_name" class="form-label fw-semibold">
                                            Last Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="last_name" id="last_name" 
                                               class="form-control @error('last_name') is-invalid @enderror" 
                                               value="{{ old('last_name', $user->last_name) }}" 
                                               placeholder="Enter last name" required>
                                        @error('last_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="mother_name" class="form-label fw-semibold">Mother's Name</label>
                                        <input type="text" name="mother_name" id="mother_name" 
                                               class="form-control @error('mother_name') is-invalid @enderror" 
                                               value="{{ old('mother_name', $user->mother_name) }}" 
                                               placeholder="Enter mother's name">
                                        @error('mother_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold">
                                            Email
                                        </label>
                                        <input type="email" name="email" id="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email', $user->email) }}"
                                               placeholder="Enter email address">
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="gender" class="form-label fw-semibold">Gender</label>
                                        <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                                        <input type="date" name="dob" id="dob" 
                                               class="form-control @error('dob') is-invalid @enderror" 
                                               value="{{ old('dob', $user->dob ? $user->dob->format('Y-m-d') : '') }}">
                                        @error('dob')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone_number" class="form-label fw-semibold">Phone Number</label>
                                        <input type="text" name="phone_number" id="phone_number" 
                                               class="form-control @error('phone_number') is-invalid @enderror" 
                                               value="{{ old('phone_number', $user->phone_number) }}" 
                                               placeholder="+961 00 000 000">
                                        @error('phone_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Nationalities Section --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Nationalities</h6>
                                <span class="text-muted small">Select one or more nationalities</span>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Select Nationalities</label>
                                        <div class="dropdown-multiselect">
                                            <button class="form-control dropdown-toggle text-start d-flex align-items-center justify-content-between" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="nationalitiesDropdown">
                                                <span class="selected-text text-truncate">
                                                    @php
                                                        $selectedNationalities = old('nationalities', $user->nationalities->pluck('nationality_id')->toArray());
                                                        $selectedNationalityNames = [];
                                                        foreach($nationalities as $nationality) {
                                                            if (in_array($nationality->nationality_id, $selectedNationalities)) {
                                                                $selectedNationalityNames[] = $nationality->name;
                                                            }
                                                        }
                                                        echo count($selectedNationalityNames) > 0 ? implode(', ', $selectedNationalityNames) : 'Select nationalities...';
                                                    @endphp
                                                </span>
                                                <i class="bi bi-chevron-down ms-2"></i>
                                            </button>
                                            <ul class="dropdown-menu w-100" id="nationalitiesList">
                                                @foreach($nationalities as $nationality)
                                                    <li class="dropdown-item p-0">
                                                        <div class="form-check m-2">
                                                            <input class="form-check-input nationality-checkbox" type="checkbox" 
                                                                   name="nationalities[]" 
                                                                   value="{{ $nationality->nationality_id }}" 
                                                                   id="nationality_{{ $nationality->nationality_id }}"
                                                                   {{ in_array($nationality->nationality_id, old('nationalities', $user->nationalities->pluck('nationality_id')->toArray())) ? 'checked' : '' }}>
                                                            <label class="form-check-label w-100" for="nationality_{{ $nationality->nationality_id }}">
                                                                {{ $nationality->name }}
                                                            </label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @error('nationalities')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        @error('nationalities.*')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Diplomas Section --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Educational Qualifications</h6>
                                <span class="text-muted small">Select one or more education levels</span>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Select Education Levels</label>
                                        <div class="dropdown-multiselect">
                                            <button class="form-control dropdown-toggle text-start d-flex align-items-center justify-content-between" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="diplomasDropdown">
                                                <span class="selected-text text-truncate">
                                                    @php
                                                        $selectedDiplomas = old('diplomas', $user->diplomas->pluck('diploma_id')->toArray());
                                                        $selectedDiplomaNames = [];
                                                        foreach($diplomas as $diploma) {
                                                            if (in_array($diploma->diploma_id, $selectedDiplomas)) {
                                                                $selectedDiplomaNames[] = $diploma->diploma_name;
                                                            }
                                                        }
                                                        echo count($selectedDiplomaNames) > 0 ? implode(', ', $selectedDiplomaNames) : 'Select education levels...';
                                                    @endphp
                                                </span>
                                                <i class="bi bi-chevron-down ms-2"></i>
                                            </button>
                                            <ul class="dropdown-menu w-100" id="diplomasList">
                                                @foreach($diplomas as $diploma)
                                                    <li class="dropdown-item p-0">
                                                        <div class="form-check m-2">
                                                            <input class="form-check-input diploma-checkbox" type="checkbox" 
                                                                   name="diplomas[]" 
                                                                   value="{{ $diploma->diploma_id }}" 
                                                                   id="diploma_{{ $diploma->diploma_id }}"
                                                                   {{ in_array($diploma->diploma_id, old('diplomas', $user->diplomas->pluck('diploma_id')->toArray())) ? 'checked' : '' }}>
                                                            <label class="form-check-label w-100" for="diploma_{{ $diploma->diploma_id }}">
                                                                {{ $diploma->diploma_name }}
                                                            </label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @error('diplomas')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        @error('diplomas.*')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status Details --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Status Details</h6>
                                <span class="text-muted small">Employment and marital information</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="marital_status" class="form-label fw-semibold">Marital Status</label>
                                        <select name="marital_status" id="marital_status" class="form-control @error('marital_status') is-invalid @enderror">
                                            <option value="">Select Marital Status</option>
                                            <option value="Single" {{ old('marital_status', $user->marital_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                            <option value="Married" {{ old('marital_status', $user->marital_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                            <option value="Divorced" {{ old('marital_status', $user->marital_status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                            <option value="Widowed" {{ old('marital_status', $user->marital_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                        </select>
                                        @error('marital_status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="employment_status" class="form-label fw-semibold">Employment Status</label>
                                        <select name="employment_status" id="employment_status" class="form-control @error('employment_status') is-invalid @enderror">
                                            <option value="">Select Employment Status</option>
                                            <option value="Employed" {{ old('employment_status', $user->employment_status) == 'Employed' ? 'selected' : '' }}>Employed</option>
                                            <option value="Unemployed" {{ old('employment_status', $user->employment_status) == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                                            <option value="Student" {{ old('employment_status', $user->employment_status) == 'Student' ? 'selected' : '' }}>Student</option>
                                            <option value="Retired" {{ old('employment_status', $user->employment_status) == 'Retired' ? 'selected' : '' }}>Retired</option>
                                            <option value="Self-Employed" {{ old('employment_status', $user->employment_status) == 'Self-Employed' ? 'selected' : '' }}>Self-Employed</option>
                                        </select>
                                        @error('employment_status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Identification Details --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Identification Details</h6>
                                <span class="text-muted small">Optional identification information</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="identification_id" class="form-label fw-semibold">National ID</label>
                                        <input type="text" name="identification_id" id="identification_id" 
                                               class="form-control @error('identification_id') is-invalid @enderror" 
                                               value="{{ old('identification_id', $user->identification_id) }}" 
                                               placeholder="Enter national ID number">
                                        @error('identification_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="passport_number" class="form-label fw-semibold">Passport Number</label>
                                        <input type="text" name="passport_number" id="passport_number" 
                                               class="form-control @error('passport_number') is-invalid @enderror" 
                                               value="{{ old('passport_number', $user->passport_number) }}" 
                                               placeholder="Enter passport number">
                                        @error('passport_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="register_number" class="form-label fw-semibold">Register Number</label>
                                        <input type="text" name="register_number" id="register_number" 
                                               class="form-control @error('register_number') is-invalid @enderror" 
                                               value="{{ old('register_number', $user->register_number) }}" 
                                               placeholder="Enter register number">
                                        @error('register_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="register_place" class="form-label fw-semibold">Register Place</label>
                                        <input type="text" name="register_place" id="register_place" 
                                               class="form-control @error('register_place') is-invalid @enderror" 
                                               value="{{ old('register_place', $user->register_place) }}" 
                                               placeholder="Enter register place">
                                        @error('register_place')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="card bg-light border-0">
                            <div class="card-body py-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Fields marked with <span class="text-danger">*</span> are required
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="reset" class="btn btn-outline-secondary me-2">
                                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Changes
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="bi bi-check-circle me-1"></i> Update User
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .section-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: visible;
        margin-bottom: 1.5rem;
        position: relative;
    }
    
    .section-header {
        background-color: #f8f9fa;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .section-body {
        padding: 1.5rem;
        position: relative;
        overflow: visible;
    }
    
    .form-label {
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .form-control {
        border-radius: 6px;
        border: 1px solid #ced4da;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
    
    .form-control:focus {
        border-color: #0a58ca;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .btn {
        border-radius: 6px;
        font-weight: 500;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }
    
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }
    
    /* Dropdown Multiselect Styles */
    .dropdown-multiselect {
        position: relative;
        width: 100%;
    }
    
    .dropdown-multiselect .dropdown-toggle {
        background: white;
        border: 1px solid #ced4da;
        padding: 0.5rem 0.75rem;
        text-align: left;
        width: 100%;
        position: relative;
        cursor: pointer;
    }
    
    .dropdown-multiselect .dropdown-toggle:after {
        display: none;
    }
    
    .dropdown-multiselect .dropdown-toggle i {
        font-size: 0.8rem;
        color: #6c757d;
        transition: transform 0.2s ease;
    }
    
    .dropdown-multiselect .dropdown-toggle.show i {
        transform: rotate(180deg);
    }
    
    .dropdown-multiselect .dropdown-menu {
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        padding: 0;
        margin: 0.125rem 0 0 0;
        z-index: 1060;
        position: absolute;
        top: 100%;
        left: 0;
        background: white;
    }
    
    .dropdown-multiselect .dropdown-menu.show {
        display: block;
        transform: none !important;
    }
    
    .dropdown-multiselect .dropdown-item {
        padding: 0;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .dropdown-multiselect .dropdown-item:last-child {
        border-bottom: none;
    }
    
    .dropdown-multiselect .form-check {
        margin: 0;
        padding: 0.5rem 1rem;
        width: 100%;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    
    .dropdown-multiselect .form-check:hover {
        background-color: #f8f9fa;
    }
    
    .dropdown-multiselect .form-check-input {
        margin-right: 0.5rem;
        margin-top: 0;
        flex-shrink: 0;
    }
    
    .dropdown-multiselect .form-check-label {
        cursor: pointer;
        width: 100%;
        font-weight: normal;
        margin-bottom: 0;
    }
    
    .selected-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editUserForm');
        const submitBtn = document.getElementById('submitBtn');
        
        // Form validation
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
            
            // Show loading state
            if (form.checkValidity()) {
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Updating User...';
                submitBtn.disabled = true;
            }
        });
        
        // Phone number formatting for Lebanon
        const phoneInput = document.getElementById('phone_number');
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.startsWith('961')) {
                value = value.substring(3);
            }
            
            if (value.length > 0) {
                let formatted = '+961 ';
                
                if (value.length <= 2) {
                    formatted += value;
                } else if (value.length <= 5) {
                    formatted += value.substring(0, 2) + ' ' + value.substring(2);
                } else {
                    formatted += value.substring(0, 2) + ' ' + value.substring(2, 5) + ' ' + value.substring(5, 8);
                }
                
                e.target.value = formatted;
            }
        });
        
        // Nationalities dropdown functionality
        const nationalitiesDropdown = document.getElementById('nationalitiesDropdown');
        const nationalityCheckboxes = document.querySelectorAll('.nationality-checkbox');
        
        // Update nationalities dropdown text
        function updateNationalitiesText() {
            const selectedNationalities = Array.from(nationalityCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.nextElementSibling.textContent.trim());
            
            const selectedText = selectedNationalities.length > 0 
                ? selectedNationalities.join(', ') 
                : 'Select nationalities...';
            
            nationalitiesDropdown.querySelector('.selected-text').textContent = selectedText;
        }
        
        // Add event listeners to nationality checkboxes
        nationalityCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateNationalitiesText);
        });
        
        // Diplomas dropdown functionality
        const diplomasDropdown = document.getElementById('diplomasDropdown');
        const diplomaCheckboxes = document.querySelectorAll('.diploma-checkbox');
        
        // Update diplomas dropdown text
        function updateDiplomasText() {
            const selectedDiplomas = Array.from(diplomaCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.nextElementSibling.textContent.trim());
            
            const selectedText = selectedDiplomas.length > 0 
                ? selectedDiplomas.join(', ') 
                : 'Select education levels...';
            
            diplomasDropdown.querySelector('.selected-text').textContent = selectedText;
        }
        
        // Add event listeners to diploma checkboxes
        diplomaCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateDiplomasText);
        });
        
        // Prevent dropdown from closing when clicking on checkboxes
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.addEventListener('click', function(e) {
                if (e.target.type === 'checkbox' || e.target.tagName === 'LABEL') {
                    e.stopPropagation();
                }
            });
        });
        
        // Handle dropdown show/hide for chevron rotation
        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('show.bs.dropdown', function() {
                this.classList.add('show');
            });
            
            toggle.addEventListener('hide.bs.dropdown', function() {
                this.classList.remove('show');
            });
        });
        
        // Initialize dropdown texts
        updateNationalitiesText();
        updateDiplomasText();
    });
</script>
@endsection