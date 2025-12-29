@extends('layouts.app')

@section('title', 'Edit Activity')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Activities</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Activity</li>
                        </ol>
                    </nav>
                    <h1 class="h2 fw-bold mb-1">Edit Activity</h1>
                    <p class="text-muted mb-0">Update activity information and details</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Activities
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
                            <h5 class="mb-0 fw-semibold">Activity Information Form</h5>
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

                    <form action="#" method="POST" class="needs-validation" novalidate id="activityForm">
                        {{-- @csrf --}}
                        {{-- @method('PUT') --}}
                        
                        {{-- ============================ --}}
                        {{-- SECTION 1: BASIC INFORMATION --}}
                        {{-- ============================ --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Basic Information</h6>
                                <span class="text-muted small">Activity title and type</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    {{-- Activity Titles --}}
                                    <div class="col-md-6">
                                        <label for="title_en" class="form-label fw-semibold">
                                            Activity Title (EN) <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="title_en" id="title_en" 
                                               class="form-control @error('title_en') is-invalid @enderror" 
                                               value="Leadership Training Workshop" 
                                               placeholder="Activity Title in English" required>
                                        @error('title_en')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="title_ar" class="form-label fw-semibold">Activity Title (AR)</label>
                                        <input type="text" name="title_ar" id="title_ar" 
                                               class="form-control @error('title_ar') is-invalid @enderror" 
                                               value="ورشة تدريب القيادة" 
                                               placeholder="Activity Title in Arabic">
                                        @error('title_ar')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Activity Type --}}
                                    <div class="col-md-6">
                                        <label for="activity_type" class="form-label fw-semibold">
                                            Activity Type <span class="text-danger">*</span>
                                        </label>
                                        <select name="activity_type" id="activity_type" class="form-control form-select @error('activity_type') is-invalid @enderror" required>
                                            <option value="">Select Activity Type</option>
                                            <option value="Capacity Building" selected>Capacity Building - بناء القدرات</option>
                                            <option value="Policies & Plans">Policies & Plans - السياسات والخطط</option>
                                            <option value="Engagement Event">Engagement Event - فعالية تفاعلية</option>
                                            <option value="Overview">Overview - نظرة عامة</option>
                                            <option value="Field Activity">Field Activity - نشاط ميداني</option>
                                            <option value="Specialized Service">Specialized Service - خدمة متخصصة</option>
                                            <option value="Research Activity">Research Activity - نشاط بحثي</option>
                                            <option value="Physical Development">Physical Development - تطوير مادي</option>
                                            <option value="Technical Development">Technical Development - تطوير تقني</option>
                                            <option value="Media Production">Media Production - إنتاج إعلامي</option>
                                            <option value="Public Campaign">Public Campaign - حملة توعوية</option>
                                            <option value="Legal Activity">Legal Activity - نشاط قانوني</option>
                                            <option value="Support & Assistance">Support & Assistance - الدعم والمساندة</option>
                                        </select>
                                        @error('activity_type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ======================================= --}}
                        {{-- SECTION 2: HARIRI FOUNDATION PROJECT ALLOCATION --}}
                        {{-- ======================================= --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Hariri Foundation Project Allocation</h6>
                                <span class="text-muted small">Select programs and projects for this activity</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    {{-- Programs Single Select --}}
                                    <div class="col-md-12">
                                        <label for="programs_select" class="form-label fw-semibold mb-2">
                                            Program <span class="text-danger">*</span>
                                        </label>
                                        <select id="programs_select" 
                                                class="form-control @error('program') is-invalid @enderror"
                                                name="program">
                                            <option value="">Select a Program</option>
                                            <option value="PROG001">PROG001 - Rafic Hariri High School</option>
                                            <option value="PROG002">PROG002 - Hajj Bahaa Hariri High School</option>
                                            <option value="PROG010">PROG010 - School Network of Saida & Neighboring Towns</option>
                                            <option value="PROG019">PROG019 - National State Academy</option>
                                        </select>
                                        @error('program')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text mt-1">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Projects will be filtered based on selected program
                                        </div>
                                    </div>

                                    {{-- Projects Multi-Select --}}
                                    <div class="col-md-12">
                                        <label for="projects_select" class="form-label fw-semibold mb-2">Projects</label>
                                        <select id="projects_select" 
                                                multiple
                                                class="form-control @error('projects') is-invalid @enderror"
                                                name="projects[]">
                                            {{-- Projects will be dynamically loaded here --}}
                                            <option value="" disabled>Select a program first to see available projects</option>
                                        </select>
                                        @error('projects')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        @error('projects.*')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text mt-1">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Only projects related to selected program are shown
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================================ --}}
                        {{-- SECTION 3: DATES AND VENUE --}}
                        {{-- ================================ --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Dates and Venue</h6>
                                <span class="text-muted small">Schedule and location details</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    {{-- Dates --}}
                                    <div class="col-md-6">
                                        <label for="start_date" class="form-label fw-semibold">
                                            Start Date <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="start_date" id="start_date" 
                                               class="form-control @error('start_date') is-invalid @enderror" 
                                               value="2024-01-15" required>
                                        @error('start_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="end_date" class="form-label fw-semibold">End Date</label>
                                        <input type="date" name="end_date" id="end_date" 
                                               class="form-control @error('end_date') is-invalid @enderror" 
                                               value="2024-01-17">
                                        @error('end_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Venue Dropdown --}}
                                    <div class="col-md-12">
                                        <label for="venue" class="form-label fw-semibold">Venue</label>
                                        <select name="venue" id="venue" 
                                               class="form-control form-select @error('venue') is-invalid @enderror">
                                            <option value="">Select Venue</option>
                                            <option value="Hariri Foundation Headquarters" selected>Hariri Foundation Headquarters</option>
                                            <option value="Rafic Hariri High School">Rafic Hariri High School</option>
                                            <option value="Hajj Bahaa Hariri High School">Hajj Bahaa Hariri High School</option>
                                            <option value="Hariri Social & Medical Center">Hariri Social & Medical Center</option>
                                            <option value="Hariri Foundation Vocational & Technical Training Center">Hariri Foundation Vocational & Technical Training Center</option>
                                            <option value="Khan al Franj">Khan al Franj</option>
                                            <option value="Outreach & Leadership Academy">Outreach & Leadership Academy</option>
                                            <option value="National State Academy">National State Academy</option>
                                        </select>
                                        @error('venue')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================================== --}}
                        {{-- SECTION 4: CONTENT AND NETWORK --}}
                        {{-- ================================== --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Content and Network</h6>
                                <span class="text-muted small">Activity description and reporting</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    {{-- Content Network --}}
                                    <div class="col-md-12">
                                        <label for="content_network" class="form-label fw-semibold">Content / Network</label>
                                        <textarea name="content_network" id="content_network" 
                                                  class="form-control @error('content_network') is-invalid @enderror" 
                                                  rows="3" 
                                                  placeholder="Describe the activity content and network...">Training program for school leaders focusing on management skills.</textarea>
                                        @error('content_network')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ====================================== --}}
{{-- SECTION 5: REPORTING ACTIVITIES (Connected to Database) --}}
{{-- ====================================== --}}
<div class="section-card mb-4">
    <div class="section-header">
        <h6 class="mb-0 fw-semibold">Reporting Activities</h6>
        <span class="text-muted small">Select reporting component and activities</span>
    </div>
    <div class="section-body">
        <div class="row g-3">
            {{-- RP Components Dropdown --}}
            <div class="col-md-12">
                <label for="rp_component_id" class="form-label fw-semibold mb-2">Reporting Component</label>
                <select id="rp_component_id" 
                        class="form-control form-select @error('rp_component_id') is-invalid @enderror"
                        name="rp_component_id">
                    <option value="">Select a Reporting Component</option>
                    @foreach($rpComponents as $component)
                        <option value="{{ $component->rp_components_id }}" 
                                {{ old('rp_component_id') == $component->rp_components_id ? 'selected' : '' }}>
                            {{ $component->code }} - {{ $component->name }}
                        </option>
                    @endforeach
                </select>
                @error('rp_component_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="form-text mt-1">
                    <i class="bi bi-info-circle me-1"></i>
                    Select a reporting component to see related activities
                </div>
            </div>

            {{-- RP Activities Multi-Select (Grouped by Actions) --}}
            <div class="col-md-12">
                <label for="rp_activities_select" class="form-label fw-semibold mb-2">Reporting Activities</label>
                <select id="rp_activities_select" 
                        multiple
                        class="form-control @error('rp_activities') is-invalid @enderror"
                        name="rp_activities[]">
                    <option value="" disabled>Select a reporting component first</option>
                </select>
                @error('rp_activities')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @error('rp_activities.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="form-text mt-1">
                    <i class="bi bi-info-circle me-1"></i>
                    Activities are grouped by their parent actions
                </div>
            </div>
        </div>
    </div>
</div>

                        {{-- ====================================== --}}
                        {{-- SECTION 6: FOCAL POINTS (Multiple Select) --}}
                        {{-- ====================================== --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Focal Points</h6>
                                <span class="text-muted small">Select one or more focal points</span>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-0">
                                            <label for="focal_points_select" class="form-label fw-semibold mb-2">Select Focal Points</label>
                                            <select id="focal_points_select" 
                                                    multiple
                                                    class="form-control @error('focal_points') is-invalid @enderror"
                                                    name="focal_points[]">
                                                @php
                                                    // Reduced to 4 key focal points
                                                    $focalPoints = [
                                                         ['id' => 1, 'name' => 'Mohamad Ismail'],
                                                        ['id' => 2, 'name' => 'Mohammad Harriri'],
                                                        ['id' => 3, 'name' => 'Lilia Chahine'],
                                                        ['id' => 4, 'name' => 'Nadine Zaidan'],
                                                        ['id' => 5, 'name' => 'Hatem Assii'],
                                                        ['id' => 6, 'name' => 'Ahmad Chami'],
                                                    ];
                                                @endphp
                                                @foreach($focalPoints as $point)
                                                    <option value="{{ $point['id'] }}" 
                                                            {{ in_array($point['id'], [1, 2]) ? 'selected' : '' }}>
                                                        {{ $point['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('focal_points')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            @error('focal_points.*')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================== --}}
                        {{-- SECTION 7: OPERATIONAL SUPPORT REQUIRED (Checkboxes) --}}
                        {{-- ========================================== --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Operational Support Required</h6>
                                <span class="text-muted small">Select required support types (multiple selection)</span>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label fw-semibold mb-2 d-block">Select Required Support</label>
                                            <div class="row">
                                                <div class="col-md-6 col-lg-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input @error('operational_support') is-invalid @enderror" 
                                                               type="checkbox" 
                                                               name="operational_support[]" 
                                                               id="support_logistics" 
                                                               checked
                                                               value="Logistics"
                                                               {{ is_array(old('operational_support')) && in_array('Logistics', old('operational_support')) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="support_logistics">
                                                            Logistics
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input @error('operational_support') is-invalid @enderror" 
                                                               type="checkbox" 
                                                               name="operational_support[]" 
                                                               id="support_media" 
                                                               value="Media"
                                                               {{ is_array(old('operational_support')) && in_array('Media', old('operational_support')) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="support_media">
                                                            Media
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input @error('operational_support') is-invalid @enderror" 
                                                               type="checkbox" 
                                                               name="operational_support[]" 
                                                               id="support_pr" 
                                                               value="Public Relations"
                                                               {{ is_array(old('operational_support')) && in_array('Public Relations', old('operational_support')) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="support_pr">
                                                            Public Relations
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-6 col-lg-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input @error('operational_support') is-invalid @enderror" 
                                                               type="checkbox" 
                                                               name="operational_support[]" 
                                                               id="support_none" 
                                                               value="None">
                                                        <label class="form-check-label" for="support_none">
                                                            None
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            @error('operational_support')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            @error('operational_support.*')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ======================== --}}
                        {{-- SECTION 8: ACTION BUTTONS --}}
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
                                        <button type="submit" class="btn btn-success" id="submitBtn">
                                            <i class="bi bi-check-circle me-1"></i> Update Activity
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
    /* ============================================= */
/* ENHANCED SELECT2 STYLING - PROFESSIONAL LOOK */
/* ============================================= */

/* 1. BASE & RESET */
* {
    box-sizing: border-box;
}

:root {
    --primary-border: #dee2e6;
    --focus-border: #86b7fe;
    --focus-shadow: rgba(13, 110, 253, 0.25);
    --invalid-color: #dc3545;
    --valid-color: #198754;
    --placeholder-color: #6c757d;
    --text-color: #495057;
    --bg-light: #f8f9fa;
    --transition-speed: 0.15s;
}

/* 2. BASE SELECT2 CONTAINERS */
.select2-container {
    width: 100% !important;
    min-height: 42px;
    margin-top: 0.25rem;
    z-index: 1055 !important;
}

/* 3. SINGLE SELECT STYLING */
.select2-container--default .select2-selection--single {
    background-color: #fff;
    border: 1px solid var(--primary-border);
    border-radius: 6px;
    height: 42px;
    display: flex;
    align-items: center;
    transition: all var(--transition-speed) ease-in-out;
}

.select2-container--default .select2-selection--single:hover {
    border-color: #adb5bd;
}

/* 4. MULTIPLE SELECT STYLING */
.select2-container--default .select2-selection--multiple {
    display: flex;
    align-items: center;
    
}

.select2-container--default .select2-selection--multiple:hover {
    border-color: #adb5bd;
}

/* 5. FOCUS STATES (Consolidated) */
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: var(--focus-border);
    box-shadow: 0 0 0 0.2rem var(--focus-shadow);
    outline: 0;
}

/* 6. TEXT & PLACEHOLDER STYLING */
.select2-container--default .select2-selection__rendered {
    color: var(--text-color);
    font-size: 0.95rem;
    line-height: 1.5;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    padding-left: 12px;
    padding-right: 30px;
}

.select2-container--default .select2-selection--multiple .select2-selection__placeholder {
    position: static;
    transform: none;
    line-height: normal;
    margin: 0;
}

/* 7. DROPDOWN STYLING */
.select2-container--default .select2-dropdown {
    border: 1px solid var(--primary-border);
    border-radius: 6px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    margin-top: 4px;
    z-index: 1060 !important;
}

/* 8. OPTIONS & OPTGROUPS */
.select2-container--default .select2-results__option {
    padding: 8px 12px;
    font-size: 0.95rem;
    color: var(--text-color);
    transition: background-color var(--transition-speed);
}

.select2-container--default .select2-results__option[aria-selected=true] {
    background-color: var(--bg-light);
    color: var(--text-color);
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #e9ecef;
    color: var(--text-color);
}

.select2-container--default .select2-results__group {
    background-color: var(--bg-light);
    color: var(--text-color);
    font-size: 0.95rem;
    font-weight: 600;
    padding: 8px 12px;
    border-bottom: 1px solid #e9ecef;
    cursor: default;
}

/* 9. SELECTED TAGS (Multiple Select) */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: var(--bg-light);
    border: 1px solid #e9ecef;
    border-radius: 4px;
    color: var(--text-color);
    font-size: 0.95rem;
    font-weight: 500;
    padding: 3px 8px;
    display: flex;
    align-items: center;
    margin: 2px 0;
}

/* 10. VALIDATION STATES */
.select2-container--default .select2-selection--single.is-invalid,
.select2-container--default .select2-selection--multiple.is-invalid {
    border-color: var(--invalid-color) !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    padding-right: calc(1.5em + 0.75rem);
}

.select2-container--default.select2-container--focus .select2-selection--single.is-invalid,
.select2-container--default.select2-container--focus .select2-selection--multiple.is-invalid {
    border-color: var(--invalid-color) !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* 11. CUSTOM SCROLLBAR */
.select2-results__options {
    max-height: 280px;
    overflow-y: auto;
}

.select2-results__options::-webkit-scrollbar {
    width: 8px;
}

.select2-results__options::-webkit-scrollbar-track {
    background: var(--bg-light);
    border-radius: 4px;
}

.select2-results__options::-webkit-scrollbar-thumb {
    background: #adb5bd;
    border-radius: 4px;
}

.select2-results__options::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
}

/* 12. FORM CONTROLS ENHANCEMENT */
.form-control, .form-select {
    border: 1px solid var(--primary-border);
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    font-size: 0.95rem;
    color: var(--text-color);
    background-color: #fff;
    transition: all 0.3s ease;
    min-height: 42px;
}

.form-control:focus, .form-select:focus {
    border-color: var(--focus-border);
    box-shadow: 0 0 0 0.25rem var(--focus-shadow);
    outline: 0;
}

/* 13. SECTION CARDS IMPROVEMENT */
.section-card {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    overflow: hidden;
    margin-bottom: 1.5rem;
    transition: box-shadow 0.3s ease;
}

.section-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.section-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e0e0e0;
}

.section-body {
    padding: 1.5rem;
}

/* 14. BUTTON IMPROVEMENTS */
.btn-success {
    background: linear-gradient(135deg, #198754 0%, #157347 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    color: white;
    transform: translateY(-1px);
}

/* 15. CHECKBOX & RADIO IMPROVEMENT */
.form-check-input {
    width: 1.1em;
    height: 1.1em;
    margin-top: 0.15em;
    margin-right: 0.5em;
    border: 2px solid var(--primary-border);
    cursor: pointer;
}

.form-check-input:checked {
    background-color: var(--valid-color);
    border-color: var(--valid-color);
}

.form-check-input:focus {
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    border-color: var(--valid-color);
}

/* 16. FORM VALIDATION ENHANCEMENT */
.is-invalid {
    border-color: var(--invalid-color) !important;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
    20%, 40%, 60%, 80% { transform: translateX(2px); }
}

.invalid-feedback {
    color: var(--invalid-color);
    font-size: 0.875rem;
    margin-top: 0.25rem;
    font-weight: 500;
}

/* 17. LOADING STATES */
.btn.loading {
    position: relative;
    color: transparent;
    pointer-events: none;
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

/* 18. ARROW STYLING (Optimized) */
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    position: absolute;
    top: 1px;
    right: 1px;
    width: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #adb5bd transparent transparent transparent;
    border-style: solid;
    border-width: 5px 4px 0 4px;
    margin-top: -2px;
    transition: border-color 0.2s ease;
}

/* 19. REMOVE BUTTON HOVER EFFECT */
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: var(--invalid-color);
    background-color: transparent;
    transform: scale(1.1);
}

/* 20. RESPONSIVE IMPROVEMENTS */
@media (max-width: 768px) {
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        font-size: 0.8rem;
        padding: 2px 6px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    width: 100%;
}
    
    .section-card {
        border-radius: 8px;
    }
    
    .section-header,
    .section-body {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .form-control, .form-select {
        font-size: 0.875rem;
    }
    
    .form-label.fw-semibold {
        font-size: 0.9rem;
    }
}

/* 21. DISABLED STATE ENHANCEMENT */
.select2-container--default .select2-selection--single[aria-disabled=true],
.select2-container--default .select2-selection--multiple[aria-disabled=true] {
    background-color: #e9ecef;
    opacity: 0.7;
    cursor: not-allowed;
}

/* 22. CLEAR BUTTON STYLING */
.select2-container--default .select2-selection__clear {
    color: var(--placeholder-color);
    font-size: 1rem;
    transition: color 0.2s ease;
}

.select2-container--default .select2-selection__clear:hover {
    color: var(--invalid-color);
}

/* 23. SEARCH FIELD IMPROVEMENT */
.select2-container--default .select2-search--inline .select2-search__field {
    color: var(--text-color);
    font-size: 0.95rem;
    margin-top: 4px;
    min-height: 28px;
    padding: 0 4px;
    font-family: inherit;
}

.select2-container--default .select2-search--inline .select2-search__field::placeholder {
    color: var(--placeholder-color);
    opacity: 0.7;
}

/* 24. SMOOTH TRANSITIONS */
.select2-container * {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* 25. ACCESSIBILITY IMPROVEMENTS */
.select2-container--default .select2-selection--single:focus,
.select2-container--default .select2-selection--multiple:focus {
    outline: 2px solid var(--focus-border);
    outline-offset: 2px;
}

/* Hide default dropdown arrow for consistency */
.form-select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
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
        // Complete Program-Project relationship mapping based on provided data
        const programProjects = {
            // PROG001: Rafic Hariri High School
            'PROG001': [
                {id: 'PROG001-P01', text: 'PROG001-P01 - Rafic Hariri Technical Institute', subProgram: null},
                {id: 'PROG001-P02', text: 'PROG001-P02 - RHHS Teachers & Staff Capacity Building', subProgram: null}
            ],
            
            // PROG002: Hajj Bahaa Hariri High School
            'PROG002': [
                {id: 'PROG002-P01', text: 'PROG002-P01 - HBHS Teachers & Staff Capacity Building', subProgram: null},
                {id: 'PROG002-P02', text: 'PROG002-P02 - IB PYP Accreditation', subProgram: null}
            ],
            
            // PROG010: School Network of Saida & Neighboring Towns
            'PROG010': [
                {id: 'PROG010-P01', text: 'PROG010-P01 - Remedial Education Courses', subProgram: null},
                {id: 'PROG010-P02', text: 'PROG010-P02 - Educational Conference of Saida & Neighbouring Towns', subProgram: null}
            ],
            
            // PROG019: National State Academy (with sub-programs)
            'PROG019': [
                // Sub-program: PROG020 - National State University Academy
                {id: 'PROG020-P01', text: 'PROG020-P01 - University Academy Legal Registration', subProgram: 'PROG020'},
                
                // Sub-program: PROG021 - National State Forum
                {id: 'PROG021-P01', text: 'PROG021-P01 - Saida Discusses the Ministerial Statement', subProgram: 'PROG021'},
                {id: 'PROG021-P02', text: 'PROG021-P02 - Readings in the Inaugural Speech', subProgram: 'PROG021'},
                
                // Sub-program: PROG022 - Prevention of Violent Extremism Program
                {id: 'PROG022-P01', text: 'PROG022-P01 - Rafic Hariri Forum for PVE', subProgram: 'PROG022'},
                {id: 'PROG022-P02', text: 'PROG022-P02 - Hariri Foundation Award for PVE', subProgram: 'PROG022'},
                {id: 'PROG023-P03', text: 'PROG023-P03 - Early Warning Network for PVE - Saida', subProgram: 'PROG022'},
                {id: 'PROG023-P04', text: 'PROG023-P04 - Trainings - The State and PVE', subProgram: 'PROG022'}
            ]
        };

        // Sub-program display names
        const subProgramDisplayNames = {
            'PROG020': 'National State University Academy',
            'PROG021': 'National State Forum',
            'PROG022': 'Prevention of Violent Extremism Program'
        };

        // Initialize the single select for programs
        $('#programs_select').select2({
            placeholder: 'Select a program...',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 10
        });

        // Initialize projects select with empty placeholder
        $('#projects_select').select2({
            placeholder: 'Select a project...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            multiple: true
        });
       // Initialize RP Components Select2
        $('#rp_component_id').select2({
            placeholder: 'Select a reporting component...',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 10
        });


        // Initialize the custom multiple select for reporting activities
       $('#rp_activities_select').select2({
    placeholder: 'Select reporting activities...',
    allowClear: true,
    width: '100%',
    closeOnSelect: false,
    tags: false,
    multiple: true
});
        
        // Initialize the custom multiple select for focal points
        $('#focal_points_select').select2({
            placeholder: 'Select focal points...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            tags: false,
            multiple: true
        });
// AJAX function to load RP Activities based on selected component
        function loadRPActivitiesByComponent(componentId) {
            const activitiesSelect = $('#rp_activities_select');
            
            if (!componentId) {
                activitiesSelect.empty();
                activitiesSelect.append('<option value="" disabled>Select a reporting component first</option>');
                activitiesSelect.trigger('change');
                
                activitiesSelect.select2({
                    placeholder: 'Select a reporting component first',
                    allowClear: true,
                    width: '100%',
                    closeOnSelect: false,
                    multiple: true
                });
                return;
            }
            
            // Show loading state
            activitiesSelect.empty();
            activitiesSelect.append('<option value="">Loading activities...</option>');
            activitiesSelect.prop('disabled', true);
            activitiesSelect.trigger('change');
            
            // Get CSRF token
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            // AJAX call to fetch activities
            $.ajax({
                url: '{{ route("activities.get-rp-activities") }}',
                type: 'GET',
                data: {
                    component_id: componentId,
                    _token: csrfToken
                },
                cache: false,
                success: function(response) {
                    console.log('AJAX Response:', response);
                    
                    activitiesSelect.empty();
                    
                    if (response.success && response.data && response.data.length > 0) {
                        // Group activities by action
                        const groupedActivities = {};
                        
                        response.data.forEach(activity => {
                            const actionId = activity.rp_action_id || 'general';
                            const actionName = activity.action_name || 'General Activities';
                            
                            if (!groupedActivities[actionId]) {
                                groupedActivities[actionId] = {
                                    name: actionName,
                                    activities: []
                                };
                            }
                            
                            groupedActivities[actionId].activities.push({
                                id: activity.rp_activities_id,
                                text: activity.code + ' - ' + activity.name
                            });
                        });
                        
                        // Create optgroups for each action
                        Object.keys(groupedActivities).forEach(actionId => {
                            const group = groupedActivities[actionId];
                            
                            // Sort activities alphabetically
                            group.activities.sort((a, b) => a.text.localeCompare(b.text));
                            
                            // Create optgroup for this action
                            const actionGroup = $('<optgroup>').attr('label', group.name);
                            group.activities.forEach(activity => {
                                actionGroup.append($('<option>').val(activity.id).text(activity.text));
                            });
                            activitiesSelect.append(actionGroup);
                        });
                        
                        // Re-initialize Select2 with new options
                        activitiesSelect.select2({
                            placeholder: 'Select reporting activities...',
                            allowClear: true,
                            width: '100%',
                            closeOnSelect: false,
                            multiple: true,
                            dropdownAutoWidth: true
                        });
                        
                    } else {
                        console.log('No activities found for component');
                        activitiesSelect.append('<option value="">No activities found for this component</option>');
                        activitiesSelect.select2({
                            placeholder: 'No activities available',
                            allowClear: true,
                            width: '100%',
                            closeOnSelect: false,
                            multiple: true
                        });
                    }
                    
                    activitiesSelect.prop('disabled', false);
                    activitiesSelect.trigger('change');
                    
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', xhr.responseText);
                    
                    activitiesSelect.empty();
                    activitiesSelect.append('<option value="">Error loading activities. Please try again.</option>');
                    activitiesSelect.prop('disabled', false);
                    
                    // Reinitialize Select2
                    activitiesSelect.select2({
                        placeholder: 'Error loading activities',
                        allowClear: true,
                        width: '100%',
                        closeOnSelect: false,
                        multiple: true
                    });
                    
                    activitiesSelect.trigger('change');
                }
            });
        }
        // Function to update projects based on selected program
        function updateProjectsBasedOnProgram() {
            const selectedProgram = $('#programs_select').val();
            const projectsSelect = $('#projects_select');
            
            // Clear current projects
            projectsSelect.empty();
            
            if (!selectedProgram) {
                projectsSelect.append('<option value="">Select a program first to see available projects</option>');
                projectsSelect.trigger('change');
                
                // Update Select2 to show placeholder properly
                projectsSelect.select2({
                    placeholder: 'Select a program first to see available projects',
                    allowClear: true,
                    width: '100%',
                    closeOnSelect: false,
                    multiple: true
                });
                return;
            }
            
            // Get projects for selected program
            const projects = programProjects[selectedProgram] || [];
            
            if (projects.length === 0) {
                projectsSelect.append('<option value="">No projects available for selected program</option>');
            } else {
                // Group projects by sub-program
                const groupedProjects = {};
                projects.forEach(project => {
                    const groupKey = project.subProgram || 'general';
                    if (!groupedProjects[groupKey]) {
                        groupedProjects[groupKey] = [];
                    }
                    groupedProjects[groupKey].push(project);
                });
                
                // Create optgroups for better organization
                
                // First, add general projects (not under any sub-program)
                if (groupedProjects['general'] && groupedProjects['general'].length > 0) {
                    // Sort general projects alphabetically
                    groupedProjects['general'].sort((a, b) => a.text.localeCompare(b.text));
                    
                    // Create optgroup for general projects
                    const generalGroup = $('<optgroup>').attr('label', 'Direct Projects');
                    groupedProjects['general'].forEach(project => {
                        generalGroup.append($('<option>').val(project.id).text(project.text));
                    });
                    projectsSelect.append(generalGroup);
                }
                
                // Add projects grouped by sub-program
                Object.keys(groupedProjects).forEach(groupKey => {
                    if (groupKey !== 'general' && groupedProjects[groupKey].length > 0) {
                        const groupName = subProgramDisplayNames[groupKey] || groupKey;
                        
                        // Sort projects in this group alphabetically
                        groupedProjects[groupKey].sort((a, b) => a.text.localeCompare(b.text));
                        
                        // Create optgroup for this sub-program
                        const subProgramGroup = $('<optgroup>').attr('label', groupName);
                        groupedProjects[groupKey].forEach(project => {
                            subProgramGroup.append($('<option>').val(project.id).text(project.text));
                        });
                        projectsSelect.append(subProgramGroup);
                    }
                });
            }
            // Add RP Components and Activities to Select2 validation
        $('#rp_component_id, #rp_activities_select').on('change', function() {
            if ($(this).val() && $(this).val().length > 0) {
                $(this).removeClass('is-invalid');
            }
        });
            // Re-initialize Select2 with proper configuration for optgroups
            projectsSelect.select2({
                placeholder: 'Select projects...',
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
                multiple: true,
                templateResult: function(data) {
                    // If it's an optgroup (no id), return the text
                    if (!data.id) {
                        return data.text;
                    }
                    return data.text;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            });
            
            projectsSelect.trigger('change');
        }

        // Update projects when program is selected
        $('#programs_select').on('change', updateProjectsBasedOnProgram);

        // Form validation
        const form = document.getElementById('activityForm');
        const submitBtn = document.getElementById('submitBtn');
        const resetBtn = document.getElementById('resetBtn');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                } else {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                }
                
                form.classList.add('was-validated');
            });
        }
        
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                if (form) {
                    form.classList.remove('was-validated');
                    const invalidFields = form.querySelectorAll('.is-invalid');
                    invalidFields.forEach(field => {
                        field.classList.remove('is-invalid');
                    });
                }
                
                // Reset Select2 fields
                $('#programs_select').val(null).trigger('change');
                $('#projects_select').val(null).trigger('change');
                $('#reporting_activities_select').val(null).trigger('change');
                $('#focal_points_select').val(null).trigger('change');
                
                // Reset checkboxes
                const checkboxes = form.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        }
        
        // Real-time validation
        if (form) {
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                field.addEventListener('blur', validateField);
                field.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        }
        
        // Event listener for component change
        $('#rp_component_id').on('change', function() {
            const componentId = $(this).val();
            console.log('Component changed to:', componentId);
            loadRPActivitiesByComponent(componentId);
        });

        // Load activities if component is already selected on page load
        $(document).ready(function() {
            // Store currently selected activities for reference
            const selectedActivityIds = [];
            $('#rp_activities_select option:selected').each(function() {
                selectedActivityIds.push($(this).val());
            });
            
            if (selectedActivityIds.length > 0) {
                console.log('Pre-selected activities:', selectedActivityIds);
            }
            
            // Load activities if component is already selected
            const initialComponentId = $('#rp_component_id').val();
            if (initialComponentId) {
                console.log('Loading activities for initial component:', initialComponentId);
                // Small delay to ensure DOM is ready
                setTimeout(() => {
                    loadRPActivitiesByComponent(initialComponentId);
                }, 300);
            }
        });
        
        function validateField() {
            if (!this.value.trim()) {
                this.classList.add('is-invalid');
                return false;
            } else {
                this.classList.remove('is-invalid');
                return true;
            }
        }
        
        // Select2 validation
        $('#programs_select, #projects_select, #reporting_activities_select, #focal_points_select').on('change', function() {
            if ($(this).val() && $(this).val().length > 0) {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Checkbox validation
        const checkboxes = document.querySelectorAll('input[name="operational_support[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.classList.remove('is-invalid');
                });
            });
        });

        // Handle "None" checkbox logic
        const noneCheckbox = document.getElementById('support_none');
        if (noneCheckbox) {
            noneCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    checkboxes.forEach(cb => {
                        if (cb.id !== 'support_none') {
                            cb.checked = false;
                        }
                    });
                }
            });

            checkboxes.forEach(cb => {
                if (cb.id !== 'support_none') {
                    cb.addEventListener('change', function() {
                        if (this.checked) {
                            noneCheckbox.checked = false;
                        }
                    });
                }
            });
        }
    });
</script> 
@endsection