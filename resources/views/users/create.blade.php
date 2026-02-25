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
                    <p class="text-muted mb-0">Create a new professional contact in the system</p>
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

                    <form action="{{ route('users.store') }}" method="POST" class="needs-validation" novalidate id="userForm">
                        @csrf
                        <!-- Additional CSRF protection -->
                        <input type="hidden" name="_token" id="csrf-token" value="{{ csrf_token() }}">
                        
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
                                               value="{{ old('first_name') }}" 
                                               placeholder="First Name" required>
                                        @error('first_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="middle_name" class="form-label fw-semibold">Middle Name</label>
                                        <input type="text" name="middle_name" id="middle_name" 
                                               class="form-control @error('middle_name') is-invalid @enderror" 
                                               value="{{ old('middle_name') }}" 
                                               placeholder="Middle Name">
                                        @error('middle_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="last_name" class="form-label fw-semibold">
                                            Last Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="last_name" id="last_name" 
                                               class="form-control @error('last_name') is-invalid @enderror" 
                                               value="{{ old('last_name') }}" 
                                               placeholder="Last Name" required>
                                        @error('last_name')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label for="mother_name" class="form-label fw-semibold">Mother's Name</label>
                                        <input type="text" name="mother_name" id="mother_name" 
                                               class="form-control @error('mother_name') is-invalid @enderror" 
                                               value="{{ old('mother_name') }}" 
                                               placeholder="Mother's Name">
                                        @error('mother_name')
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
                                            <option value="Male" {{ old('gender')=='Male' ? 'selected':'' }}>Male</option>
                                            <option value="Female" {{ old('gender')=='Female' ? 'selected':'' }}>Female</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="dob" class="form-label fw-semibold">Date of Birth</label>
                                        <input type="date" name="dob" id="dob" 
                                               class="form-control @error('dob') is-invalid @enderror" 
                                               value="{{ old('dob') }}">
                                        @error('dob')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="prefix" class="form-label fw-semibold">Prefix</label>
                                        <select name="prefix" id="prefix" class="form-control form-select @error('prefix') is-invalid @enderror">
                                            <option value="">Select Prefix</option>
                                            <option value="Dr." {{ old('prefix')=='Dr.' ? 'selected':'' }}>Dr.</option>
                                            <option value="Mr." {{ old('prefix')=='Mr.' ? 'selected':'' }}>Mr.</option>
                                            <option value="Ms." {{ old('prefix')=='Ms.' ? 'selected':'' }}>Ms.</option>
                                            <option value="Mrs." {{ old('prefix')=='Mrs.' ? 'selected':'' }}>Mrs.</option>
                                            <option value="Prof." {{ old('prefix')=='Prof.' ? 'selected':'' }}>Prof.</option>
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
                                               value="{{ old('address') }}" 
                                               placeholder="Address" required>
                                        @error('address')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
{{-- ============================ --}}
{{-- SECTION: USER TYPE --}}
{{-- ============================ --}}
<div class="section-card mb-4">
    <div class="section-header">
        <h6 class="mb-0 fw-semibold">User Type</h6>
        <span class="text-muted small">Select the type of user</span>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-md-6">
                <label for="type" class="form-label fw-semibold">
                    User Type <span class="text-danger">*</span>
                </label>
                <select name="type" id="type" class="form-control form-select @error('type') is-invalid @enderror" required>
                    <option value="">Select User Type</option>
                    <option value="Stakeholder" {{ old('type') == 'Stakeholder' ? 'selected' : '' }}>Stakeholder</option>
                    <option value="Beneficiary" {{ old('type') == 'Beneficiary' ? 'selected' : '' }}>Beneficiary</option>
          a     </select>
                @error('type')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="form-text text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Stakeholders are typically partners, donors, or institutions. Beneficiaries are recipients of services.
                </div>
            </div>
        </div>
    </div>
</div>
                        {{-- ================================ --}}
                        {{-- SECTION 2: CONTACT INFORMATION --}}
                        {{-- ================================ --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Contact Information</h6>
                                <span class="text-muted small">All contact details in one section</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    {{-- Row 1 --}}
                                    <div class="col-md-4">
                                        <label for="email" class="form-label fw-semibold">Email</label>
                                        <input type="email" name="email" id="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email') }}"
                                               placeholder="Email">
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="phone_number" class="form-label fw-semibold">
                                            Phone Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" name="phone_number" id="phone_number" 
                                               class="form-control @error('phone_number') is-invalid @enderror" 
                                               value="{{ old('phone_number') }}" 
                                               placeholder="Phone Number" required>
                                        @error('phone_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="office_phone" class="form-label fw-semibold">Office Phone</label>
                                        <input type="tel" name="office_phone" id="office_phone" 
                                               class="form-control @error('office_phone') is-invalid @enderror" 
                                               value="{{ old('office_phone') }}" 
                                               placeholder="Office Phone">
                                        @error('office_phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Row 2 --}}
                                    <div class="col-md-4">
                                        <label for="extension_number" class="form-label fw-semibold">Extension</label>
                                        <input type="text" name="extension_number" id="extension_number" 
                                               class="form-control @error('extension_number') is-invalid @enderror" 
                                               value="{{ old('extension_number') }}" 
                                               placeholder="Extension">
                                        @error('extension_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="home_phone" class="form-label fw-semibold">Home Phone</label>
                                        <input type="tel" name="home_phone" id="home_phone" 
                                               class="form-control @error('home_phone') is-invalid @enderror" 
                                               value="{{ old('home_phone') }}" 
                                               placeholder="Home Phone">
                                        @error('home_phone')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================================== --}}
                        {{-- SECTION 3: PROFESSIONAL INFORMATION --}}
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
                                            <option value="0" {{ old('is_high_profile')==='0' ? 'selected':'' }}>No</option>
                                            <option value="1" {{ old('is_high_profile')==='1' ? 'selected':'' }}>Yes</option>
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
                                            <option value="International" {{ old('scope')=='International' ? 'selected':'' }}>International</option>
                                            <option value="Regional" {{ old('scope')=='Regional' ? 'selected':'' }}>Regional</option>
                                            <option value="National" {{ old('scope')=='National' ? 'selected':'' }}>National</option>
                                            <option value="Local" {{ old('scope')=='Local' ? 'selected':'' }}>Local</option>
                                        </select>
                                        @error('scope')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="employment_status" class="form-label fw-semibold">Employment Status</label>
                                        <select name="employment_status" id="employment_status" class="form-control form-select @error('employment_status') is-invalid @enderror">
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

                                    <div class="col-md-3">
                                        <label for="sector" class="form-label fw-semibold">Sector</label>
                                        <input type="text" name="sector" id="sector" 
                                               class="form-control @error('sector') is-invalid @enderror" 
                                               value="{{ old('sector') }}" 
                                               placeholder="Sector">
                                        @error('sector')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================ --}}
                        {{-- SECTION 4: PRIMARY POSITION --}}
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
                                               value="{{ old('position_1') }}" 
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
                                               value="{{ old('organization_1') }}" 
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
                                            <option value="Public Sector" {{ old('organization_type_1')=='Public Sector' ? 'selected':'' }}>Public Sector</option>
                                            <option value="Private Sector" {{ old('organization_type_1')=='Private Sector' ? 'selected':'' }}>Private Sector</option>
                                            <option value="Academia" {{ old('organization_type_1')=='Academia' ? 'selected':'' }}>Academia</option>
                                            <option value="UN" {{ old('organization_type_1')=='UN' ? 'selected':'' }}>UN</option>
                                            <option value="INGOs" {{ old('organization_type_1')=='INGOs' ? 'selected':'' }}>INGOs</option>
                                            <option value="Civil Society" {{ old('organization_type_1')=='Civil Society' ? 'selected':'' }}>Civil Society</option>
                                            <option value="NGOs" {{ old('organization_type_1')=='NGOs' ? 'selected':'' }}>NGOs</option>
                                            <option value="Activist" {{ old('organization_type_1')=='Activist' ? 'selected':'' }}>Activist</option>
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
                                        <select name="status_1" id="status_1" class="form-control form-select @error('status_1') is-invalid @enderror" required>
                                            <option value="">Select Status</option>
                                            <option value="Active" {{ old('status_1')=='Active' ? 'selected':'' }}>Active</option>
                                            <option value="Inactive" {{ old('status_1')=='Inactive' ? 'selected':'' }}>Inactive</option>
                                        </select>
                                        @error('status_1')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ======================================== --}}
                        {{-- SECTION 5: SECONDARY POSITION (Optional) --}}
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
                                               value="{{ old('position_2') }}" 
                                               placeholder="Position">
                                        @error('position_2')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="organization_2" class="form-label fw-semibold">Organization</label>
                                        <input type="text" name="organization_2" id="organization_2" 
                                               class="form-control @error('organization_2') is-invalid @enderror" 
                                               value="{{ old('organization_2') }}" 
                                               placeholder="Organization">
                                        @error('organization_2')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="organization_type_2" class="form-label fw-semibold">Organization Type</label>
                                        <select name="organization_type_2" id="organization_type_2" class="form-control form-select @error('organization_type_2') is-invalid @enderror">
                                            <option value="">Select Organization Type</option>
                                            <option value="Public Sector" {{ old('organization_type_2')=='Public Sector' ? 'selected':'' }}>Public Sector</option>
                                            <option value="Private Sector" {{ old('organization_type_2')=='Private Sector' ? 'selected':'' }}>Private Sector</option>
                                            <option value="Academia" {{ old('organization_type_2')=='Academia' ? 'selected':'' }}>Academia</option>
                                            <option value="UN" {{ old('organization_type_2')=='UN' ? 'selected':'' }}>UN</option>
                                            <option value="INGOs" {{ old('organization_type_2')=='INGOs' ? 'selected':'' }}>INGOs</option>
                                            <option value="Civil Society" {{ old('organization_type_2')=='Civil Society' ? 'selected':'' }}>Civil Society</option>
                                            <option value="NGOs" {{ old('organization_type_2')=='NGOs' ? 'selected':'' }}>NGOs</option>
                                            <option value="Activist" {{ old('organization_type_2')=='Activist' ? 'selected':'' }}>Activist</option>
                                        </select>
                                        @error('organization_type_2')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Row 2 --}}
                                    <div class="col-md-3">
                                        <label for="status_2" class="form-label fw-semibold">Status</label>
                                        <select name="status_2" id="status_2" class="form-control form-select @error('status_2') is-invalid @enderror">
                                            <option value="">Select Status</option>
                                            <option value="Active" {{ old('status_2')=='Active' ? 'selected':'' }}>Active</option>
                                            <option value="Inactive" {{ old('status_2')=='Inactive' ? 'selected':'' }}>Inactive</option>
                                        </select>
                                        @error('status_2')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ============================================== --}}
                        {{-- SECTION 6: COMMUNITY OF PRACTICE (Optional) --}}
                        {{-- ============================================== --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Community of Practice</h6>
                                <span class="text-muted small">Enter default COP for activities</span>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="default_cop_name" class="form-label fw-semibold">Default COP</label>
                                            <input type="text" 
                                                   name="default_cop_name" 
                                                   id="default_cop_name" 
                                                   class="form-control @error('default_cop_name') is-invalid @enderror"
                                                   value="{{ old('default_cop_name') }}"
                                                   placeholder="Enter COP name">
                                            @error('default_cop_name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ==================================== --}}
                        {{-- SECTION 7: NATIONALITIES (Optional) --}}
                        {{-- ==================================== --}}
                        <div class="section-card mb-5">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Nationalities</h6>
                                <span class="text-muted small">Select one or more nationalities (optional)</span>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-0">
                                            <label for="nationalities_select" class="form-label fw-semibold mb-2">Select Nationalities</label>
                                            <select id="nationalities_select" 
                                                    multiple
                                                    class="form-control @error('nationalities') is-invalid @enderror"
                                                    name="nationalities[]">
                                                @foreach($nationalities as $nationality)
                                                    <option value="{{ $nationality->nationality_id }}" 
                                                            {{ in_array($nationality->nationality_id, old('nationalities', [])) ? 'selected' : '' }}>
                                                        {{ $nationality->name }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                        </div>

                        {{-- ============================================ --}}
                        {{-- SECTION 8: EDUCATIONAL QUALIFICATIONS (Optional) --}}
                        {{-- ============================================ --}}
                        <div class="section-card mb-5">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Educational Qualifications</h6>
                                <span class="text-muted small">Select one or more education levels (optional)</span>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-0">
                                            <label for="diplomas_select" class="form-label fw-semibold mb-2">Select Education Levels</label>
                                            <select id="diplomas_select" 
                                                    multiple
                                                    class="form-control @error('diplomas') is-invalid @enderror"
                                                    name="diplomas[]">
                                                @foreach($diplomas as $diploma)
                                                    <option value="{{ $diploma->diploma_id }}" 
                                                            {{ in_array($diploma->diploma_id, old('diplomas', [])) ? 'selected' : '' }}>
                                                        {{ $diploma->diploma_name }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                                        <button type="reset" class="btn btn-outline-secondary me-2" id="resetBtn">
                                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
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
    /* Section Styling */
    .section-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        overflow: hidden;
    }
    
    .section-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .section-body {
        padding: 1.5rem;
    }
    
    /* Form Control Styling */
    .form-control, .form-select {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.95rem;
        color: #495057;
        background-color: #fff;
        transition: all 0.3s ease;
        min-height: 42px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        outline: 0;
    }
    
    /* Form Labels */
    .form-label.fw-semibold {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        color: #495057;
        font-weight: 600;
    }
    
    /* Select2 Styling to match form controls */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        padding: 0.25rem 0.5rem !important;
        background-color: #fff !important;
        transition: all 0.3s ease !important;
        width: 100% !important;
        font-size: 0.95rem !important;
        min-height: 42px !important;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
        outline: 0 !important;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 4px !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Selected tags styling - matching form style */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 50px !important;
        color: #495057 !important;
        padding: 0.25rem 0.75rem !important;
        font-size: 0.85rem !important;
        font-weight: 500 !important;
        display: flex !important;
        align-items: center !important;
        margin: 1px !important;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #6c757d !important;
        font-size: 1rem !important;
        line-height: 1 !important;
        margin-right: 4px !important;
        opacity: 0.7 !important;
        transition: opacity 0.2s !important;
        order: -1 !important;
        padding: 0 !important;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #495057 !important;
        opacity: 1 !important;
        background: transparent !important;
    }
    
    /* Dropdown styling */
    .select2-container--default .select2-dropdown {
        border: 1px solid #dee2e6 !important;
        border-radius: 0 0 8px 8px !important;
        margin-top: -1px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }
    
    .select2-container--default .select2-results__option {
        padding: 0.5rem 0.75rem !important;
        font-size: 0.95rem !important;
        color: #495057 !important;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #f8f9fa !important;
        color: #495057 !important;
    }
    
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #e9ecef !important;
        color: #495057 !important;
    }
    
    /* Placeholder styling */
    .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
        color: #6c757d !important;
        font-size: 0.95rem !important;
        line-height: 1.5 !important;
    }
    
    /* Clear button styling */
    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        color: #6c757d !important;
        font-size: 1rem !important;
        margin-right: 6px !important;
        padding: 0 !important;
    }
    
    /* Search input styling */
    .select2-container--default .select2-search--inline .select2-search__field {
        font-size: 0.95rem !important;
        color: #495057 !important;
        padding: 0 !important;
        margin: 0 !important;
        min-height: auto !important;
        line-height: 1.5 !important;
    }
    
    /* Button loading state */
    .btn.loading {
        position: relative;
        color: transparent;
    }
    
    .btn.loading::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        top: 50%;
        left: 50%;
        margin-left: -10px;
        margin-top: -10px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.8s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Invalid state styling */
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    .is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15) !important;
    }
    
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    /* Select2 invalid state */
    .select2-container--default .select2-selection--multiple.is-invalid {
        border-color: #dc3545 !important;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--multiple.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15) !important;
    }
    
    /* Phone number input specific styling */
    input[type="tel"] {
        font-family: monospace;
    }
    
    /* Ensure proper height for all inputs */
    .form-control:not(textarea) {
        height: 42px;
    }
    
    /* Make Select2 match regular input height */
    .select2-container .select2-selection--multiple {
        min-height: 42px;
        height: auto;
    }
</style>
@endsection

@section('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the custom multiple select for nationalities
        $('#nationalities_select').select2({
            placeholder: 'Select nationalities...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            dropdownParent: $('#nationalities_select').parent(),
            tags: false,
            multiple: true,
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return data.text;
            },
            templateSelection: function(data) {
                return data.text;
            },
            language: {
                noResults: function() {
                    return "No nationalities found";
                }
            }
        }).on('select2:open', function() {
            // Ensure dropdown is properly positioned
            $(this).data('select2').$dropdown.css('width', $(this).outerWidth() + 'px');
        });
        
        // Initialize the custom multiple select for diplomas
        $('#diplomas_select').select2({
            placeholder: 'Select education levels...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            dropdownParent: $('#diplomas_select').parent(),
            tags: false,
            multiple: true,
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                return data.text;
            },
            templateSelection: function(data) {
                return data.text;
            },
            language: {
                noResults: function() {
                    return "No education levels found";
                }
            }
        }).on('select2:open', function() {
            // Ensure dropdown is properly positioned
            $(this).data('select2').$dropdown.css('width', $(this).outerWidth() + 'px');
        });



        // ============================================
    // PHONE NUMBER FORMATTING (Lebanese Format)
    // ============================================
    function formatPhoneNumber(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        let lastValue = input.value;
        
        input.addEventListener('input', function(e) {
            let value = e.target.value;
            
            // Remove all non-digit characters except '+'
            let cleanValue = value.replace(/[^\d+]/g, '');
            
            // Store cursor position
            const cursorPos = e.target.selectionStart;
            
            // Format the number with spaces
            let formatted = '';
            let digits = '';
            
            // Check if it starts with +961
            if (cleanValue.startsWith('+961')) {
                digits = cleanValue.substring(4); // Remove +961
                formatted = '+961';
            } else if (cleanValue.startsWith('961')) {
                digits = cleanValue.substring(3); // Remove 961
                formatted = '+961';
            } else if (cleanValue.startsWith('+')) {
                // If starts with + but not +961, keep as is
                formatted = cleanValue;
            } else {
                digits = cleanValue;
                formatted = '';
            }
            
            // Add spaces for formatting (only for +961 numbers)
            if (formatted === '+961' && digits.length > 0) {
                // Format: +961 XX XXX XXX
                formatted += ' ' + digits.substring(0, 2);
                if (digits.length > 2) {
                    formatted += ' ' + digits.substring(2, 5);
                    if (digits.length > 5) {
                        formatted += ' ' + digits.substring(5, 8);
                    }
                }
            } else if (formatted !== '+961' && formatted.length > 0) {
                // For other international numbers, just add spaces every 3 digits
                formatted = formatted.replace(/(\d{3})(?=\d)/g, '$1 ');
            }
            
            // Limit to 15 characters maximum (for +961 format)
            if (formatted.length > 15) {
                formatted = formatted.substring(0, 15);
            }
            
            // Only update if value has changed
            if (formatted !== lastValue) {
                e.target.value = formatted;
                lastValue = formatted;
                
                // Try to maintain cursor position
                try {
                    // Simple cursor position adjustment
                    let newCursorPos = cursorPos;
                    
                    // If we added spaces, adjust cursor
                    if (formatted.length > value.length) {
                        // Count how many spaces were added before cursor position
                        const addedSpaces = (formatted.substring(0, cursorPos).match(/ /g) || []).length - 
                                           (value.substring(0, cursorPos).match(/ /g) || []).length;
                        newCursorPos = cursorPos + addedSpaces;
                    } else if (formatted.length < value.length) {
                        // If we removed characters, adjust cursor
                        newCursorPos = Math.max(0, cursorPos - 1);
                    }
                    
                    e.target.setSelectionRange(newCursorPos, newCursorPos);
                } catch (err) {
                    // Ignore cursor position errors
                }
            }
        });
        
        // On blur, format properly if it looks like a Lebanese number
        input.addEventListener('blur', function(e) {
            let value = e.target.value.trim();
            if (!value) return;
            
            // Remove all non-digit characters except '+'
            let cleanValue = value.replace(/[^\d+]/g, '');
            
            // If it's 8 digits (Lebanese mobile without country code), add +961
            if (cleanValue.length === 8 && !cleanValue.startsWith('+') && !cleanValue.startsWith('961')) {
                const formatted = `+961 ${cleanValue.substring(0, 2)} ${cleanValue.substring(2, 5)} ${cleanValue.substring(5, 8)}`;
                e.target.value = formatted;
                lastValue = formatted;
            }
            // If it's 11 digits starting with 961 (Lebanese number with country code but no +)
            else if (cleanValue.length === 11 && cleanValue.startsWith('961')) {
                const digits = cleanValue.substring(3);
                const formatted = `+961 ${digits.substring(0, 2)} ${digits.substring(2, 5)} ${digits.substring(5, 8)}`;
                e.target.value = formatted;
                lastValue = formatted;
            }
            // If it's 12 digits starting with +961 (full Lebanese number with +)
            else if (cleanValue.length === 12 && cleanValue.startsWith('+961')) {
                const digits = cleanValue.substring(4);
                const formatted = `+961 ${digits.substring(0, 2)} ${digits.substring(2, 5)} ${digits.substring(5, 8)}`;
                e.target.value = formatted;
                lastValue = formatted;
            }
        });
        
        // Prevent entering more than allowed digits
        input.addEventListener('keydown', function(e) {
            // Allow: backspace, delete, tab, escape, enter, arrows
            if ([46, 8, 9, 27, 13, 110, 190].includes(e.keyCode) ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            
            // Get current value and count digits
            let value = this.value;
            let digits = value.replace(/[^\d]/g, '');
            
            // If it starts with +961, limit to 8 more digits
            if (value.startsWith('+961')) {
                if (digits.length >= 11) { // 3 (961) + 8 digits = 11
                    e.preventDefault();
                }
            }
            // Otherwise limit to reasonable length
            else if (digits.length >= 15) {
                e.preventDefault();
            }
        });
    }
    
    // Apply formatting to all phone inputs
    formatPhoneNumber('phone_number');
    formatPhoneNumber('office_phone');
    formatPhoneNumber('home_phone');
        
        // Form validation
        const form = document.getElementById('userForm');
        const submitBtn = document.getElementById('submitBtn');
        const resetBtn = document.getElementById('resetBtn');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                } else {
                    // Add loading state to submit button
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                }
                
                form.classList.add('was-validated');
            });
        }
        
        // Reset button handler
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                // Remove validation classes
                if (form) {
                    form.classList.remove('was-validated');
                    const invalidFields = form.querySelectorAll('.is-invalid');
                    invalidFields.forEach(field => {
                        field.classList.remove('is-invalid');
                    });
                }
                
                // Reset Select2 fields
                $('#nationalities_select').val(null).trigger('change');
                $('#diplomas_select').val(null).trigger('change');
                
                // Reset submit button
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        }
        
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
                    e.target.value = '';
                }
            });
        }
        
        // Real-time validation for required fields
        if (form) {
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
        }
        
        function validateField(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                return false;
            } else {
                field.classList.remove('is-invalid');
                return true;
            }
        }
        
        // Auto-focus on first field
        if (form) {
            const firstField = form.querySelector('input:not([type="hidden"]):not([type="checkbox"]), select, textarea');
            if (firstField) {
                setTimeout(() => {
                    firstField.focus();
                }, 100);
            }
        }
        
        // Ensure Select2 inherits validation classes
        $('#nationalities_select, #diplomas_select').on('change', function() {
            if ($(this).val() && $(this).val().length > 0) {
                $(this).removeClass('is-invalid');
            }
        });
    });
</script>
@endsection