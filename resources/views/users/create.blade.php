@extends('layouts.app')

@section('title', 'Add New User')

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
                            <li class="breadcrumb-item active" aria-current="page">Add New User</li>
                        </ol>
                    </nav>
                    <h1 class="h2 fw-bold mb-1">Add New User</h1>
                    <p class="text-muted mb-0">Create a new user account in the system</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Users
                    </a>
                </div>
            </div>

            {{-- Progress Steps --}}
            <div class="card mb-4">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="step active">
                                    <div class="step-number">1</div>
                                    <div class="step-label">Personal Info</div>
                                </div>
                                <div class="step-connector"></div>
                                <div class="step">
                                    <div class="step-number">2</div>
                                    <div class="step-label">Status Details</div>
                                </div>
                                <div class="step-connector"></div>
                                <div class="step">
                                    <div class="step-number">3</div>
                                    <div class="step-label">Identification</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-clock me-1"></i> Takes 2 minutes
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-person-plus fs-4 me-2"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-0 fw-semibold">User Information Form</h5>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-light text-success">Required *</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
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

                    <form action="{{ route('users.store') }}" method="POST" class="needs-validation" novalidate id="userForm">
                        @csrf
                        
                        {{-- Personal Information --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <i class="bi bi-person-vcard me-2"></i>
                                <h6 class="mb-0 fw-semibold">Personal Information</h6>
                                <span class="text-muted small">Basic user details</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="first_name" class="form-label fw-semibold">
                                            First Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-person text-muted"></i>
                                            </span>
                                            <input type="text" name="first_name" id="first_name" 
                                                   class="form-control @error('first_name') is-invalid @enderror" 
                                                   value="{{ old('first_name') }}" 
                                                   placeholder="Enter first name" required>
                                        </div>
                                        @error('first_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="middle_name" class="form-label fw-semibold">Middle Name</label>
                                        <input type="text" name="middle_name" id="middle_name" 
                                               class="form-control @error('middle_name') is-invalid @enderror" 
                                               value="{{ old('middle_name') }}" 
                                               placeholder="Enter middle name (optional)">
                                        @error('middle_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="last_name" class="form-label fw-semibold">
                                            Last Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-person text-muted"></i>
                                            </span>
                                            <input type="text" name="last_name" id="last_name" 
                                                   class="form-control @error('last_name') is-invalid @enderror" 
                                                   value="{{ old('last_name') }}" 
                                                   placeholder="Enter last name" required>
                                        </div>
                                        @error('last_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="mother_name" class="form-label fw-semibold">Mother's Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-gender-female text-muted"></i>
                                            </span>
                                            <input type="text" name="mother_name" id="mother_name" 
                                                   class="form-control @error('mother_name') is-invalid @enderror" 
                                                   value="{{ old('mother_name') }}" 
                                                   placeholder="Enter mother's name">
                                        </div>
                                        @error('mother_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="gender" class="form-label fw-semibold">Gender</label>
                                        <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ old('gender')=='Male' ? 'selected':'' }}>Male</option>
                                            <option value="Female" {{ old('gender')=='Female' ? 'selected':'' }}>Female</option>
                                            <option value="Other" {{ old('gender')=='Other' ? 'selected':'' }}>Other</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-calendar text-muted"></i>
                                            </span>
                                            <input type="date" name="dob" id="dob" 
                                                   class="form-control @error('dob') is-invalid @enderror" 
                                                   value="{{ old('dob') }}">
                                        </div>
                                        @error('dob')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="phone_number" class="form-label fw-semibold">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-telephone text-muted"></i>
                                            </span>
                                            <input type="text" name="phone_number" id="phone_number" 
                                                   class="form-control @error('phone_number') is-invalid @enderror" 
                                                   value="{{ old('phone_number') }}" 
                                                  placeholder="+961 00 000 000">
                                        </div>
                                        @error('phone_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Status Details --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <i class="bi bi-briefcase me-2"></i>
                                <h6 class="mb-0 fw-semibold">Status Details</h6>
                                <span class="text-muted small">Employment and marital information</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="marital_status" class="form-label fw-semibold">Marital Status</label>
                                        <select name="marital_status" id="marital_status" class="form-select @error('marital_status') is-invalid @enderror">
                                            <option value="">Select Marital Status</option>
                                            <option value="Single" {{ old('marital_status')=='Single' ? 'selected':'' }}>Single</option>
                                            <option value="Married" {{ old('marital_status')=='Married' ? 'selected':'' }}>Married</option>
                                            <option value="Divorced" {{ old('marital_status')=='Divorced' ? 'selected':'' }}>Divorced</option>
                                            <option value="Widowed" {{ old('marital_status')=='Widowed' ? 'selected':'' }}>Widowed</option>
                                        </select>
                                        @error('marital_status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="employment_status" class="form-label fw-semibold">Employment Status</label>
                                        <select name="employment_status" id="employment_status" class="form-select @error('employment_status') is-invalid @enderror">
                                            <option value="">Select Employment Status</option>
                                            <option value="Employed" {{ old('employment_status')=='Employed' ? 'selected':'' }}>Employed</option>
                                            <option value="Unemployed" {{ old('employment_status')=='Unemployed' ? 'selected':'' }}>Unemployed</option>
                                            <option value="Student" {{ old('employment_status')=='Student' ? 'selected':'' }}>Student</option>
                                            <option value="Retired" {{ old('employment_status')=='Retired' ? 'selected':'' }}>Retired</option>
                                            <option value="Self-Employed" {{ old('employment_status')=='Self-Employed' ? 'selected':'' }}>Self-Employed</option>
                                        </select>
                                        @error('employment_status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="type" class="form-label fw-semibold">User Type</label>
                                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                                            <option value="Stakeholder" {{ old('type', 'Stakeholder')=='Stakeholder' ? 'selected':'' }}>Stakeholder</option>
                                            <option value="Beneficiary" {{ old('type')=='Beneficiary' ? 'selected':'' }}>Beneficiary</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Identification Details --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <i class="bi bi-fingerprint me-2"></i>
                                <h6 class="mb-0 fw-semibold">Identification Details</h6>
                                <span class="text-muted small">Optional identification information</span>
                            </div>
                            <div class="section-body">
                                <div class="alert alert-info mb-4">
                                    <div class="d-flex">
                                        <i class="bi bi-info-circle-fill me-2"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Identification Information</h6>
                                            <p class="mb-0">Provide at least one form of identification. All fields are optional but recommended for complete records.</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="identification_id" class="form-label fw-semibold">National ID</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-credit-card text-muted"></i>
                                            </span>
                                            <input type="text" name="identification_id" id="identification_id" 
                                                   class="form-control @error('identification_id') is-invalid @enderror" 
                                                   value="{{ old('identification_id') }}" 
                                                   placeholder="Enter national ID number">
                                        </div>
                                        @error('identification_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="passport_number" class="form-label fw-semibold">Passport Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-passport text-muted"></i>
                                            </span>
                                            <input type="text" name="passport_number" id="passport_number" 
                                                   class="form-control @error('passport_number') is-invalid @enderror" 
                                                   value="{{ old('passport_number') }}" 
                                                   placeholder="Enter passport number">
                                        </div>
                                        @error('passport_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="register_number" class="form-label fw-semibold">Register Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-journal-text text-muted"></i>
                                            </span>
                                            <input type="text" name="register_number" id="register_number" 
                                                   class="form-control @error('register_number') is-invalid @enderror" 
                                                   value="{{ old('register_number') }}" 
                                                   placeholder="Enter register number">
                                        </div>
                                        @error('register_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="register_place" class="form-label fw-semibold">Register Place</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-geo-alt text-muted"></i>
                                            </span>
                                            <input type="text" name="register_place" id="register_place" 
                                                   class="form-control @error('register_place') is-invalid @enderror" 
                                                   value="{{ old('register_place') }}" 
                                                   placeholder="Enter register place">
                                        </div>
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
                                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-success px-4" id="submitBtn">
                                            <i class="bi bi-person-plus me-1"></i> Create User
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
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .section-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .section-body {
        padding: 1.5rem;
    }
    
    .step {
        display: flex;
        align-items: center;
        flex-direction: column;
        position: relative;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-bottom: 0.5rem;
        border: 3px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .step.active .step-number {
        background: #4361ee;
        color: #fff;
    }
    
    .step-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #6c757d;
    }
    
    .step.active .step-label {
        color: #4361ee;
        font-weight: 600;
    }
    
    .step-connector {
        width: 60px;
        height: 2px;
        background: #e9ecef;
        margin: 0 1rem;
        margin-top: -20px;
    }
    
    .form-label {
        margin-bottom: 0.5rem;
    }
    
    .input-group-text {
        transition: all 0.3s ease;
    }
    
    .form-control:focus + .input-group-text,
    .form-select:focus + .input-group-text {
        border-color: #4361ee;
        background-color: #e7f1ff;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('userForm');
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
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Creating User...';
                submitBtn.disabled = true;
            }
        });
        
        // Real-time validation
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('blur', function() {
                this.classList.add('validated');
            });
        });
        
        // Phone number formatting
        const phoneInput = document.getElementById('phone_number');
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                value = '+1 (' + value.substring(0, 3) + ') ' + value.substring(3, 6) + '-' + value.substring(6, 10);
            }
            e.target.value = value;
        });
    });
</script>
@endsection,