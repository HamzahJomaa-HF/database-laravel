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
                    <p class="text-muted mb-0">Update professional contact information</p>
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
                            <h5 class="mb-0 fw-semibold">Professional Contact Information Form</h5>
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
                                <i class="bi bi-check-circle-fill me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">Success!</h6>
                                    <p class="mb-0">{{ session('success') }}</p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('users.update', $user->user_id) }}" method="POST" class="needs-validation" novalidate id="userForm">
                        @csrf
                        @method('PUT')
                        
                        {{-- Hidden field for user type --}}
                        <input type="hidden" name="type" value="Stakeholder">
                        
                        {{-- ============================ --}}
                        {{-- SECTION 1: BASIC INFORMATION --}}
                        {{-- ============================ --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Basic Information</h6>
                                <span class="text-muted small">Personal and contact details</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    {{-- Row 1 --}}
                                    <div class="col-md-3">
                                        <label for="first_name" class="form-label fw-semibold">
                                            First Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="first_name" id="first_name" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
                                               value="{{ old('first_name', $user->first_name) }}" 
                                               placeholder="First Name" required>
                                        @error('first_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="middle_name" class="form-label fw-semibold">Middle Name</label>
                                        <input type="text" name="middle_name" id="middle_name" 
                                               class="form-control @error('middle_name') is-invalid @enderror" 
                                               value="{{ old('middle_name', $user->middle_name) }}" 
                                               placeholder="Middle Name">
                                        @error('middle_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label for="mother_name" class="form-label fw-semibold">Mother's Name</label>
                                        <input type="text" name="mother_name" id="mother_name" 
                                               class="form-control @error('mother_name') is-invalid @enderror" 
                                               value="{{ old('mother_name', $user->mother_name) }}" 
                                               placeholder="Mother's Name">
                                        @error('mother_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="last_name" class="form-label fw-semibold">
                                            Last Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="last_name" id="last_name" 
                                               class="form-control @error('last_name') is-invalid @enderror" 
                                               value="{{ old('last_name', $user->last_name) }}" 
                                               placeholder="Last Name" required>
                                        @error('last_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Row 2 --}}
                                    <div class="col-md-3">
                                        <label for="gender" class="form-label fw-semibold">
                                            Gender <span class="text-danger">*</span>
                                        </label>
                                        <select name="gender" id="gender" class="form-control form-select @error('gender') is-invalid @enderror" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                                        <input type="date" name="dob" id="dob" 
                                               class="form-control @error('dob') is-invalid @enderror" 
                                               value="{{ old('dob', $user->dob) }}">
                                        @error('dob')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="email" class="form-label fw-semibold">Email</label>
                                        <input type="email" name="email" id="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email', $user->email) }}"
                                               placeholder="Email">
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="mobile_phone" class="form-label fw-semibold">
                                            Mobile Phone <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="mobile_phone" id="mobile_phone" 
                                               class="form-control @error('mobile_phone') is-invalid @enderror" 
                                               value="{{ old('mobile_phone', $user->mobile_phone) }}" 
                                               placeholder="Mobile Phone" required>
                                        @error('mobile_phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Row 3 --}}
                                    <div class="col-md-3">
                                        <label for="prefix" class="form-label fw-semibold">Prefix</label>
                                        <select name="prefix" id="prefix" class="form-control form-select @error('prefix') is-invalid @enderror">
                                            <option value="">Select Prefix</option>
                                            <option value="Dr." {{ old('prefix', $user->prefix) == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                            <option value="Mr." {{ old('prefix', $user->prefix) == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                            <option value="Ms." {{ old('prefix', $user->prefix) == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                                            <option value="Mrs." {{ old('prefix', $user->prefix) == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                            <option value="Prof." {{ old('prefix', $user->prefix) == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                                        </select>
                                        @error('prefix')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label for="address" class="form-label fw-semibold">
                                            Address <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="address" id="address" 
                                               class="form-control @error('address') is-invalid @enderror" 
                                               value="{{ old('address', $user->address) }}" 
                                               placeholder="Address" required>
                                        @error('address')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================================== --}}
                        {{-- SECTION 2: PROFESSIONAL INFORMATION --}}
                        {{-- ================================== --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Professional Information</h6>
                                <span class="text-muted small">Organization and position details</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="is_high_profile" class="form-label fw-semibold">
                                            High Profile <span class="text-danger">*</span>
                                        </label>
                                        <select name="is_high_profile" id="is_high_profile" class="form-control form-select @error('is_high_profile') is-invalid @enderror" required>
                                            <option value="0" {{ old('is_high_profile', $user->is_high_profile) == '0' ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('is_high_profile', $user->is_high_profile) == '1' ? 'selected' : '' }}>Yes</option>
                                        </select>
                                        @error('is_high_profile')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="scope" class="form-label fw-semibold">
                                            Scope <span class="text-danger">*</span>
                                        </label>
                                        <select name="scope" id="scope" class="form-control form-select @error('scope') is-invalid @enderror" required>
                                            <option value="">Select Scope</option>
                                            <option value="International" {{ old('scope', $user->scope) == 'International' ? 'selected' : '' }}>International</option>
                                            <option value="Regional" {{ old('scope', $user->scope) == 'Regional' ? 'selected' : '' }}>Regional</option>
                                            <option value="National" {{ old('scope', $user->scope) == 'National' ? 'selected' : '' }}>National</option>
                                            <option value="Local" {{ old('scope', $user->scope) == 'Local' ? 'selected' : '' }}>Local</option>
                                        </select>
                                        @error('scope')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="community_of_practice" class="form-label fw-semibold">
                                            Community of Practice <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="community_of_practice" id="community_of_practice" 
                                               class="form-control @error('community_of_practice') is-invalid @enderror" 
                                               value="{{ old('community_of_practice', $user->community_of_practice) }}" 
                                               placeholder="Community of Practice" required>
                                        @error('community_of_practice')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="sector" class="form-label fw-semibold">Sector</label>
                                        <input type="text" name="sector" id="sector" 
                                               class="form-control @error('sector') is-invalid @enderror" 
                                               value="{{ old('sector', $user->sector) }}" 
                                               placeholder="Sector">
                                        @error('sector')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================ --}}
                        {{-- SECTION 3: PRIMARY POSITION --}}
                        {{-- ============================ --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Primary Position</h6>
                                <span class="text-muted small">Main professional role</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    {{-- Row 1 --}}
                                    <div class="col-md-3">
                                        <label for="position_1" class="form-label fw-semibold">
                                            Position <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="position_1" id="position_1" 
                                               class="form-control @error('position_1') is-invalid @enderror" 
                                               value="{{ old('position_1', $user->position_1) }}" 
                                               placeholder="Position" required>
                                        @error('position_1')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="organization_1" class="form-label fw-semibold">
                                            Organization <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="organization_1" id="organization_1" 
                                               class="form-control @error('organization_1') is-invalid @enderror" 
                                               value="{{ old('organization_1', $user->organization_1) }}" 
                                               placeholder="Organization" required>
                                        @error('organization_1')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="organization_type_1" class="form-label fw-semibold">
                                            Organization Type <span class="text-danger">*</span>
                                        </label>
                                        <select name="organization_type_1" id="organization_type_1" class="form-control form-select @error('organization_type_1') is-invalid @enderror" required>
                                            <option value="">Select Organization Type</option>
                                            <option value="Public Sector" {{ old('organization_type_1', $user->organization_type_1) == 'Public Sector' ? 'selected' : '' }}>Public Sector</option>
                                            <option value="Private Sector" {{ old('organization_type_1', $user->organization_type_1) == 'Private Sector' ? 'selected' : '' }}>Private Sector</option>
                                            <option value="Academia" {{ old('organization_type_1', $user->organization_type_1) == 'Academia' ? 'selected' : '' }}>Academia</option>
                                            <option value="UN" {{ old('organization_type_1', $user->organization_type_1) == 'UN' ? 'selected' : '' }}>UN</option>
                                            <option value="INGOs" {{ old('organization_type_1', $user->organization_type_1) == 'INGOs' ? 'selected' : '' }}>INGOs</option>
                                            <option value="Civil Society" {{ old('organization_type_1', $user->organization_type_1) == 'Civil Society' ? 'selected' : '' }}>Civil Society</option>
                                            <option value="NGOs" {{ old('organization_type_1', $user->organization_type_1) == 'NGOs' ? 'selected' : '' }}>NGOs</option>
                                            <option value="Activist" {{ old('organization_type_1', $user->organization_type_1) == 'Activist' ? 'selected' : '' }}>Activist</option>
                                        </select>
                                        @error('organization_type_1')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Row 2 --}}
                                    <div class="col-md-3">
                                        <label for="status_1" class="form-label fw-semibold">
                                            Status <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="status_1" id="status_1" 
                                               class="form-control @error('status_1') is-invalid @enderror" 
                                               value="{{ old('status_1', $user->status_1) }}" 
                                               placeholder="Status" required>
                                        @error('status_1')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="office_phone" class="form-label fw-semibold">Office Phone</label>
                                        <input type="text" name="office_phone" id="office_phone" 
                                               class="form-control @error('office_phone') is-invalid @enderror" 
                                               value="{{ old('office_phone', $user->office_phone) }}" 
                                               placeholder="Office Phone">
                                        @error('office_phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="extension_number" class="form-label fw-semibold">Extension</label>
                                        <input type="text" name="extension_number" id="extension_number" 
                                               class="form-control @error('extension_number') is-invalid @enderror" 
                                               value="{{ old('extension_number', $user->extension_number) }}" 
                                               placeholder="Extension">
                                        @error('extension_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label for="home_phone_1" class="form-label fw-semibold">Home Phone</label>
                                        <input type="text" name="home_phone_1" id="home_phone_1" 
                                               class="form-control @error('home_phone_1') is-invalid @enderror" 
                                               value="{{ old('home_phone_1', $user->home_phone_1) }}" 
                                               placeholder="Home Phone">
                                        @error('home_phone_1')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ======================================== --}}
                        {{-- SECTION 4: SECONDARY POSITION (Optional) --}}
                        {{-- ======================================== --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Secondary Position (Optional)</h6>
                                <span class="text-muted small">Additional professional role</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    {{-- Row 1 --}}
                                    <div class="col-md-3">
                                        <label for="position_2" class="form-label fw-semibold">Position</label>
                                        <input type="text" name="position_2" id="position_2" 
                                               class="form-control @error('position_2') is-invalid @enderror" 
                                               value="{{ old('position_2', $user->position_2) }}" 
                                               placeholder="Position">
                                        @error('position_2')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="organization_2" class="form-label fw-semibold">Organization</label>
                                        <input type="text" name="organization_2" id="organization_2" 
                                               class="form-control @error('organization_2') is-invalid @enderror" 
                                               value="{{ old('organization_2', $user->organization_2) }}" 
                                               placeholder="Organization">
                                        @error('organization_2')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="organization_type_2" class="form-label fw-semibold">Organization Type</label>
                                        <select name="organization_type_2" id="organization_type_2" class="form-control form-select @error('organization_type_2') is-invalid @enderror">
                                            <option value="">Select Organization Type</option>
                                            <option value="Public Sector" {{ old('organization_type_2', $user->organization_type_2) == 'Public Sector' ? 'selected' : '' }}>Public Sector</option>
                                            <option value="Private Sector" {{ old('organization_type_2', $user->organization_type_2) == 'Private Sector' ? 'selected' : '' }}>Private Sector</option>
                                            <option value="Academia" {{ old('organization_type_2', $user->organization_type_2) == 'Academia' ? 'selected' : '' }}>Academia</option>
                                            <option value="UN" {{ old('organization_type_2', $user->organization_type_2) == 'UN' ? 'selected' : '' }}>UN</option>
                                            <option value="INGOs" {{ old('organization_type_2', $user->organization_type_2) == 'INGOs' ? 'selected' : '' }}>INGOs</option>
                                            <option value="Civil Society" {{ old('organization_type_2', $user->organization_type_2) == 'Civil Society' ? 'selected' : '' }}>Civil Society</option>
                                            <option value="NGOs" {{ old('organization_type_2', $user->organization_type_2) == 'NGOs' ? 'selected' : '' }}>NGOs</option>
                                            <option value="Activist" {{ old('organization_type_2', $user->organization_type_2) == 'Activist' ? 'selected' : '' }}>Activist</option>
                                        </select>
                                        @error('organization_type_2')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Row 2 --}}
                                    <div class="col-md-3">
                                        <label for="status_2" class="form-label fw-semibold">Status</label>
                                        <input type="text" name="status_2" id="status_2" 
                                               class="form-control @error('status_2') is-invalid @enderror" 
                                               value="{{ old('status_2', $user->status_2) }}" 
                                               placeholder="Status">
                                        @error('status_2')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="office_phone_2" class="form-label fw-semibold">Office Phone</label>
                                        <input type="text" name="office_phone_2" id="office_phone_2" 
                                               class="form-control @error('office_phone_2') is-invalid @enderror" 
                                               value="{{ old('office_phone_2', $user->office_phone_2) }}" 
                                               placeholder="Office Phone">
                                        @error('office_phone_2')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="extension_number_2" class="form-label fw-semibold">Extension</label>
                                        <input type="text" name="extension_number_2" id="extension_number_2" 
                                               class="form-control @error('extension_number_2') is-invalid @enderror" 
                                               value="{{ old('extension_number_2', $user->extension_number_2) }}" 
                                               placeholder="Extension">
                                        @error('extension_number_2')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label for="home_phone" class="form-label fw-semibold">Home Phone</label>
                                        <input type="text" name="home_phone" id="home_phone" 
                                               class="form-control @error('home_phone') is-invalid @enderror" 
                                               value="{{ old('home_phone', $user->home_phone) }}" 
                                               placeholder="Home Phone">
                                        @error('home_phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ==================================== --}}
                        {{-- SECTION 6: NATIONALITIES (Optional) --}}
                        {{-- ==================================== --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Nationalities</h6>
                                <span class="text-muted small">Select one or more nationalities (optional)</span>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Select Nationalities</label>
                                        <div class="dropdown">
                                            <button type="button" class="form-control form-select text-start d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" aria-expanded="false" id="nationalitiesDropdown">
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
                                            </button>
                                            <div class="dropdown-menu w-100 p-2" id="nationalitiesList">
                                                <div class="dropdown-search mb-2">
                                                    <input type="text" class="form-control form-control-sm" placeholder="Search nationalities..." id="nationalitySearch">
                                                </div>
                                                <div class="dropdown-items-container" style="max-height: 250px; overflow-y: auto;">
                                                    @foreach($nationalities as $nationality)
                                                        <div class="form-check dropdown-item p-2">
                                                            <input class="form-check-input nationality-checkbox" type="checkbox" 
                                                                   name="nationalities[]" 
                                                                   value="{{ $nationality->nationality_id }}" 
                                                                   id="nationality_{{ $nationality->nationality_id }}"
                                                                   {{ in_array($nationality->nationality_id, old('nationalities', $user->nationalities->pluck('nationality_id')->toArray())) ? 'checked' : '' }}>
                                                            <label class="form-check-label w-100 ms-2" for="nationality_{{ $nationality->nationality_id }}">
                                                                {{ $nationality->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="dropdown-footer p-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted" id="nationalityCount">0 selected</small>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearNationalities">
                                                            Clear All
                                                        </button>
                                                    </div>
                                                </div>
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

                        {{-- ============================================ --}}
                        {{-- SECTION 7: EDUCATIONAL QUALIFICATIONS (Optional) --}}
                        {{-- ============================================ --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Educational Qualifications</h6>
                                <span class="text-muted small">Select one or more education levels (optional)</span>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Select Education Levels</label>
                                        <div class="dropdown">
                                            <button type="button" class="form-control form-select text-start d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" aria-expanded="false" id="diplomasDropdown">
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
                                            </button>
                                            <div class="dropdown-menu w-100 p-2" id="diplomasList">
                                                <div class="dropdown-search mb-2">
                                                    <input type="text" class="form-control form-control-sm" placeholder="Search education levels..." id="diplomaSearch">
                                                </div>
                                                <div class="dropdown-items-container" style="max-height: 250px; overflow-y: auto;">
                                                    @foreach($diplomas as $diploma)
                                                        <div class="form-check dropdown-item p-2">
                                                            <input class="form-check-input diploma-checkbox" type="checkbox" 
                                                                   name="diplomas[]" 
                                                                   value="{{ $diploma->diploma_id }}" 
                                                                   id="diploma_{{ $diploma->diploma_id }}"
                                                                   {{ in_array($diploma->diploma_id, old('diplomas', $user->diplomas->pluck('diploma_id')->toArray())) ? 'checked' : '' }}>
                                                            <label class="form-check-label w-100 ms-2" for="diploma_{{ $diploma->diploma_id }}">
                                                                {{ $diploma->diploma_name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="dropdown-footer p-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted" id="diplomaCount">0 selected</small>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearDiplomas">
                                                            Clear All
                                                        </button>
                                                    </div>
                                                </div>
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

                        {{-- ================================================ --}}
                        {{-- SECTION 8: ADDITIONAL INFORMATION (Backward Compatibility) --}}
                        {{-- ================================================ --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Additional Information</h6>
                                <span class="text-muted small">Optional identification and status details</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    {{-- Row 1 --}}
                                    <div class="col-md-3">
                                        <label for="marital_status" class="form-label fw-semibold">Marital Status</label>
                                        <select name="marital_status" id="marital_status" class="form-control form-select @error('marital_status') is-invalid @enderror">
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

                                    <div class="col-md-3">
                                        <label for="employment_status" class="form-label fw-semibold">Employment Status</label>
                                        <select name="employment_status" id="employment_status" class="form-control form-select @error('employment_status') is-invalid @enderror">
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

                                    <div class="col-md-3">
                                        <label for="identification_id" class="form-label fw-semibold">National ID</label>
                                        <input type="text" name="identification_id" id="identification_id" 
                                               class="form-control @error('identification_id') is-invalid @enderror" 
                                               value="{{ old('identification_id', $user->identification_id) }}" 
                                               placeholder="National ID">
                                        @error('identification_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Row 2 --}}
                                    <div class="col-md-3">
                                        <label for="passport_number" class="form-label fw-semibold">Passport Number</label>
                                        <input type="text" name="passport_number" id="passport_number" 
                                               class="form-control @error('passport_number') is-invalid @enderror" 
                                               value="{{ old('passport_number', $user->passport_number) }}" 
                                               placeholder="Passport Number">
                                        @error('passport_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="register_number" class="form-label fw-semibold">Register Number</label>
                                        <input type="text" name="register_number" id="register_number" 
                                               class="form-control @error('register_number') is-invalid @enderror" 
                                               value="{{ old('register_number', $user->register_number) }}" 
                                               placeholder="Register Number">
                                        @error('register_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="register_place" class="form-label fw-semibold">Register Place</label>
                                        <input type="text" name="register_place" id="register_place" 
                                               class="form-control @error('register_place') is-invalid @enderror" 
                                               value="{{ old('register_place', $user->register_place) }}" 
                                               placeholder="Register Place">
                                        @error('register_place')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ======================== --}}
                        {{-- SECTION 9: ACTION BUTTONS --}}
                        {{-- ======================== --}}
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
    /* Base Styles */
    .section-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        overflow: visible;
        margin-bottom: 1.5rem;
        background: white;
        transition: all 0.3s ease;
    }
    
    .section-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    
    .section-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.25rem 1.5rem;
        border-bottom: 2px solid #dee2e6;
        border-radius: 10px 10px 0 0;
    }
    
    .section-header h6 {
        color: #2c3e50;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .section-body {
        padding: 1.5rem;
        position: relative;
        overflow: visible;
    }
    
    .form-label {
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #495057;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 2px solid #e9ecef;
        padding: 0.625rem 0.875rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: #fff;
        color: #495057;
    }
    
    .form-control::placeholder, .form-select:invalid {
        color: #6c757d !important;
        opacity: 0.7;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        outline: 0;
        background-color: #fff;
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    }
    
    /* Custom dropdown styling to match form-select */
    .dropdown > .form-select {
        position: relative;
        cursor: pointer;
        background-image: none;
        padding-right: 2.5rem;
    }
    
    .dropdown > .form-select::after {
        content: "";
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        border-top: 0.3em solid;
        border-right: 0.3em solid transparent;
        border-bottom: 0;
        border-left: 0.3em solid transparent;
        color: #6c757d;
        opacity: 0.7;
    }
    
    .dropdown > .form-select.placeholder-toggle {
        color: #6c757d;
        opacity: 0.7;
    }
    
    .dropdown > .form-select:not(.placeholder-toggle) {
        color: #495057;
        opacity: 1;
    }
    
    .dropdown-menu {
        border: 2px solid #86b7fe;
        border-top: none;
        border-radius: 0 0 8px 8px;
        margin-top: -2px;
        padding: 0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 100%;
        z-index: 1050;
    }
    
    .dropdown-search {
        padding: 0.75rem;
        border-bottom: none;
        background-color: #f8f9fa;
    }
    
    .dropdown-search input {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        color: #495057;
    }
    
    .dropdown-search input::placeholder {
        color: #6c757d !important;
        opacity: 0.7;
    }
    
    .dropdown-search input:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
    }
    
    .dropdown-items-container {
        padding: 0.5rem;
    }
    
    .dropdown-item {
        padding: 0.625rem 0.75rem;
        border-radius: 6px;
        margin-bottom: 0.125rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .dropdown-item:hover {
        background-color: #f1f3f4;
    }
    
    .dropdown-item:active {
        background-color: #e9ecef;
    }
    
    .dropdown-item .form-check {
        display: flex;
        align-items: center;
        margin: 0;
    }
    
    .dropdown-item .form-check-input {
        width: 1.1rem;
        height: 1.1rem;
        margin-top: 0;
        cursor: pointer;
        border: 2px solid #adb5bd;
    }
    
    .dropdown-item .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .dropdown-item .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .dropdown-item .form-check-label {
        cursor: pointer;
        user-select: none;
        font-size: 0.95rem;
        color: #495057;
        flex: 1;
        padding-left: 0.5rem;
    }
    
    .dropdown-footer {
        background-color: #f8f9fa;
        border-radius: 0 0 8px 8px;
    }
    
    .dropdown-footer .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.85rem;
    }
    
    /* Placeholder color for all inputs */
    input::placeholder,
    textarea::placeholder,
    select:invalid {
        color: #6c757d !important;
        opacity: 0.7;
    }
    
    /* Badge for selected items */
    .selected-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
        margin-top: 0.5rem;
    }
    
    .selected-badge {
        background-color: #e7f1ff;
        color: #0d6efd;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        border: 1px solid #b3d4ff;
    }
    
    .selected-badge .remove-badge {
        background: none;
        border: none;
        color: #0d6efd;
        font-size: 1.2rem;
        line-height: 1;
        padding: 0;
        margin-left: 0.25rem;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    
    .selected-badge .remove-badge:hover {
        opacity: 1;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .section-card .section-header,
        .section-card .section-body {
            padding: 1rem;
        }
        
        .dropdown-menu {
            position: fixed;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            width: 90vw;
            max-width: 400px;
            max-height: 70vh;
        }
        
        .dropdown-items-container {
            max-height: 40vh;
        }
    }
    
    /* Button Styles */
    .btn {
        padding: 0.625rem 1.25rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        border: none;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }
    
    .btn-outline-secondary {
        border: 2px solid #6c757d;
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
    }
    
    /* Loading State */
    .btn.loading {
        position: relative;
        color: transparent;
    }
    
    .btn.loading::after {
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    
    /* Card Styles */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        background-color: #f8f9fa;
    }
    
    /* Alert Styles */
    .alert {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .section-card {
        animation: fadeIn 0.4s ease-out;
    }
    
    /* Scrollbar Styling */
    .dropdown-items-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .dropdown-items-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    .dropdown-items-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    .dropdown-items-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Required Field Indicator */
    .required-indicator {
        color: #dc3545;
        margin-left: 2px;
    }
    
    /* Help Text */
    .form-text-help {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }
    
    /* Focus States for Accessibility */
    .form-control:focus-visible,
    .form-select:focus-visible,
    .dropdown-toggle:focus-visible {
        outline: 2px solid #0d6efd;
        outline-offset: 2px;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation
        const form = document.getElementById('userForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                } else {
                    // Add loading state to submit button
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Updating User...';
                }
                
                form.classList.add('was-validated');
            });
        }
        
        // Multiselect Dropdown Functions
        function initializeDropdown(dropdownId, searchId, checkboxClass, countId, clearBtnId) {
            const dropdown = document.getElementById(dropdownId);
            const dropdownButton = dropdown.closest('.form-select');
            const searchInput = document.getElementById(searchId);
            const checkboxes = document.querySelectorAll(`.${checkboxClass}`);
            const countElement = document.getElementById(countId);
            const clearButton = document.getElementById(clearBtnId);
            const selectedText = dropdown.querySelector('.selected-text');
            
            // Update selected count and text
            function updateSelection() {
                const selected = Array.from(checkboxes).filter(cb => cb.checked);
                const selectedCount = selected.length;
                
                // Update count display
                if (countElement) {
                    countElement.textContent = `${selectedCount} selected`;
                }
                
                // Update button text and toggle placeholder class
                if (selectedCount === 0) {
                    selectedText.textContent = `Select ${checkboxClass === 'nationality-checkbox' ? 'nationalities' : 'education levels'}...`;
                    dropdownButton.classList.add('placeholder-toggle');
                } else if (selectedCount === 1) {
                    const label = selected[0].closest('label').textContent.trim();
                    selectedText.textContent = label;
                    dropdownButton.classList.remove('placeholder-toggle');
                } else {
                    selectedText.textContent = `${selectedCount} ${checkboxClass === 'nationality-checkbox' ? 'nationalities' : 'education levels'} selected`;
                    dropdownButton.classList.remove('placeholder-toggle');
                }
            }
            
            // Search functionality
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const items = dropdown.querySelectorAll('.dropdown-item');
                    
                    items.forEach(item => {
                        const label = item.querySelector('label').textContent.toLowerCase();
                        item.style.display = label.includes(searchTerm) ? 'block' : 'none';
                    });
                });
            }
            
            // Clear all functionality
            if (clearButton) {
                clearButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    checkboxes.forEach(cb => {
                        cb.checked = false;
                        cb.dispatchEvent(new Event('change'));
                    });
                    
                    updateSelection();
                });
            }
            
            // Update on checkbox change
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelection);
            });
            
            // Initialize count
            updateSelection();
            
            // Prevent dropdown close when clicking inside
            const dropdownMenu = dropdown;
            if (dropdownMenu) {
                dropdownMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        }
        
        // Initialize both dropdowns
        initializeDropdown('nationalitiesList', 'nationalitySearch', 'nationality-checkbox', 'nationalityCount', 'clearNationalities');
        initializeDropdown('diplomasList', 'diplomaSearch', 'diploma-checkbox', 'diplomaCount', 'clearDiplomas');
        
        // Phone number formatting - FIXED: No delay, immediate formatting
        const phoneInputs = ['mobile_phone', 'office_phone', 'extension_number', 'home_phone_1', 
                           'office_phone_2', 'extension_number_2', 'home_phone'];
        
        phoneInputs.forEach(function(id) {
            const input = document.getElementById(id);
            if (input) {
                // Format on input (no delay)
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    
                    // Store cursor position
                    const cursorPos = e.target.selectionStart;
                    
                    if (value.length > 0) {
                        // Format as +961 70 123 456
                        let formatted = '+961 ';
                        
                        if (value.length > 2) {
                            formatted += value.substring(0, 2) + ' ';
                            if (value.length > 5) {
                                formatted += value.substring(2, 5) + ' ';
                                if (value.length > 8) {
                                    formatted += value.substring(5, 8) + ' ';
                                    formatted += value.substring(8, 12);
                                } else {
                                    formatted += value.substring(5, 8);
                                }
                            } else {
                                formatted += value.substring(2, 5);
                            }
                        } else {
                            formatted += value;
                        }
                        
                        e.target.value = formatted;
                        
                        // Try to restore cursor position (adjusting for added characters)
                        try {
                            const newCursorPos = cursorPos + (formatted.length - value.length);
                            e.target.setSelectionRange(newCursorPos, newCursorPos);
                        } catch (err) {
                            // Ignore cursor position errors
                        }
                    }
                });
                
                // Remove focus to prevent formatting issues
                input.addEventListener('blur', function(e) {
                    let value = e.target.value.trim();
                    if (value && !value.startsWith('+')) {
                        e.target.value = '+961 ' + value.replace(/\D/g, '');
                    }
                });
            }
        });
        
        // Date of birth validation (minimum 18 years old)
        const dobInput = document.getElementById('dob');
        if (dobInput) {
            dobInput.addEventListener('change', function(e) {
                const selectedDate = new Date(e.target.value);
                const today = new Date();
                const minAgeDate = new Date();
                minAgeDate.setFullYear(today.getFullYear() - 18);
                
                if (selectedDate > minAgeDate) {
                    alert('User must be at least 18 years old.');
                    e.target.value = '{{ old("dob", $user->dob) }}';
                }
            });
        }
        
        // Real-time validation for required fields
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('blur', function() {
                validateField(this);
            });
            
            field.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        });
        
        function validateField(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                return false;
            } else {
                field.classList.remove('is-invalid');
                return true;
            }
        }
        
        // Form reset handler
        form.addEventListener('reset', function() {
            // Remove validation classes
            form.classList.remove('was-validated');
            requiredFields.forEach(field => {
                field.classList.remove('is-invalid');
            });
            
            // Reset submit button
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Update User';
            
            // Reinitialize dropdowns after a short delay
            setTimeout(() => {
                initializeDropdown('nationalitiesList', 'nationalitySearch', 'nationality-checkbox', 'nationalityCount', 'clearNationalities');
                initializeDropdown('diplomasList', 'diplomaSearch', 'diploma-checkbox', 'diplomaCount', 'clearDiplomas');
            }, 100);
        });
        
        // Auto-focus on first field
        const firstField = form.querySelector('input:not([type="hidden"]):not([type="checkbox"]), select, textarea');
        if (firstField) {
            setTimeout(() => {
                firstField.focus();
            }, 100);
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    const dropdown = menu.closest('.dropdown');
                    if (dropdown) {
                        const button = dropdown.querySelector('.form-select');
                        if (button) {
                            button.classList.remove('show');
                            menu.classList.remove('show');
                        }
                    }
                });
            }
        });
    });
</script>
@endsection