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
                        {{-- SECTION 5: REPORTING ACTIVITIES (STATIC DATA WITH ACTIONS AS TITLES) --}}
                        {{-- ====================================== --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Reporting Activities</h6>
                                <span class="text-muted small">Select reporting component and activities (grouped by Actions)</span>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    {{-- RP Components Dropdown (STATIC) --}}
                                    <div class="col-md-12">
                                        <label for="rp_component_id" class="form-label fw-semibold mb-2">Reporting Component</label>
                                        <select id="rp_component_id" 
                                                class="form-control form-select @error('rp_component_id') is-invalid @enderror"
                                                name="rp_component_id">
                                            <option value="">Select a Reporting Component</option>
                                            <option value="b4848eb6-f46d-40f9-bcac-58736ae015fe">AD.A - برامج مؤسسة الحريري التربوية والإنمائية</option>
                                            <option value="7458b148-2dc6-4811-acd9-eea3ec5c04a0">AD.B - المساعدة الاجتماعية والصحية والتعليمية</option>
                                            <option value="63f1df7a-42b5-4062-950b-589c66584d23">AD.C - أكاديمية الدولة الوطنية</option>
                                            <option value="cb9f2e6c-3823-49e1-aab1-973cbb80351f">AD.D - منتدى شباب نهوض لبنان</option>
                                            <option value="a96ae126-80b6-454f-bf0d-c14f95b06442">AD.E - منصة "إنمائية"</option>
                                        </select>
                                        @error('rp_component_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text mt-1">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Select a reporting component to see related activities grouped by their Actions
                                        </div>
                                    </div>

                                    {{-- RP Activities Multi-Select (STATIC - Grouped by Actions) --}}
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
                                            Activities are grouped under their parent Action titles
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
                                                <option value="1" selected>Mohamad Ismail</option>
                                                <option value="2" selected>Mohammad Harriri</option>
                                                <option value="3">Lilia Chahine</option>
                                                <option value="4">Nadine Zaidan</option>
                                                <option value="5">Hatem Assii</option>
                                                <option value="6">Ahmad Chami</option>
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
                                                               value="Logistics">
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
                                                               value="Media">
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
                                                               value="Public Relations">
                                                        <label class="form-check-label" for="support_pr">
                                                            Public Relations
                                                        </label>
                                                    </div>
                                                </div>
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
    
    .form-label.fw-semibold {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        color: #495057;
        font-weight: 600;
    }
    
    .form-check-input {
        width: 1.1em;
        height: 1.1em;
        margin-top: 0.15em;
        margin-right: 0.5em;
        border: 2px solid #dee2e6;
    }
    
    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }
    
    .form-check-label {
        font-size: 0.95rem;
        color: #495057;
        font-weight: 500;
    }
    
    .select2-container--default .select2-selection--multiple,
    .select2-container--default .select2-selection--single {
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        padding: 0.25rem 0.5rem !important;
        background-color: #fff !important;
        transition: all 0.3s ease !important;
        font-size: 0.95rem !important;
        min-height: 42px !important;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
        outline: 0 !important;
    }
    
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
    
    .select2-container--default .select2-dropdown {
        border: 1px solid #dee2e6 !important;
        border-radius: 0 0 8px 8px !important;
        margin-top: -1px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }
    
    .select2-container--default .select2-results__option[disabled] {
        background-color: #f8f9fa !important;
        color: #6c757d !important;
        font-weight: 600 !important;
        cursor: default !important;
        border-top: 1px solid #dee2e6 !important;
        margin-top: 5px !important;
    }
    
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
    
    .select2-container--default .select2-selection--multiple.is-invalid,
    .select2-container--default .select2-selection--single.is-invalid {
        border-color: #dc3545 !important;
    }
    
    .form-check-input.is-invalid {
        border-color: #dc3545 !important;
    }
    
    .form-control:not(textarea) {
        height: 42px;
    }
    
    .select2-container .select2-selection--multiple,
    .select2-container .select2-selection--single {
        min-height: 42px;
        height: auto;
    }
    
    .select2-container {
        z-index: 1055 !important;
    }
    
    .select2-dropdown {
        z-index: 1060 !important;
    }
    
    .select2-results__group {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
        padding: 8px 12px;
        border-bottom: 1px solid #dee2e6;
        margin-top: 5px;
    }
</style>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // STATIC RP COMPONENTS AND ACTIVITIES DATA WITH ACTIONS AS TITLES
        const rpComponentsData = {
            // AD.A - برامج مؤسسة الحريري التربوية والإنمائية
            'b4848eb6-f46d-40f9-bcac-58736ae015fe': {
                name: 'AD.A - برامج مؤسسة الحريري التربوية والإنمائية',
                actions: [
                    {
                        title: 'إجراء تدريب للهيئات العاملة في المدرسة حول استراتيجيات وسبل التدريس',
                        activities: [
                            {id: 'activity-aa-1', text: 'تدريبات لهيئة القيادة البيداغولوجية في المدرسة بالتعاون مع خبراء وجهات تدريبية متخصصة'},
                            {id: 'activity-aa-2', text: 'إجراء دورة تدريبية للهيئة التعليمية في الثانوية بالتعاون مع خبراء وجهات تدريبية متخصصة'}
                        ]
                    },
                    {
                        title: 'مواصلة التقديم للحصول على اعتماد NEASC',
                        activities: [
                            {id: 'activity-aa-3', text: 'تغطية التكاليف اللوجستية للزيارة التفقدية للجنة اعتماد جمعية نيو إنجلاند للمدارس والكليات NEASC'}
                        ]
                    },
                    {
                        title: 'تأهيل وتحديث مباني الحرم المدرسي من أجل تطوير البنية التحتية التعليمية وتأمين السلامة العامة',
                        activities: [
                            {id: 'activity-aa-4', text: 'تنفيذ أعمال صيانة شاملة ضمن مباني القسم الابتدائي والقسم المتوسط والإدارة'},
                            {id: 'activity-aa-5', text: 'تنفيذ أعمال صيانة شاملة (ترميم ودهان وجلي بلاط) ضمن مباني القسم الابتدائي والقسم المتوسط والإدارة'}
                        ]
                    },
                    {
                        title: 'مواصلة تجهيز الفصول الدراسية لتعزيز التعليم الرقمي في المدرسة',
                        activities: [
                            {id: 'activity-aa-6', text: 'تجهيز فصلين دراسيين بأجهزة العرض الذكي والتفاعلي'},
                            {id: 'activity-aa-7', text: 'تجهيز 6 فصول دراسية بأجهزة العرض الذكي والتفاعلي'}
                        ]
                    },
                    {
                        title: 'إنشاء وتجهيز فصول دراسية جديدة وإطلاق برامج تعليمية جديدة في معهد رفيق الحريري التقني',
                        activities: [
                            {id: 'activity-aa-8', text: 'إنشاء فصلين جديدين وتوفير التجهيزات اللوجستية والتقنية اللازمة'},
                            {id: 'activity-aa-9', text: 'تطوير برامج تعليمية جديدة والتعليمية للتعليم التقني عبر إعداد مناهج الجديدة'},
                            {id: 'activity-aa-10', text: 'الحصول على الترخيص من مديرية التعليم المهني والتقني في وزارة التربية والتعليم العالي'},
                            {id: 'activity-aa-11', text: 'إطلاق البرامج الجديدة وفتح باب الانتساب'}
                        ]
                    },
                    {
                        title: 'إجراء حملة إعلامية وتشاركية لتعميم مبادئ التعليم المهني والتقني الخاص والتسويق لبرامج معهد رفيق الحريري التقني',
                        activities: [
                            {id: 'activity-aa-12', text: 'حملة إعلامية وتشاركية عبارة عن عدة منشورات على اللوحات الإعلانية وصفحات التواصل الاجتماعي'}
                        ]
                    },
                    {
                        title: 'التقديم للحصول على اعتماد برنامج البكالوريا الدولية للمرحلة الابتدائية',
                        activities: [
                            {id: 'activity-aa-13', text: 'تقديم طلب الترشيح للحصول على اعتماد برنامج البكالوريا الدولية للمرحلة الابتدائية IB PYP'},
                            {id: 'activity-aa-14', text: 'عقد إجتماعات تنسيقية وورش عمل تدريبية لدمج معايير الـIB في التعليم الابتدائي PYP'}
                        ]
                    },
                    {
                        title: 'تجهيز مختبر للتعليم على فنون الموسيقى',
                        activities: [
                            {id: 'activity-aa-15', text: 'تجهيز المختبر بآلات موسيقية أساسية'},
                            {id: 'activity-aa-16', text: 'توفير أجهزة صوت وتقنيات تسجيل رقمية'}
                        ]
                    },
                    {
                        title: 'إجراء حملة إعلامية وتشاركية لتسويق برامج الثانوية',
                        activities: [
                            {id: 'activity-aa-17', text: 'تطوير وتنفيذ حملة إعلامية وتشاركية حول المستوى التعليمي والبيئة التعلمية في الثانوية'},
                            {id: 'activity-aa-18', text: 'تطوير وتنفيذ فيلم وثائقي'},
                            {id: 'activity-aa-19', text: 'تأليف وتسجيل وتعميم نشيد خاص بالثانوية'}
                        ]
                    },
                    {
                        title: 'إنشاء منصة إلكترونية للثانوية لتحسين التواصل',
                        activities: [
                            {id: 'activity-aa-20', text: 'تجهيز المدرسة بنظام Eduware لتنظيم ومتابعة العملية التعليمية'},
                            {id: 'activity-aa-21', text: 'تصميم وتطوير منصة إلكترونية متعددة المستخدمين تسمح بمتابعة العملية التعليمية والأنشطة اللامنهجية'}
                        ]
                    }
                ]
            },
            // AD.B - المساعدة الاجتماعية والصحية والتعليمية
            '7458b148-2dc6-4811-acd9-eea3ec5c04a0': {
                name: 'AD.B - المساعدة الاجتماعية والصحية والتعليمية',
                actions: [
                    {
                        title: 'القيام بحملة للتوعية والتشخيص والوقاية الصحية',
                        activities: [
                            {id: 'activity-ab-1', text: 'جلسات توعوية وأيام صحية في المركز وفي المجتمعات المحلية'}
                        ]
                    },
                    {
                        title: 'إجراء دورات تدريبية متخصصة للكادر الصحي والطبي',
                        activities: [
                            {id: 'activity-ab-2', text: 'دورات تدريبية بالاستعانة بخبراء طبيين'}
                        ]
                    },
                    {
                        title: 'إجراء حملة إلكترونية للمركز حول الخدمات وأفضل الممارسات',
                        activities: [
                            {id: 'activity-ab-3', text: 'حملة إلكترونية توعوية عبارة عن عدة منشورات على صفحات التواصل الاجتماعي على Facebook وInstagram'}
                        ]
                    },
                    {
                        title: 'توفير خدمة العيادات النقالة المجانية',
                        activities: [
                            {id: 'activity-ab-4', text: 'عيادة نقالة عبارة عن فريق متخصص جوال (طبيب صحة عامة وممرض/ة وفريق تطوعي)'},
                            {id: 'activity-ab-5', text: 'زيارة مراكز في المجتمعات المحلية بحسب جدول زمني يحدّد أسبوعياً'}
                        ]
                    },
                    {
                        title: 'توسيع فريق الجهاز الطبي المتنقل وتوفير التجهيزات اللازمة',
                        activities: [
                            {id: 'activity-ab-6', text: 'تدريب 15 مسعف/ة جدد للانضمام إلى فريق عمل الجهاز عبر برامج تدريبية (إسعافات أولية، مستجيب أول)'},
                            {id: 'activity-ab-7', text: 'توفير المعدات اللازمة والمستلزمات الطبية الأساسية'}
                        ]
                    },
                    {
                        title: 'تشغيل الجهاز الطبي المتنقل لنقل الحالات الباردة والحالات الطارئة ومواكبة الفعاليات',
                        activities: [
                            {id: 'activity-ab-8', text: 'توفير النقل الطبي المجاني عبر سيارات مخصّصة وفريق إسعافي مجهّز'},
                            {id: 'activity-ab-9', text: 'تفعيل غرفة عمليات الجهاز الطبي المتنقل'},
                            {id: 'activity-ab-10', text: 'عقد الشراكات وتقديم الدعم والتغطية الطبية في الفعاليات العامة وأنشطة المشروع'},
                            {id: 'activity-ab-11', text: 'تأمين التجهيزات اللوجستية، وتوفير اللوازم الطبية والوقود والصيانة الدائمة لسيارات الإسعاف'}
                        ]
                    },
                    {
                        title: 'العمل على تحسين خدمات المركز ضمن معايير وزارة الصحة العامة',
                        activities: [
                            {id: 'activity-ab-12', text: 'تقييم العمليات التشغيلية للمركز ودراسة الاحتياجات'},
                            {id: 'activity-ab-13', text: 'تطوير وتطبيق خطط وإجراءات تشغيلية تماشياً مع المعايير الوطنية لوزارة الصحة العامّة'}
                        ]
                    },
                    {
                        title: 'تأهيل وتجهيز مساحات المركز',
                        activities: [
                            {id: 'activity-ab-14', text: 'تنفيذ أعمال إنشائية لصيانة المساحات القائمة (15 غرفة وقاعة) ضمن نطاق المركز (348 متر مربع) تماشياً مع الخطط الموضوعة لرفع تصنيف المركز'},
                            {id: 'activity-ab-15', text: 'أعمال الترميم والتأهيل الإنشائي والخارجي للمركز | دهان القناطر والجدران (535 متر مربع) ودهان خارجي (172 متر مربع) | معالجة الأجزاء التالفة وتأهيل المداخل والمحيط'},
                            {id: 'activity-ab-16', text: 'أعمال الصيانة الداخلية | التجهيزات الكهربائية والتقنية | الحفاظ على الوظائف التشغيلية للأجهزة والأنظمة'}
                        ]
                    },
                    {
                        title: 'مواصلة توسيع مساحات المركز لتطوير الخدمات والآليات التدريبية والتشاركية',
                        activities: [
                            {id: 'activity-ab-17', text: 'المرحلة الثانية من أصل خمسة مراحل للأعمال الإنشائية'},
                            {id: 'activity-ab-18', text: 'تأهيل مساحات إضافية ملاصقة للمركز (15 غرفة وقاعة)'},
                            {id: 'activity-ab-19', text: 'أعمال الترميم والتأهيل الإنشائي والخارجي | إصلاح السقف والعقد الرملي | تنفيذ أعمال العزل ومعالجة الرطوبة | إعادة تأهيل الأرصفة والدرج والأرضيات | تسكير الفتحات الخارجية | ترميم الأحجار والمداخل'},
                            {id: 'activity-ab-20', text: 'أعمال التشطيب الداخلي (730 متر مربع) | تأهيل الواجهة الخارجية (170 متر مربع) | التجهيزات التقنية والخدمية | تركيب الزجاج والأبواب | تجهيزات المطبخ والحمام | تمديدات الكهرباء والتكييف'},
                            {id: 'activity-ab-21', text: 'تسوية الساحات الخارجية وتبليطها (1200 متر مربع) | صيانة التصوينة والمداخل لضمان الجهوزية التشغيلية الكاملة للمبنى'}
                        ]
                    },
                    {
                        title: 'تأمين المستلزمات الطبية واللوجستية لتوسيع الخدمات المقدمة للمرضى',
                        activities: [
                            {id: 'activity-ab-22', text: 'تأمين جهاز بصري رقمي Redsun Autolensmeter'}
                        ]
                    },
                    {
                        title: 'مواصلة تطوير نظام المعلوماتية والإنترنت لتنظيم عمليات المركز وجهازه المتنقل',
                        activities: [
                            {id: 'activity-ab-23', text: 'تطوير نظام المعلوماتية Meta soft لتنظيم العمليات الإدارية والمالية للمركز وجهازه المتنقل'},
                            {id: 'activity-ab-24', text: 'تطوير نظام الإنترنت لتوسيع التغطية والسرعة'},
                            {id: 'activity-ab-25', text: 'تركيب كاميرات للمراقبة'}
                        ]
                    },
                    {
                        title: 'متابعة تجهيز وتشغيل المركز كمساحة آمنة ومحفزة للتعلم والابتكار',
                        activities: [
                            {id: 'activity-ab-26', text: 'تأهيل مساحات المركز وتشغيلها لاستضافة الدورات والعمليات الإدارية'},
                            {id: 'activity-ab-27', text: 'تجهيز المساحات بأدوات تعليمية تتماشى مع دورات المركز'}
                        ]
                    },
                    {
                        title: 'إجراء عدة دورات مهنية معجلة في المركز',
                        activities: [
                            {id: 'activity-ab-28', text: 'تنظيم 8 دورات مهنية معجلة تشمل تدريب نظري وتطبيقي'}
                        ]
                    },
                    {
                        title: 'متابعة تطوير وتجهيز وتشغيل أكاديمية سيسكو للذكاء الاصطناعي',
                        activities: [
                            {id: 'activity-ab-29', text: 'تأهيل مساحات المركز وتشغيلها لاستضافة الدورات والعمليات الإدارية'},
                            {id: 'activity-ab-30', text: 'تطوير إطار عمل الأكاديمية ومجالات تخصصها'},
                            {id: 'activity-ab-31', text: 'تطوير شعار خاص بالأكاديمية'},
                            {id: 'activity-ab-32', text: 'توفير أدوات وتقنيات تعليمية حديثة بما يتماشى مع المعايير التربوية والتقنية'}
                        ]
                    },
                    {
                        title: 'إجراء عدة دورات تقنية معجلة في المركز',
                        activities: [
                            {id: 'activity-ab-33', text: 'تنظيم 5 دورات مهنية معجلة تشمل تدريب نظري وتطبيقي'}
                        ]
                    },
                    {
                        title: 'إجراء دورات تدريبية إضافية للمستفيدين من التدريب المهني والتقني',
                        activities: [
                            {id: 'activity-ab-34', text: 'تنظيم حفل تخرّج جامع لطلاب التدريب المهني والتقني في المركز وفتح باب التسجيل للدورات التدريبية الجديدة'},
                            {id: 'activity-ab-35', text: 'تنظيم عدّة دورات تدريبية للمستفيدين من تدريبات المركز'}
                        ]
                    },
                    {
                        title: 'إقامة ورشة عمل حول أحدث آليات التدريب التقني',
                        activities: [
                            {id: 'activity-ab-36', text: 'تنظيم ورشة عمل تشاركية حول أحدث آليات التدريب التقني ومجالات تكنولوجيا المعلومات والشبكات الإلكترونية والذكاء الاصطناعي'}
                        ]
                    },
                    {
                        title: 'إجراء عدة دورات مهنية معجلة للمركز في المجتمعات المهمشة',
                        activities: [
                            {id: 'activity-ab-37', text: 'تنظيم 5 دورات تقنية معجلة في المجتمعات المهمشة'}
                        ]
                    },
                    {
                        title: 'إجراء عدة دورات تقنية معجلة لأكاديمية سيسكو للذكاء الاصطناعي في المجتمعات المهمشة',
                        activities: [
                            {id: 'activity-ab-38', text: 'تنظيم 4 دورات تقنية معجلة في المجتمعات المهمشة'}
                        ]
                    },
                    {
                        title: 'تنفيذ خطة عمل برنامج أناملنا',
                        activities: [
                            {id: 'activity-ab-39', text: 'تطوير العلامة التسويقية لبرنامج "أناملنا" تحاكي التراث الثقافي وتحسّن جودة المنتجات الحرفية وتحويلها إلى تصاميم للمشغل'},
                            {id: 'activity-ab-40', text: 'تنفيذ خطة عمل الإنتاج المستدام باستخدام مواد وتقنيات إنتاجية تراعي المعايير البيئية'},
                            {id: 'activity-ab-41', text: 'إشراك مجموعة من النساء في أنشطة برنامج أناملنا لزيادة الدخل بإشراف وتوجيه من المركز'}
                        ]
                    },
                    {
                        title: 'إجراء دورات تدريبية للنساء حول أساليب الخياطة والحياكة والتطريز',
                        activities: [
                            {id: 'activity-ab-42', text: 'تنظيم دورات تدريبية حول حياكة المكرمة وأساليب الخياطة المستدامة'}
                        ]
                    },
                    {
                        title: 'تسويق منتجات النساء المشاركات في التدريب عبر بناء الشراكات والمشاركة في المعارض',
                        activities: [
                            {id: 'activity-ab-43', text: 'تنظيم معرض متنقّل للمشغل'},
                            {id: 'activity-ab-44', text: 'إشراك فريق المشغل في المعارض المتحركة والفعاليات الثقافية والبيئية'},
                            {id: 'activity-ab-45', text: 'استدامة وتجديد الشراكات لزيادة وتوجيه إنتاج المشغل'},
                            {id: 'activity-ab-46', text: 'تشغيل المعرض الدائم لبرنامج أناملنا لعرض وبيع المنتوجات'}
                        ]
                    },
                    {
                        title: 'متابعة تطوير التسويق الإلكتروني لمنتجات النساء المشاركات في التدريب',
                        activities: [
                            {id: 'activity-ab-47', text: 'استكمال حملة التسويق الإلكتروني للعلامة التجارية Brand Image لبرنامج أناملنا ولمنتجات المشغل على منصّتي إنستغرام وتيك توك'}
                        ]
                    },
                    {
                        title: 'تطوير نظام معلوماتية ومحاسبة لتنظيم العمليات الإدارية والتجارية للبرنامج',
                        activities: [
                            {id: 'activity-ab-48', text: 'تصميم نظام إلكتروني لإدارة بيانات برنامج أناملنا'}
                        ]
                    }
                ]
            },
            // AD.C - أكاديمية الدولة الوطنية (Activities based on your provided data)
            '63f1df7a-42b5-4062-950b-589c66584d23': {
                name: 'AD.C - أكاديمية الدولة الوطنية',
                actions: [
                    {
                        title: 'إجراء مقررات تعليمية ودورات تدريبية حول موضوعات متنوعة',
                        activities: [
                            {id: 'activity-ac-1', text: 'تنظيم مقرّر تعليمي حول الدولة ومنع التطرف العنيف'},
                            {id: 'activity-ac-2', text: 'تنظيم دورة تدريبية حول الموطانة ومنع التطرف العنيف'},
                            {id: 'activity-ac-3', text: 'تنظيم دورة تدريبية حول التربية على الدولة والتربية على الديمقراطية'}
                        ]
                    },
                    {
                        title: 'متابعة الآليات التفاعلية حول توثيق التجارب الوطنية في بناء الدولة باستخدام آليات الذكاء الاصطناعي',
                        activities: [
                            {id: 'activity-ac-4', text: 'إنجاز آليات توثيق التجارب الوطنية في بناء الدولة واستشراف المستقبل باستخدام آليات الذكاء الاصطناعي'},
                            {id: 'activity-ac-5', text: 'تطوير أدوات ذكية وتفاعلية لعرض ومناقشة نتائج التوثيق في الجلسات التشاركية للأكاديمية'}
                        ]
                    },
                    {
                        title: 'إجراء ورش تشاركية',
                        activities: [
                            {id: 'activity-ac-6', text: 'تنظيم ورشة عمل تشاركية حول التربية على الدولة'},
                            {id: 'activity-ac-7', text: 'تنظيم ورشة عمل تشاركية حول التربية على الديمقراطية'},
                            {id: 'activity-ac-8', text: 'تنظيم ورشة عمل تشاركية حول المواطنة ومنع التطرف العنيف'}
                        ]
                    }
                ]
            },
            // AD.D - منتدى شباب نهوض لبنان (Activities based on your provided data)
            'cb9f2e6c-3823-49e1-aab1-973cbb80351f': {
                name: 'AD.D - منتدى شباب نهوض لبنان',
                actions: [
                    {
                        title: 'تطوير أدوات رقمية وتنظيم ورش عمل تشاركية للشباب حول التعامل مع الذكاء الاصطناعي',
                        activities: [
                            {id: 'activity-ad-1', text: 'إشراك الشباب في تنفيذ مشاريع للبحث والتطوير عبر الذكاء الاصطناعي ضمن أولويات المنتدى'},
                            {id: 'activity-ad-2', text: 'تنظيم عدة ورش عمل للشباب حول التعامل مع الذكاء الاصطناعي'},
                            {id: 'activity-ad-3', text: 'تصميم أدوات تفاعلية تساعد الشباب على فهم تطبيقات الذكاء الاصطناعي في البحث والتطوير وتوظيفها في المنتديات'}
                        ]
                    },
                    {
                        title: 'تنظيم ورش عمل تدريبية للشباب حول آليات العمل البحثية في مجالات التنمية المستدامة والإنذار المبكر',
                        activities: [
                            {id: 'activity-ad-4', text: 'تنفيذ 4 ورش عمل تدريبية وتفاعلية تُمكّن الشباب من استخدام أدوات البحث وتحليل البيانات ضمن مجالات إدارة البيانات التنموية Env Data Management'},
                            {id: 'activity-ad-5', text: 'تنفيذ 4 ورش عمل تدريبية وتفاعلية تُمكّن الشباب من استخدام أدوات البحث وتحليل البيانات ضمن مجالات النيوجغرافيا وجمع البيانات الجماعية Neogeography'},
                            {id: 'activity-ad-6', text: 'تنفيذ 4 ورش عمل تدريبية وتفاعلية تُمكّن الشباب من استخدام أدوات البحث وتحليل البيانات ضمن مجالات أنظمة الإنذار المبكر Early Warning Systems DRM TOOLS'}
                        ]
                    }
                ]
            },
            // AD.E - منصة "إنمائية" (Activities based on your provided data)
            'a96ae126-80b6-454f-bf0d-c14f95b06442': {
                name: 'AD.E - منصة "إنمائية"',
                actions: [
                    {
                        title: 'تطوير تطبيق الهاتف المحمول للمنصة',
                        activities: [
                            {id: 'activity-ae-1', text: 'تصميم وتطوير تطبيق مخصص للهواتف الذكية لأنظمة iOS وAndroid يعكس خدمات ومحتوى منصة "إنمائية"'},
                            {id: 'activity-ae-2', text: 'توفير التطبيق عبر متجري App Store وGoogle Play'}
                        ]
                    },
                    {
                        title: 'تطوير صفحات متخصصة للمجالات القطاعية على المنصة',
                        activities: [
                            {id: 'activity-ae-3', text: 'إنشاء صفحات مخصصة داخل المنصة لكل قطاع تنموي رئيسي (التعليم، الصحة، البيئة، الاقتصاد)'},
                            {id: 'activity-ae-4', text: 'تصنيف المحتوى القطاعي للمنصة ضمن الصفحات القطاعية على مقالات وتحليلات وبيانات ومؤشرات مرئية'}
                        ]
                    },
                    {
                        title: 'تطوير صفحة جديدة حول فرص العمل وفرص تمويل المبادرات الشبابية في مجالات التنمية',
                        activities: [
                            {id: 'activity-ae-5', text: 'إنشاء صفحة ضمن المنصة لنشر فرص العمل وفرص التدريب المتاحة للشباب في القطاعات التنموية محليًا وإقليميًا'}
                        ]
                    },
                    {
                        title: 'تطوير صفحة "القطاع بالأرقام" لإبراز بيانات وإحصاءات القطاعات',
                        activities: [
                            {id: 'activity-ae-6', text: 'تصميم صفحة تفاعلية ضمن المنصة بعنوان "القطاع بالأرقام" تتضمن عرضًا بصريًا مبسطًا ومحدثًا لبيانات وإحصاءات رسمية وغير رسمية'}
                        ]
                    },
                    {
                        title: 'تطوير غرفة دردشة تفاعلية للنقاشات القطاعية واستطلاع الرأي',
                        activities: [
                            {id: 'activity-ae-7', text: 'إنشاء غرفة دردشة رقمية تفاعلية ضمن المنصة مخصصة للنقاشات المفتوحة حول القضايا القطاعية'}
                        ]
                    },
                    {
                        title: 'تقويم الأيام العالمية لتسليط الضوء على المناسبات المرتبطة بالتنمية وقطاعات المنصة',
                        activities: [
                            {id: 'activity-ae-8', text: 'إعداد تقويم سنوي تفاعلي يُدرج الأيام العالمية ذات الصلة بالتنمية المستدامة وقطاعات المنصة'},
                            {id: 'activity-ae-9', text: 'تخصيص محتوى رقمي في كل مناسبة (مقالات، حملات، مقابلات، إحصاءات، منشورات تفاعلية)'}
                        ]
                    },
                    {
                        title: 'تطبيق التحسينات لمحركات البحث SEO وتطوير أداة تصفح ذكية لتسهيل الوصول إلى المواضيع',
                        activities: [
                            {id: 'activity-ae-10', text: 'تحسين بنية المنصة ومحتواها لتكون متوافقة مع معايير محركات البحث (SEO)'},
                            {id: 'activity-ae-11', text: 'تطوير أداة تصفح ذكية (Smart Navigator) تعتمد على التصنيف التلقائي والاقتراحات السياقية'}
                        ]
                    },
                    {
                        title: 'تطوير مساعد ذكي مخصص للتقارير للتفاعل مع محتوى التقارير المنفردة وتوفير إجابات وتحليلات مختصة',
                        activities: [
                            {id: 'activity-ae-12', text: 'تصميم مساعد ذكي تفاعلي داخل المنصة، يعتمد على الذكاء الاصطناعي، يوفر قراءة وفهم التقارير بشكل منفرد ويتيح للمستخدمين طرح أسئلة مباشرة والحصول على إجابات دقيقة وتحليلات متعمّقة'}
                        ]
                    },
                    {
                        title: 'تطوير مساعد البحث الشامل ليتيح للمستخدمين البحث عبر جميع التقارير المتوفرة على المنصة، ويجمع المعلومات من مختلف القطاعات وأنواع المحتوى',
                        activities: [
                            {id: 'activity-ae-13', text: 'تصميم وتطوير مساعد بحث ذكي لإجراء بحث شامل ودقيق في جميع التقارير والوثائق والمقالات المنشورة'}
                        ]
                    },
                    {
                        title: 'إطلاق روبوت دردشة لمساعدة المستخدمين وتوجيههم للمحتوى',
                        activities: [
                            {id: 'activity-ae-14', text: 'تطوير وإطلاق روبوت دردشة ذكي (Chatbot) مدمج في واجهة المنصة، يعمل بالذكاء الاصطناعي، ويقدّم إجابات فورية على أسئلة المستخدمين'}
                        ]
                    },
                    {
                        title: 'تطبيق خرائط معرفية لتتبع الكلمات المفتاحية لربط المواضيع التنموية والبيانات والجهات المعنية بصريًا',
                        activities: [
                            {id: 'activity-ae-15', text: 'تصميم وتفعيل خرائط معرفية تفاعلية Knowledge Graphs داخل المنصة تعتمد على الذكاء الاصطناعي لتحديد الكلمات المفتاحية والكيانات'}
                        ]
                    },
                    {
                        title: 'إصدار نشرة أسبوعية للمنصة تضم أحدث الأخبار والمحتوى',
                        activities: [
                            {id: 'activity-ae-16', text: 'إعداد وإطلاق نشرة إلكترونية أسبوعية Newsletter تصل إلى مشتركي المنصة عبر البريد الإلكتروني'}
                        ]
                    },
                    {
                        title: 'تطوير لوحة بيانات وتحليلات للمستخدمين والمحتوى',
                        activities: [
                            {id: 'activity-ae-17', text: 'تصميم وتفعيل لوحة تحكم تفاعلية (Dashboard) داخل المنصة تُظهر إحصاءات وبيانات لحظية حول أداء المحتوى وسلوك المستخدمين'}
                        ]
                    }
                ]
            }
        };

        // Program-Project relationship mapping
        const programProjects = {
            'PROG001': [
                {id: 'PROG001-P01', text: 'PROG001-P01 - Rafic Hariri Technical Institute', subProgram: null},
                {id: 'PROG001-P02', text: 'PROG001-P02 - RHHS Teachers & Staff Capacity Building', subProgram: null}
            ],
            'PROG002': [
                {id: 'PROG002-P01', text: 'PROG002-P01 - HBHS Teachers & Staff Capacity Building', subProgram: null},
                {id: 'PROG002-P02', text: 'PROG002-P02 - IB PYP Accreditation', subProgram: null}
            ],
            'PROG010': [
                {id: 'PROG010-P01', text: 'PROG010-P01 - Remedial Education Courses', subProgram: null},
                {id: 'PROG010-P02', text: 'PROG010-P02 - Educational Conference of Saida & Neighbouring Towns', subProgram: null}
            ],
            'PROG019': [
                {id: 'PROG020-P01', text: 'PROG020-P01 - University Academy Legal Registration', subProgram: 'PROG020'},
                {id: 'PROG021-P01', text: 'PROG021-P01 - Saida Discusses the Ministerial Statement', subProgram: 'PROG021'},
                {id: 'PROG021-P02', text: 'PROG021-P02 - Readings in the Inaugural Speech', subProgram: 'PROG021'},
                {id: 'PROG022-P01', text: 'PROG022-P01 - Rafic Hariri Forum for PVE', subProgram: 'PROG022'},
                {id: 'PROG022-P02', text: 'PROG022-P02 - Hariri Foundation Award for PVE', subProgram: 'PROG022'},
                {id: 'PROG023-P03', text: 'PROG023-P03 - Early Warning Network for PVE - Saida', subProgram: 'PROG022'},
                {id: 'PROG023-P04', text: 'PROG023-P04 - Trainings - The State and PVE', subProgram: 'PROG022'}
            ]
        };

        const subProgramDisplayNames = {
            'PROG020': 'National State University Academy',
            'PROG021': 'National State Forum',
            'PROG022': 'Prevention of Violent Extremism Program'
        };

        // Initialize Select2 for all dropdowns
        $('#programs_select').select2({
            placeholder: 'Select a program...',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 10
        });

        $('#projects_select').select2({
            placeholder: 'Select a program first to see available projects',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            multiple: true
        });

        $('#rp_component_id').select2({
            placeholder: 'Select a reporting component...',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 10
        });

        $('#rp_activities_select').select2({
            placeholder: 'Select a reporting component first',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            multiple: true
        });

        $('#focal_points_select').select2({
            placeholder: 'Select focal points...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            multiple: true
        });

        // Function to load RP Activities based on selected component (GROUPED BY ACTIONS)
        function loadRPActivitiesByComponent(componentId) {
            const activitiesSelect = $('#rp_activities_select');
            
            if (!componentId || !rpComponentsData[componentId]) {
                activitiesSelect.empty();
                activitiesSelect.append('<option value="" disabled>Select a reporting component first</option>');
                activitiesSelect.select2({
                    placeholder: 'Select a reporting component first',
                    allowClear: true,
                    width: '100%',
                    closeOnSelect: false,
                    multiple: true
                });
                return;
            }
            
            const component = rpComponentsData[componentId];
            activitiesSelect.empty();
            
            if (component.actions && component.actions.length > 0) {
                component.actions.forEach(action => {
                    // Create optgroup for each action (this will show as a disabled group header)
                    const actionGroup = $('<optgroup>').attr('label', action.title);
                    
                    // Add each activity under this action
                    if (action.activities && action.activities.length > 0) {
                        action.activities.forEach(activity => {
                            actionGroup.append($('<option>').val(activity.id).text(activity.text));
                        });
                    }
                    
                    activitiesSelect.append(actionGroup);
                });
                
                // Reinitialize Select2 with the new grouped options
                activitiesSelect.select2({
                    placeholder: 'Select reporting activities...',
                    allowClear: true,
                    width: '100%',
                    closeOnSelect: false,
                    multiple: true,
                    templateResult: function(data) {
                        // If it's an optgroup (disabled), return the text as a group header
                        if (data.disabled) {
                            return $('<span style="font-weight: 600; color: #6c757d;">').text(data.text);
                        }
                        return data.text;
                    }
                });
            } else {
                activitiesSelect.append('<option value="">No activities found for this component</option>');
                activitiesSelect.select2({
                    placeholder: 'No activities available',
                    allowClear: true,
                    width: '100%',
                    closeOnSelect: false,
                    multiple: true
                });
            }
            
            activitiesSelect.trigger('change');
        }

        // Function to update projects based on selected program
        function updateProjectsBasedOnProgram() {
            const selectedProgram = $('#programs_select').val();
            const projectsSelect = $('#projects_select');
            
            projectsSelect.empty();
            
            if (!selectedProgram) {
                projectsSelect.append('<option value="">Select a program first to see available projects</option>');
                projectsSelect.select2({
                    placeholder: 'Select a program first to see available projects',
                    allowClear: true,
                    width: '100%',
                    closeOnSelect: false,
                    multiple: true
                });
                return;
            }
            
            const projects = programProjects[selectedProgram] || [];
            
            if (projects.length === 0) {
                projectsSelect.append('<option value="">No projects available for selected program</option>');
            } else {
                const groupedProjects = {};
                projects.forEach(project => {
                    const groupKey = project.subProgram || 'general';
                    if (!groupedProjects[groupKey]) {
                        groupedProjects[groupKey] = [];
                    }
                    groupedProjects[groupKey].push(project);
                });
                
                if (groupedProjects['general'] && groupedProjects['general'].length > 0) {
                    const generalGroup = $('<optgroup>').attr('label', 'Direct Projects');
                    groupedProjects['general'].sort((a, b) => a.text.localeCompare(b.text))
                        .forEach(project => {
                            generalGroup.append($('<option>').val(project.id).text(project.text));
                        });
                    projectsSelect.append(generalGroup);
                }
                
                Object.keys(groupedProjects).forEach(groupKey => {
                    if (groupKey !== 'general' && groupedProjects[groupKey].length > 0) {
                        const groupName = subProgramDisplayNames[groupKey] || groupKey;
                        const subProgramGroup = $('<optgroup>').attr('label', groupName);
                        groupedProjects[groupKey].sort((a, b) => a.text.localeCompare(b.text))
                            .forEach(project => {
                                subProgramGroup.append($('<option>').val(project.id).text(project.text));
                            });
                        projectsSelect.append(subProgramGroup);
                    }
                });
            }
            
            projectsSelect.select2({
                placeholder: 'Select projects...',
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
                multiple: true
            });
            
            projectsSelect.trigger('change');
        }

        // Event Listeners
        $('#programs_select').on('change', updateProjectsBasedOnProgram);
        $('#rp_component_id').on('change', function() {
            loadRPActivitiesByComponent($(this).val());
        });

        // Load activities if component is already selected on page load
        $(document).ready(function() {
            const initialComponentId = $('#rp_component_id').val();
            if (initialComponentId) {
                loadRPActivitiesByComponent(initialComponentId);
            }
        });

        // Form Validation
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
                    form.querySelectorAll('.is-invalid').forEach(field => {
                        field.classList.remove('is-invalid');
                    });
                }
                
                // Reset all Select2 fields
                $('#programs_select, #projects_select, #rp_component_id, #rp_activities_select, #focal_points_select')
                    .val(null).trigger('change');
                
                // Reset checkboxes
                form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    checkbox.checked = false;
                });
                
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            });
        }

        // Real-time validation
        if (form) {
            form.querySelectorAll('[required]').forEach(field => {
                field.addEventListener('blur', validateField);
                field.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        }

        // Select2 validation
        $('#programs_select, #projects_select, #rp_component_id, #rp_activities_select, #focal_points_select')
            .on('change', function() {
                if ($(this).val() && $(this).val().length > 0) {
                    $(this).removeClass('is-invalid');
                }
            });

        // Handle "None" checkbox logic
        const noneCheckbox = document.getElementById('support_none');
        const otherCheckboxes = document.querySelectorAll('input[name="operational_support[]"]:not(#support_none)');
        
        if (noneCheckbox) {
            noneCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    otherCheckboxes.forEach(cb => {
                        cb.checked = false;
                    });
                }
            });

            otherCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (this.checked) {
                        noneCheckbox.checked = false;
                    }
                });
            });
        }

        function validateField() {
            if (!this.value.trim()) {
                this.classList.add('is-invalid');
                return false;
            } else {
                this.classList.remove('is-invalid');
                return true;
            }
        }
    });
</script>
@endsection