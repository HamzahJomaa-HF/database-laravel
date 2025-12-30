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
                            <li class="breadcrumb-item"><a href="{{ route('activities.index') }}" class="text-decoration-none">Activities</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Activity</li>
                        </ol>
                    </nav>
                    <h1 class="h2 fw-bold mb-1">Edit Activity</h1>
                    <p class="text-muted mb-0">Update activity information and details</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary">
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

                    <form action="{{ route('activities.update', $activity->activity_id) }}" method="POST" class="needs-validation" novalidate id="activityForm">
                        @csrf
                        @method('PUT')
                        
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
                                        <label for="activity_title_en" class="form-label fw-semibold">
                                            Activity Title (EN) <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="activity_title_en" id="activity_title_en" 
                                               class="form-control @error('activity_title_en') is-invalid @enderror" 
                                               value="{{ old('activity_title_en', $activity->activity_title_en) }}" 
                                               placeholder="Activity Title in English" required>
                                        @error('activity_title_en')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="activity_title_ar" class="form-label fw-semibold">Activity Title (AR)</label>
                                        <input type="text" name="activity_title_ar" id="activity_title_ar" 
                                               class="form-control @error('activity_title_ar') is-invalid @enderror" 
                                               value="{{ old('activity_title_ar', $activity->activity_title_ar) }}" 
                                               placeholder="Activity Title in Arabic">
                                        @error('activity_title_ar')
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
                                            @php
                                                $activityTypes = [
                                                    'Capacity Building' => 'Capacity Building - بناء القدرات',
                                                    'Policies & Plans' => 'Policies & Plans - السياسات والخطط',
                                                    'Engagement Event' => 'Engagement Event - فعالية تفاعلية',
                                                    'Overview' => 'Overview - نظرة عامة',
                                                    'Field Activity' => 'Field Activity - نشاط ميداني',
                                                    'Specialized Service' => 'Specialized Service - خدمة متخصصة',
                                                    'Research Activity' => 'Research Activity - نشاط بحثي',
                                                    'Physical Development' => 'Physical Development - تطوير مادي',
                                                    'Technical Development' => 'Technical Development - تطوير تقني',
                                                    'Media Production' => 'Media Production - إنتاج إعلامي',
                                                    'Public Campaign' => 'Public Campaign - حملة توعوية',
                                                    'Legal Activity' => 'Legal Activity - نشاط قانوني',
                                                    'Support & Assistance' => 'Support & Assistance - الدعم والمساندة'
                                                ];
                                            @endphp
                                            @foreach($activityTypes as $key => $label)
                                                <option value="{{ $key }}" {{ old('activity_type', $activity->activity_type) == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
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
                                            <option value="PROG001" {{ old('program', $activity->program) == 'PROG001' ? 'selected' : '' }}>PROG001 - Rafic Hariri High School</option>
                                            <option value="PROG002" {{ old('program', $activity->program) == 'PROG002' ? 'selected' : '' }}>PROG002 - Hajj Bahaa Hariri High School</option>
                                            <option value="PROG010" {{ old('program', $activity->program) == 'PROG010' ? 'selected' : '' }}>PROG010 - School Network of Saida & Neighboring Towns</option>
                                            <option value="PROG019" {{ old('program', $activity->program) == 'PROG019' ? 'selected' : '' }}>PROG019 - National State Academy</option>
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
                                               value="{{ old('start_date', $activity->start_date ? \Carbon\Carbon::parse($activity->start_date)->format('Y-m-d') : '') }}" 
                                               required>
                                        @error('start_date')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="end_date" class="form-label fw-semibold">End Date</label>
                                        <input type="date" name="end_date" id="end_date" 
                                               class="form-control @error('end_date') is-invalid @enderror" 
                                               value="{{ old('end_date', $activity->end_date ? \Carbon\Carbon::parse($activity->end_date)->format('Y-m-d') : '') }}">
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
                                            @php
                                                $venues = [
                                                    'Hariri Foundation Headquarters',
                                                    'Rafic Hariri High School',
                                                    'Hajj Bahaa Hariri High School',
                                                    'Hariri Social & Medical Center',
                                                    'Hariri Foundation Vocational & Technical Training Center',
                                                    'Khan al Franj',
                                                    'Outreach & Leadership Academy',
                                                    'National State Academy'
                                                ];
                                            @endphp
                                            @foreach($venues as $venue)
                                                <option value="{{ $venue }}" {{ old('venue', $activity->venue) == $venue ? 'selected' : '' }}>
                                                    {{ $venue }}
                                                </option>
                                            @endforeach
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
                                                  placeholder="Describe the activity content and network...">{{ old('content_network', $activity->content_network) }}</textarea>
                                        @error('content_network')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ====================================== --}}
                        {{-- SECTION 5: REPORTING ACTIVITIES --}}
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
                                                        {{ old('rp_component_id', $activity->rp_component_id) == $component->rp_components_id ? 'selected' : '' }}>
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

                                    {{-- RP Activities Multi-Select --}}
                                    <div class="col-md-12">
                                        <label for="rp_activities_select" class="form-label fw-semibold mb-2">Reporting Activities</label>
                                        <select id="rp_activities_select" 
                                                multiple
                                                class="form-control @error('rp_activities') is-invalid @enderror"
                                                name="rp_activities[]">
                                            <option value="" disabled>Select a reporting component first</option>
                                            @if(isset($selectedRpActivities))
                                                @foreach($selectedRpActivities as $activityId)
                                                    @php
                                                        $activityModel = App\Models\RpActivity::find($activityId);
                                                    @endphp
                                                    @if($activityModel)
                                                        <option value="{{ $activityModel->rp_activities_id }}" selected>
                                                            {{ $activityModel->code }} - {{ $activityModel->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
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
                        {{-- SECTION 6: FOCAL POINTS --}}
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
                                                    $focalPoints = [
                                                        ['id' => 1, 'name' => 'Mohamad Ismail'],
                                                        ['id' => 2, 'name' => 'Mohammad Harriri'],
                                                        ['id' => 3, 'name' => 'Lilia Chahine'],
                                                        ['id' => 4, 'name' => 'Nadine Zaidan'],
                                                        ['id' => 5, 'name' => 'Hatem Assii'],
                                                        ['id' => 6, 'name' => 'Ahmad Chami'],
                                                    ];
                                                    
                                                    // Get selected focal points from activity
                                                    $selectedFocalPoints = json_decode($activity->focal_points ?? '[]', true) ?: [];
                                                @endphp
                                                @foreach($focalPoints as $point)
                                                    <option value="{{ $point['id'] }}" 
                                                            {{ in_array($point['id'], $selectedFocalPoints) ? 'selected' : '' }}>
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
                        {{-- SECTION 7: OPERATIONAL SUPPORT REQUIRED --}}
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
                                            @php
                                                $operationalSupport = json_decode($activity->operational_support ?? '[]', true) ?: [];
                                            @endphp
                                            <div class="row">
                                                <div class="col-md-6 col-lg-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input @error('operational_support') is-invalid @enderror" 
                                                               type="checkbox" 
                                                               name="operational_support[]" 
                                                               id="support_logistics" 
                                                               value="Logistics"
                                                               {{ in_array('Logistics', $operationalSupport) ? 'checked' : '' }}>
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
                                                               {{ in_array('Media', $operationalSupport) ? 'checked' : '' }}>
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
                                                               {{ in_array('Public Relations', $operationalSupport) ? 'checked' : '' }}>
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
                                                               value="None"
                                                               {{ in_array('None', $operationalSupport) ? 'checked' : '' }}>
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
    /* ... your existing styles ... */
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
            placeholder: 'Select projects...',
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

        // Function to update projects based on selected program
        function updateProjectsBasedOnProgram() {
            const selectedProgram = $('#programs_select').val();
            const projectsSelect = $('#projects_select');
            
            // Clear current projects
            projectsSelect.empty();
            
            if (!selectedProgram) {
                projectsSelect.append('<option value="" disabled>Select a program first to see available projects</option>');
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
            
            // Select projects that are already associated with the activity
            // Assuming $activity->projects contains the selected project IDs
            const selectedProjectIds = {!! json_encode(json_decode($activity->projects ?? '[]', true) ?: []) !!};
            if (selectedProjectIds.length > 0) {
                projectsSelect.val(selectedProjectIds).trigger('change');
            }
            
            projectsSelect.trigger('change');
        }

        // Update projects when program is selected
        $('#programs_select').on('change', updateProjectsBasedOnProgram);

                       // Function to load RP Activities based on selected component using AJAX
               // Function to load RP Activities based on selected component using AJAX
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
    
    // Clear current activities
    activitiesSelect.empty();
    activitiesSelect.append('<option value="">Loading activities...</option>');
    activitiesSelect.trigger('change');
    
    console.log('Loading activities for component ID:', componentId);
    
    // CHANGE THIS LINE: Use the actions endpoint instead
    $.ajax({
        url: '{{ route("activities.get-rp-actions-with-activities") }}', // CHANGED
        method: 'GET',
        data: { component_id: componentId },
        dataType: 'json',
        beforeSend: function() {
            console.log('Sending AJAX request to actions endpoint');
        },
        success: function(response) {
            console.log('AJAX response received:', response);
            
            // Clear the loading message
            activitiesSelect.empty();
            
            if (response.success && response.data && Array.isArray(response.data)) {
                console.log('Found', response.data.length, 'actions with activities');
                
                if (response.data.length === 0) {
                    activitiesSelect.append('<option value="">No activities found for this component</option>');
                } else {
                    // NEW: Process grouped data by actions
                    response.data.forEach(action => {
                        if (action.activities && action.activities.length > 0) {
                            // Create optgroup for each action
                            const optgroup = $('<optgroup>')
                                .attr('label', action.action_code + ' - ' + action.action_name);
                            
                            // Sort activities within each action
                            action.activities.sort((a, b) => {
                                const codeA = a.code || '';
                                const codeB = b.code || '';
                                return codeA.localeCompare(codeB);
                            });
                            
                            // Add each activity as an option
                            action.activities.forEach(activity => {
                                const activityText = activity.code + ' - ' + activity.name;
                                console.log('Adding activity:', activity.rp_activities_id, activityText);
                                
                                optgroup.append(
                                    $('<option>')
                                        .val(activity.rp_activities_id)
                                        .text(activityText)
                                        .data('action_id', action.action_id)
                                        .data('action_name', action.action_name)
                                );
                            });
                            
                            activitiesSelect.append(optgroup);
                        }
                    });
                    
                    // Re-initialize Select2
                    activitiesSelect.select2({
                        placeholder: 'Select reporting activities...',
                        allowClear: true,
                        width: '100%',
                        closeOnSelect: false,
                        multiple: true,
                        dropdownAutoWidth: true
                    });
                    
                    // Select activities that are already associated
                    const selectedActivityIds = {!! json_encode($selectedRpActivities ?? []) !!};
                    console.log('Selected activity IDs:', selectedActivityIds);
                    
                    if (selectedActivityIds.length > 0) {
                        // Get all activity IDs from the response
                        const allActivityIds = [];
                        response.data.forEach(action => {
                            if (action.activities) {
                                action.activities.forEach(activity => {
                                    allActivityIds.push(activity.rp_activities_id);
                                });
                            }
                        });
                        
                        // Filter out any IDs that don't exist in the response
                        const validIds = selectedActivityIds.filter(id => 
                            allActivityIds.includes(id)
                        );
                        
                        if (validIds.length > 0) {
                            activitiesSelect.val(validIds).trigger('change');
                            console.log('Selected', validIds.length, 'existing activities');
                        }
                    }
                }
            } else {
                console.log('Response not successful or no data:', response);
                activitiesSelect.append('<option value="">No activities found for this component</option>');
            }
            
            activitiesSelect.select2({
                placeholder: 'Select reporting activities...',
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
                multiple: true
            });
            activitiesSelect.trigger('change');
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            activitiesSelect.empty();
            activitiesSelect.append('<option value="">Error loading activities</option>');
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

        // Event listener for component change
        $('#rp_component_id').on('change', function() {
            const componentId = $(this).val();
            console.log('Component changed to:', componentId);
            loadRPActivitiesByComponent(componentId);
        });

        // Load activities on page load if component is already selected
        $(document).ready(function() {
            const initialComponentId = $('#rp_component_id').val();
            if (initialComponentId) {
                console.log('Loading activities for initial component:', initialComponentId);
                // Small delay to ensure DOM is ready
                setTimeout(() => {
                    loadRPActivitiesByComponent(initialComponentId);
                }, 300);
            }
            
            // Also load projects on page load
            updateProjectsBasedOnProgram();
        });

              // Form validation - Check if elements exist first
        const form = document.getElementById('activityForm');
        const submitBtn = document.getElementById('submitBtn');
        const resetBtn = document.getElementById('resetBtn');
        
        if (form && submitBtn) {
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
                
                if (submitBtn) {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                }
            });
        }
        
        // Handle "None" checkbox logic
        const noneCheckbox = document.getElementById('support_none');
        if (noneCheckbox) {
            noneCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    const checkboxes = document.querySelectorAll('input[name="operational_support[]"]');
                    checkboxes.forEach(cb => {
                        if (cb.id !== 'support_none') {
                            cb.checked = false;
                        }
                    });
                }
            });

            const checkboxes = document.querySelectorAll('input[name="operational_support[]"]');
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