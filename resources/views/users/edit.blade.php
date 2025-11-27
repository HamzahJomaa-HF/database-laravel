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
                    <div class="d-flex align-items-center">
                        <h1 class="h2 fw-bold mb-1 me-3">Edit User</h1>
                        <span class="badge bg-{{ $user->type === 'Stakeholder' ? 'primary' : 'success' }} fs-6">
                            {{ $user->type }}
                        </span>
                    </div>
                    <p class="text-muted mb-0">Update user information for {{ $user->first_name }} {{ $user->last_name }}</p>
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
                                <i class="bi bi-clock me-1"></i> Last updated: {{ $user->updated_at->format('M d, Y') }}
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
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-person-gear fs-4 me-2"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-0 fw-semibold">Edit User Information</h5>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-light text-primary">ID: {{ $user->user_id }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    {{-- User Summary --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body py-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-placeholder bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <span class="text-white fw-bold fs-6">
                                                            {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1 fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</h6>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @if($user->phone_number)
                                                            <span class="text-muted small">
                                                                <i class="bi bi-telephone me-1"></i>{{ $user->phone_number }}
                                                            </span>
                                                        @endif
                                                        @if($user->gender)
                                                            <span class="text-muted small">
                                                                <i class="bi bi-gender-{{ strtolower($user->gender) }} me-1"></i>{{ $user->gender }}
                                                            </span>
                                                        @endif
                                                        @if($user->dob)
                                                            <span class="text-muted small">
                                                                <i class="bi bi-calendar me-1"></i>{{ $user->dob->format('M d, Y') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="text-muted small">
                                                Created: {{ $user->created_at->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Please correct the following errors:</h6>
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
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
                                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
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
                                                   value="{{ old('first_name', $user->first_name) }}" 
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
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-person text-muted"></i>
                                            </span>
                                            <input type="text" name="last_name" id="last_name" 
                                                   class="form-control @error('last_name') is-invalid @enderror" 
                                                   value="{{ old('last_name', $user->last_name) }}" 
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
                                                   value="{{ old('mother_name', $user->mother_name) }}" 
                                                   placeholder="Enter mother's name">
                                        </div>
                                        @error('mother_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label fw-semibold">
                                            Email <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-envelope-at text-muted"></i>
                                            </span>
                                            <input type="email" name="email" id="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   value="{{ old('email', $user->email) }}"
                                                   placeholder="Enter email address" required>
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="gender" class="form-label fw-semibold">Gender</label>
                                        <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ old('gender', $user->gender)=='Male' ? 'selected':'' }}>Male</option>
                                            <option value="Female" {{ old('gender', $user->gender)=='Female' ? 'selected':'' }}>Female</option>
                                            <option value="Other" {{ old('gender', $user->gender)=='Other' ? 'selected':'' }}>Other</option>
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
                                                   value="{{ old('dob', $user->dob?->format('Y-m-d')) }}">
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
                                                   value="{{ old('phone_number', $user->phone_number) }}" 
                                                   placeholder="+961 00 000 000">
                                        </div>
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
                                <i class="bi bi-globe me-2"></i>
                                <h6 class="mb-0 fw-semibold">Nationalities</h6>
                                <span class="text-muted small">Select one or more nationalities</span>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Select Nationalities</label>
                                        <div class="multi-select-container">
                                            <select name="nationalities[]" id="nationalities" class="form-select multi-select" multiple size="5">
                                                @foreach($nationalities as $nationality)
                                                    <option value="{{ $nationality->nationality_id }}" 
                                                        {{ in_array($nationality->nationality_id, old('nationalities', $user->nationalities->pluck('nationality_id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $nationality->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="multi-select-info mt-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Hold <kbd>Ctrl</kbd> (Windows) or <kbd>Cmd</kbd> (Mac) to select multiple options
                                                </small>
                                            </div>
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
                                <i class="bi bi-mortarboard me-2"></i>
                                <h6 class="mb-0 fw-semibold">Educational Qualifications</h6>
                                <span class="text-muted small">Select one or more education levels</span>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Select Education Levels</label>
                                        <div class="multi-select-container">
                                            <select name="diplomas[]" id="diplomas" class="form-select multi-select" multiple size="5">
                                                @foreach($diplomas as $diploma)
                                                    <option value="{{ $diploma->diploma_id }}" 
                                                        {{ in_array($diploma->diploma_id, old('diplomas', $user->diplomas->pluck('diploma_id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $diploma->diploma_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="multi-select-info mt-2">
                                                <small class="text-muted">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Hold <kbd>Ctrl</kbd> (Windows) or <kbd>Cmd</kbd> (Mac) to select multiple options
                                                </small>
                                            </div>
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
                                <i class="bi bi-briefcase me-2"></i>
                                <h6 class="mb-0 fw-semibold">Status Details</h6>
                                <span class="text-muted small">Employment and user type information</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="marital_status" class="form-label fw-semibold">Marital Status</label>
                                        <select name="marital_status" id="marital_status" class="form-select @error('marital_status') is-invalid @enderror">
                                            <option value="">Select Marital Status</option>
                                            <option value="Single" {{ old('marital_status', $user->marital_status)=='Single' ? 'selected':'' }}>Single</option>
                                            <option value="Married" {{ old('marital_status', $user->marital_status)=='Married' ? 'selected':'' }}>Married</option>
                                            <option value="Divorced" {{ old('marital_status', $user->marital_status)=='Divorced' ? 'selected':'' }}>Divorced</option>
                                            <option value="Widowed" {{ old('marital_status', $user->marital_status)=='Widowed' ? 'selected':'' }}>Widowed</option>
                                            <option value="Separated" {{ old('marital_status', $user->marital_status)=='Separated' ? 'selected':'' }}>Separated</option>
                                        </select>
                                        @error('marital_status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="employment_status" class="form-label fw-semibold">Employment Status</label>
                                        <select name="employment_status" id="employment_status" class="form-select @error('employment_status') is-invalid @enderror">
                                            <option value="">Select Employment Status</option>
                                            <option value="Employed" {{ old('employment_status', $user->employment_status)=='Employed' ? 'selected':'' }}>Employed</option>
                                            <option value="Unemployed" {{ old('employment_status', $user->employment_status)=='Unemployed' ? 'selected':'' }}>Unemployed</option>
                                            <option value="Student" {{ old('employment_status', $user->employment_status)=='Student' ? 'selected':'' }}>Student</option>
                                            <option value="Retired" {{ old('employment_status', $user->employment_status)=='Retired' ? 'selected':'' }}>Retired</option>
                                            <option value="Self-Employed" {{ old('employment_status', $user->employment_status)=='Self-Employed' ? 'selected':'' }}>Self-Employed</option>
                                        </select>
                                        @error('employment_status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="type" class="form-label fw-semibold">User Type</label>
                                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                                            <option value="Stakeholder" {{ old('type', $user->type)=='Stakeholder' ? 'selected':'' }}>Stakeholder</option>
                                            <option value="Beneficiary" {{ old('type', $user->type)=='Beneficiary' ? 'selected':'' }}>Beneficiary</option>
                                            <option value="Employee" {{ old('type', $user->type)=='Employee' ? 'selected':'' }}>Employee</option>
                                            <option value="Admin" {{ old('type', $user->type)=='Admin' ? 'selected':'' }}>Administrator</option>
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
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="identification_id" class="form-label fw-semibold">National ID</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-credit-card text-muted"></i>
                                            </span>
                                            <input type="text" name="identification_id" id="identification_id" 
                                                   class="form-control @error('identification_id') is-invalid @enderror" 
                                                   value="{{ old('identification_id', $user->identification_id) }}" 
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
                                                   value="{{ old('passport_number', $user->passport_number) }}" 
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
                                                   value="{{ old('register_number', $user->register_number) }}" 
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
                                                   value="{{ old('register_place', $user->register_place) }}" 
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
                                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Changes
                                        </button>
                                        <button type="submit" class="btn btn-primary px-4" id="submitBtn">
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

    .avatar-placeholder {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Multi-select styling */
    .multi-select-container {
        position: relative;
    }

    .multi-select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.5rem;
        transition: all 0.3s ease;
        background: white;
    }

    .multi-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }

    .multi-select option {
        padding: 0.5rem 0.75rem;
        margin: 2px 0;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .multi-select option:hover {
        background-color: #4361ee !important;
        color: white;
    }

    .multi-select option:checked {
        background-color: #4361ee;
        color: white;
        font-weight: 600;
    }

    .multi-select-info {
        background: #f8f9fa;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        border-left: 3px solid #4361ee;
    }

    .multi-select-info kbd {
        background: #4361ee;
        color: white;
        padding: 0.1rem 0.3rem;
        border-radius: 3px;
        font-size: 0.75rem;
    }

    /* Selected items counter */
    .selected-counter {
        position: absolute;
        top: -8px;
        right: 10px;
        background: #4361ee;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
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
        
        // Real-time validation
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('blur', function() {
                this.classList.add('validated');
            });
        });
        
        // Lebanese phone number formatting (optional)
        const phoneInput = document.getElementById('phone_number');
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            // If user starts with 961, format as Lebanese number
            if (value.startsWith('961')) {
                value = value.substring(3); // Remove 961
                if (value.length > 0) {
                    value = '+961 ' + value.substring(0, 2) + ' ' + value.substring(2, 5) + ' ' + value.substring(5, 8);
                }
            }
            // If user starts with numbers directly, assume it's Lebanese mobile
            else if (value.length > 0 && !value.startsWith('1')) {
                value = '+961 ' + value.substring(0, 2) + ' ' + value.substring(2, 5) + ' ' + value.substring(5, 8);
            }
            
            e.target.value = value;
        });

        // Enhanced multi-select functionality
        const nationalitiesSelect = document.getElementById('nationalities');
        const diplomasSelect = document.getElementById('diplomas');

        // Add visual feedback for multi-select
        function enhanceMultiSelect(selectElement) {
            // Create selected counter
            const counter = document.createElement('div');
            counter.className = 'selected-counter';
            updateCounter(selectElement, counter);
            
            selectElement.parentNode.style.position = 'relative';
            selectElement.parentNode.appendChild(counter);
            
            // Update counter on change
            selectElement.addEventListener('change', function() {
                updateCounter(this, counter);
            });
            
            // Add hover effects
            selectElement.addEventListener('mouseenter', function() {
                this.style.borderColor = '#4361ee';
            });
            
            selectElement.addEventListener('mouseleave', function() {
                if (!this.matches(':focus')) {
                    this.style.borderColor = '#e9ecef';
                }
            });
        }

        function updateCounter(selectElement, counter) {
            const selectedCount = Array.from(selectElement.selectedOptions).length;
            counter.textContent = selectedCount;
            counter.style.display = selectedCount > 0 ? 'flex' : 'none';
        }

        // Initialize enhanced multi-selects
        enhanceMultiSelect(nationalitiesSelect);
        enhanceMultiSelect(diplomasSelect);

        // Show changes made
        const originalFormData = new FormData(form);
        form.addEventListener('input', function() {
            const currentFormData = new FormData(form);
            let hasChanges = false;
            
            for (let [key, value] of originalFormData.entries()) {
                if (currentFormData.get(key) !== value) {
                    hasChanges = true;
                    break;
                }
            }
            
            if (hasChanges) {
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save Changes';
            } else {
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Update User';
            }
        });
    });
</script>
@endsection