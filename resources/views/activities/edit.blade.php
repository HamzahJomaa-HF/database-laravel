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
                                    {{-- Row 1 --}}
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

                                    {{-- Row 2 --}}
                                    <div class="col-md-6">
                                        <label for="activity_type" class="form-label fw-semibold">
                                            Activity Type <span class="text-danger">*</span>
                                        </label>
                                        <select name="activity_type" id="activity_type" class="form-control form-select @error('activity_type') is-invalid @enderror" required>
                                            <option value="">Select Activity Type</option>
                                            @php
                                                $activityTypes = [
    'Conference / Forum' => 'Conference / Forum – مؤتمر/ منتدى',
    'Coordination Meeting' => 'Coordination Meeting – اجتماع تنسيقي',
    'Training Workshop' => 'Training Workshop – ورشة تدريبية',
    'Course' => 'Course – دورة',
    'Seminar' => 'Seminar – ندوة',
    'Simulation' => 'Simulation – محاكاة',
    'Consultative Workshop' => 'Consultative Workshop – ورشة تشاورية',
    'Working Groups' => 'Working Groups – مجموعات عمل',
    'Survey' => 'Survey – استمارة',
    'Exhibition' => 'Exhibition – معرض',
    'Concert / Festival' => 'Concert / Festival – حفل موسيقي/ مهرجان',
    'Tournament' => 'Tournament – بطولة',
    'Specialized Service' => 'Specialized Service – خدمة متخصصة',
    'Capacity Building' => 'Capacity Building - بناء القدرات',
    'Policies & Plans' => 'Policies & Plans - السياسات والخطط',
    'Engagement Event' => 'Engagement Event - فعالية تفاعلية',
    'Overview' => 'Overview - نظرة عامة',
    'Field Activity' => 'Field Activity - نشاط ميداني',
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
                                                <option value="{{ $label }}" {{ old('activity_type', $activity->activity_type) == $label ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('activity_type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="maximum_capacity" class="form-label fw-semibold">
                                            Maximum Capacity
                                        </label>

                                        <input
                                            type="number"
                                            name="maximum_capacity"
                                            id="maximum_capacity"
                                            class="form-control @error('maximum_capacity') is-invalid @enderror"
                                            value="{{ old('maximum_capacity', $activity->maximum_capacity) }}"
                                            placeholder="Enter maximum capacity"
                                            min="0"
                                        >

                                        @error('maximum_capacity')
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
                                <div class="row">
                                    {{-- Programs Single Select --}}
                                    <div class="col-md-12 mb-3">
                                        <label for="programs_select" class="form-label fw-semibold mb-2">
                                            Program <span class="text-danger">*</span>
                                        </label>
                                       <select id="programs_select"
                                            class="form-control @error('program') is-invalid @enderror"
                                            name="program">
                                        <option value="">Select a Program</option>
                                        
                                        @foreach($programs as $program)
                                            @php
                                                $isSelected = in_array($program->program_id, $selected_program ?? []);
                                            @endphp

                                            <option value="{{ $program->program_id }}"
                                                    {{ $isSelected ? 'selected' : '' }}
                                                    data-program-id="{{ $program->program_id }}">
                                                {{ $program->name }}
                                            </option>
                                        @endforeach
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
            {{-- Row 1 --}}
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
            {{-- Row 2 - Venue --}}
            <div class="col-md-12">
                <label for="venue_select" class="form-label fw-semibold">Venue</label> test {{  $activity->venue }}
                <select name="venue_select" id="venue_select" 
                       class="form-control form-select @error('venue') is-invalid @enderror">
                    <option value="">Select Venue</option>
                    @php
                        $venues = [
                            'HF Beirut Office - Brown Meeting Room',
                            'HF Beirut Office - Red Meeting Room',
                            'HF Beirut Office - Black Meeting Room',
                            'HF Beirut Office - Enmaeya Meeting Room',
                            'HF Beirut Office - Enmaeya Studio',
                            'National State Academy - Assembly Hall',
                            'National State Academy - Conference Room',
                            'National State Academy - Meeting Room',
                            'Rafic Hariri High School - Campus',
                            'Rafic Hariri High School - Theater',
                            'Rafic Hariri High School - Library',
                            'Rafic Hariri High School - Houssam Hariri Basketball Court',
                            'Hajj Bahaa Hariri High School - Campus',
                            'Hajj Bahaa Hariri High School - Theater',
                            'Outreach & Leadership Academy',
                            'Khan El Franj',
                            'Hariri Social & Medical Center',
                            'HF Local Community Center - Old Saida',
                            'HF Local Community Center - Taamir Ein El Hilweh',
                            'HF Local Community Center - Helalieh',
                            'HF Vocational & Technical Training Center',
                            'Cisco Academy for Digital Skills & Artificial Intelligence',
                            'Online',
                            'Other'
                        ];
                        
                        // Get current venue from database
                        $currentVenue = old('venue', $activity->venue);
                        $isCustomVenue = $currentVenue && !in_array($currentVenue, $venues);
                        
                        // If custom venue, we'll show "Other" as selected
                        $displayValue = $isCustomVenue ? 'Other' : $currentVenue;
                    @endphp
                    
                    @foreach($venues as $venue)
                        <option value="{{ $venue }}" {{ $displayValue == $venue ? 'selected' : '' }}>
                            {{ $venue }}
                        </option>
                    @endforeach
                </select>
                @error('venue')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Row 3 - Custom Venue Input (only shows when "Other" is selected) --}}
            <div class="col-md-12" id="customVenueContainer" style="display: {{ $isCustomVenue ? 'block' : 'none' }};">
                <label for="custom_venue" class="form-label fw-semibold">Custom Venue <span class="text-danger">*</span></label>
                <input type="text" name="custom_venue" id="custom_venue" 
                       class="form-control @error('custom_venue') is-invalid @enderror" 
                       value="{{ $isCustomVenue ? $currentVenue : old('custom_venue') }}" 
                       placeholder="Enter custom venue name"
                       {{ $isCustomVenue ? '' : 'disabled' }}>
                @error('custom_venue')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    <i class="bi bi-info-circle me-1"></i>
                    Please specify the venue name when selecting "Other"
                </div>
            </div>
            
            {{-- Hidden input to store the final venue value --}}
            <input type="hidden" name="venue" id="venue_hidden" value="{{ $currentVenue }}">
        </div>
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
                                <div class="row">
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
{{-- SECTION 5: ACTION PLAN SELECTION --}}
{{-- ====================================== --}}
<div class="section-card mb-4">
    <div class="section-header">
        <h6 class="mb-0 fw-semibold">Action Plan</h6>
        <span class="text-muted small">Select action plan to filter components</span>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="action_plan_id" class="form-label fw-semibold mb-2">Action Plan</label>
                <select id="action_plan_id" 
                        class="form-control form-select @error('action_plan_id') is-invalid @enderror"
                        name="action_plan_id">
                    <option value="">Select an Action Plan</option>
                    @foreach($actionPlans as $actionPlan)
                        <option value="{{ $actionPlan->action_plan_id }}" {{ $actionPlan->action_plan_id == $selected_action_plan_id ? 'selected' : '' }}>
                            {{ $actionPlan->title }}
                            @if($actionPlan->start_date && $actionPlan->end_date)
                                ({{ \Carbon\Carbon::parse($actionPlan->start_date)->format('Y') }} - {{ \Carbon\Carbon::parse($actionPlan->end_date)->format('Y') }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('action_plan_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="form-text mt-1">
                    <i class="bi bi-info-circle me-1"></i>
                    Select an action plan to see related reporting components
                </div>
            </div>
        </div>
    </div>
</div>

                       {{-- ====================================== --}}
                       
{{-- SECTION 6: REPORTING ACTIVITIES --}}
{{-- ====================================== --}}
<div class="section-card mb-4">
    <div class="section-header">
        <h6 class="mb-0 fw-semibold">Reporting Activities</h6>
        <span class="text-muted small">Select reporting component and activities</span>
    </div>
    <div class="section-body">
        <div class="row">
            {{-- RP Component Single Select --}}
            <div class="col-md-12 mb-3">
                <label for="rp_component_id" class="form-label fw-semibold mb-2">Reporting Component</label>
                <select id="rp_component_id" 
                        class="form-control form-select @error('rp_component_id') is-invalid @enderror"
                        name="rp_component_id">
                    <option value="">Select a Reporting Component</option>
                    
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
                    {{-- This will be populated by JavaScript --}}
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
{{-- SECTION 7: FOCAL POINTS --}}
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
                    
                    <?php
                    // Direct database query to get all employees (like in create blade)
                    use Illuminate\Support\Facades\DB;
                    
                    // Get all active employees
                    $allEmployees = DB::table('employees')
                        ->whereNull('deleted_at')
                        ->orderBy('first_name')
                        ->get(['employee_id', 'first_name', 'last_name', 'email', 'employee_type']);
                    
                    // Get selected focal points for this activity
                    // First try from activity_focal_points table (if you're using it)
                    $selectedFocalPointsFromTable = DB::table('activity_focal_points')
                        ->where('activity_id', $activity->activity_id)
                        ->whereNull('deleted_at')
                        ->pluck('rp_focalpoints_id')
                        ->toArray();
                    
                    // Also check if there's JSON in focal_points column (for backward compatibility)
                    $selectedFocalPointsFromJson = [];
                    if (!empty($activity->focal_points)) {
                        $decoded = json_decode($activity->focal_points, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $selectedFocalPointsFromJson = $decoded;
                        }
                    }
                    
                    // Combine both sources, prioritize table data
                    $selectedFocalPoints = !empty($selectedFocalPointsFromTable) 
                        ? $selectedFocalPointsFromTable 
                        : $selectedFocalPointsFromJson;
                    
                    // If form was submitted with errors, use old input
                    if (old('focal_points')) {
                        $selectedFocalPoints = old('focal_points', []);
                    }
                    ?>
                    
                    <select id="focal_points_select" 
                            multiple
                            class="form-control @error('focal_points') is-invalid @enderror"
                            name="focal_points[]">
                        
                        @if($allEmployees->count() > 0)
                            @foreach($allEmployees as $employee)
                                @php
                                    $isSelected = in_array($employee->employee_id, $selectedFocalPoints);
                                    $displayName = trim($employee->first_name . ' ' . $employee->last_name);
                                    $email = $employee->email ?? '';
                                    $type = $employee->employee_type ?? '';
                                @endphp
                                
                                <option value="{{ $employee->employee_id }}" 
                                        {{ $isSelected ? 'selected' : '' }}>
                                    {{ $displayName }} - {{ $email }} {{ $type ? '(' . $type . ')' : '' }}
                                </option>
                            @endforeach
                        @else
                            <option value="">No employees found in database</option>
                        @endif
                    </select>
                    
                    @error('focal_points')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('focal_points.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    
                    <div class="form-text mt-2">
                        <i class="bi bi-info-circle me-1"></i>
                        Select one or more employees as focal points for this activity
                    </div>
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
    /* ... your existing styles remain unchanged ... */
</style>
@endsection
@section('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@section('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script>

    document.addEventListener('DOMContentLoaded', function() {
        const selectedComponentId = @json($selected_component_id);
        const selected_rp_activity_ids = @json($selected_rp_activity_ids);
        // ============================================
        // PROGRAM AND PROJECTS HANDLING
        // ============================================
        // Initialize the single select for programs
        $('#programs_select').select2({
            placeholder: 'Select a program...',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 10
        });

        // Initialize projects select
        $('#projects_select').select2({
            placeholder: 'Select projects...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            multiple: true
        });

        // Function to load projects based on selected program
        function loadProjectsByProgram(programId, programExternalId) {
            const projectsSelect = $('#projects_select');

            if (!programId) {
                projectsSelect.empty();
                projectsSelect.append('<option value="" disabled>Select a program first to see available projects</option>');
                projectsSelect.trigger('change');

                projectsSelect.select2({
                    placeholder: 'Select a program first to see available projects',
                    allowClear: true,
                    width: '100%',
                    closeOnSelect: false,
                    multiple: true
                });

                return;
            }

            // Show loading state
            projectsSelect.empty();
            projectsSelect.append('<option value="">Loading projects...</option>');
            projectsSelect.trigger('change');

            // Load projects via AJAX
            $.ajax({
                url: '{{ route("activities.get-projects-by-program") }}',
                method: 'GET',
                data: {
                    program_id: programId
                },
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    projectsSelect.empty();

                    if (response.success && response.projects && response.projects.length > 0) {
                        // Group projects by program_type
                        const groupedProjects = {};
                        response.projects.forEach(project => {
                            const groupKey = project.program_type || 'General';
                            if (!groupedProjects[groupKey]) {
                                groupedProjects[groupKey] = [];
                            }
                            groupedProjects[groupKey].push(project);
                        });

                        // Define order for program types
                        const programTypeOrder = [
                            'Flagship',
                            'Center',
                            'Center Program',
                            'Management',
                            'Local Program',
                            'Sub-Program'
                        ];

                        // Sort groups based on predefined order
                        const sortedGroupKeys = Object.keys(groupedProjects).sort((a, b) => {
                            const indexA = programTypeOrder.indexOf(a);
                            const indexB = programTypeOrder.indexOf(b);

                            if (indexA !== -1 && indexB !== -1) {
                                return indexA - indexB;
                            }
                            if (indexA !== -1) return -1;
                            if (indexB !== -1) return 1;
                            return a.localeCompare(b);
                        });

                        // Select existing projects for the activity
                        let selectedProjectIds = {!! json_encode(
                            json_decode($projects ?? '[]', true) ?: []
                        ) !!};

                        selectedProjectIds = selectedProjectIds.map(item => item.project_id);

                        // Add projects grouped by program_type
                        sortedGroupKeys.forEach(groupKey => {
                            if (groupedProjects[groupKey] && groupedProjects[groupKey].length > 0) {
                                // Sort projects within each group alphabetically
                                groupedProjects[groupKey].sort((a, b) => a.name.localeCompare(b.name));

                                const programGroup = $('<optgroup>').attr('label', groupKey);
                                groupedProjects[groupKey].forEach(project => {
                                    programGroup.append($('<option>')
                                        .val(project.project_id)
                                        .prop('selected', selectedProjectIds.includes(project.project_id))
                                        .text(project.name)
                                    );
                                });
                                projectsSelect.append(programGroup);
                            }
                        });

                    } else {
                        projectsSelect.append('<option value="">No projects available for selected program</option>');
                    }

                    projectsSelect.select2({
                        placeholder: 'Select projects...',
                        allowClear: true,
                        width: '100%',
                        closeOnSelect: false,
                        multiple: true
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error loading projects:', error);
                    projectsSelect.empty();
                    projectsSelect.append('<option value="">Error loading projects</option>');
                    projectsSelect.select2({
                        placeholder: 'Error loading projects',
                        allowClear: true,
                        width: '100%',
                        closeOnSelect: false,
                        multiple: true
                    });
                }
            });
        }

        function handleProgramSelection() {
            const $select = $('#programs_select');
            const selectedOption = $select.find('option:selected');
            const programExternalId = $select.val();
            const programId = selectedOption.data('program-id');

            // No valid selection
            if (!programExternalId || !programId) {
                return;
            }

            loadProjectsByProgram(programId, programExternalId);
        }

        $('#programs_select').on('change', handleProgramSelection);
        handleProgramSelection();

        // ============================================
        // ACTION PLAN, COMPONENTS, AND ACTIVITIES HANDLING
        // ============================================
        // Initialize Action Plan Select2
        $('#action_plan_id').select2({
            placeholder: 'Select an action plan...',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 10
        });

        // Initialize RP Components Select2
        $('#rp_component_id').select2({
            placeholder: 'Select a reporting component...',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 10
        });

        // Initialize RP Activities Select2
        $('#rp_activities_select').select2({
            placeholder: 'Select reporting activities...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            multiple: true
        });
        
        // Initialize the custom multiple select for focal points
        $('#focal_points_select').select2({
            placeholder: 'Select focal points...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            multiple: true
        });

        // Store selected component ID
        

        // Function to load components based on selected action plan
        function loadComponentsByActionPlan(actionPlanId) {
            const componentsSelect = $('#rp_component_id');
            const activitiesSelect = $('#rp_activities_select');
            
            if (actionPlanId) {
                // If no action plan selected, show all components
                componentsSelect.empty();
                componentsSelect.append('<option value="">Loading all components...</option>');
                componentsSelect.trigger('change');
                
                // Disable while loading
                componentsSelect.prop('disabled', true);
                
                // Load all components
                $.ajax({
                    url: '{{ route("activities.get-rp-components") }}?action_plan_id=' + encodeURIComponent(actionPlanId),
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        componentsSelect.empty();
                        componentsSelect.prop('disabled', false);
                        if (response.success && response.data && response.data.length > 0) {
                            componentsSelect.append('<option value="">Select a Reporting Component</option>');
                            
                            response.data.forEach(component => {
                                const option = $('<option>')
                                    .val(component.rp_components_id)
                                    .text(component.code + ' - ' + component.name);
                                  if (selectedComponentId == component.rp_components_id.toString()) {
                                        option.prop('selected', true);
                                    }
                                componentsSelect.append(option);
                            });
                            
                            componentsSelect.select2({
                                placeholder: 'Select a reporting component...',
                                allowClear: true,
                                width: '100%',
                                minimumResultsForSearch: 10
                            });
                        } else {
                            componentsSelect.append('<option value="">No components available</option>');
                        }
                        
                        // Clear activities when components are loaded/changed
                        activitiesSelect.empty();
                        activitiesSelect.append('<option value="" disabled>Select a reporting component first</option>');
                        activitiesSelect.trigger('change');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading components:', error);
                        componentsSelect.empty();
                        componentsSelect.append('<option value="">Error loading components</option>');
                        componentsSelect.prop('disabled', false);
                    }
                });
                
                return;
            }
            
            // Show loading state
            componentsSelect.empty();
            componentsSelect.append('<option value="">Loading components...</option>');
            componentsSelect.trigger('change');
            componentsSelect.prop('disabled', true);
           
        }

        // Function to load RP Activities based on component
        function loadRPActivitiesByComponent(componentId) {
            const activitiesSelect = $('#rp_activities_select');
            
            // Clear and show loading
            activitiesSelect.empty();
            activitiesSelect.append('<option value="">Loading activities...</option>');
            activitiesSelect.trigger('change');
            activitiesSelect.prop('disabled', true);
            
            // Use the actions endpoint
            $.ajax({
                url: '{{ route("activities.get-rp-actions-with-activities") }}',
                method: 'GET',
                data: { component_id: componentId },
                dataType: 'json',
                success: function(response) {
                    activitiesSelect.empty();
                    activitiesSelect.prop('disabled', false);
                    
                    if (response.success && response.data && Array.isArray(response.data)) {
                        if (response.data.length === 0) {
                            activitiesSelect.append('<option value="">No activities found for this component</option>');
                        } else {
                            // Process grouped data by actions
                            response.data.forEach(action => {
                                if (action.activities && action.activities.length > 0) {
                                    const optgroup = $('<optgroup>')
                                        .attr('label', action.action_code + ' - ' + action.action_name);
                                    
                                    action.activities.sort((a, b) => {
                                        const codeA = a.code || '';
                                        const codeB = b.code || '';
                                        return codeA.localeCompare(codeB);
                                    });
                                    
                                    action.activities.forEach(activity => {
                                        const activityText = activity.code + ' - ' + activity.name;
                                        const option = $('<option>')
                                            .val(activity.rp_activities_id)
                                            .text(activityText);
                                        if (selected_rp_activity_ids.includes(activity.rp_activities_id)) {
                                            option.prop('selected', true);
                                        }
                                        optgroup.append(option);
                                    });
                                    
                                    activitiesSelect.append(optgroup);
                                }
                            });
                            
                            activitiesSelect.select2({
                                placeholder: 'Select reporting activities...',
                                allowClear: true,
                                width: '100%',
                                closeOnSelect: false,
                                multiple: true
                            });
                            
                        }
                    } else {
                        activitiesSelect.append('<option value="">No activities found for this component</option>');
                        activitiesSelect.select2({
                            placeholder: 'No activities found',
                            allowClear: true,
                            width: '100%',
                            closeOnSelect: false,
                            multiple: true
                        });
                    }
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
                    activitiesSelect.prop('disabled', false);
                }
            });
        }

        // Event listener for action plan change
        $('#action_plan_id').on('change', function() {
            const actionPlanId = $(this).val();
            loadComponentsByActionPlan(actionPlanId);
        });

        // Event listener for component change
        $('#rp_component_id').on('change', function() {
            const componentId = $(this).val();
            if (componentId) {
                loadRPActivitiesByComponent(componentId);
            } else {
                // Clear activities if no component selected
                const activitiesSelect = $('#rp_activities_select');
                activitiesSelect.empty();
                activitiesSelect.append('<option value="" disabled>Select a reporting component first</option>');
                activitiesSelect.trigger('change');
            }
        });

        // Load components on page load if action plan is selected
        $(document).ready(function() {
            const initialActionPlanId = $('#action_plan_id').val();
            if (initialActionPlanId) {
                loadComponentsByActionPlan(initialActionPlanId);
            }

            if (selectedComponentId) {
                loadRPActivitiesByComponent(selectedComponentId);
            }   
            
        });

        // ============================================
        // VENUE HANDLING
        // ============================================
        const venueSelect = $('#venue_select'); // Display select
        const customVenueContainer = $('#customVenueContainer');
        const customVenueInput = $('#custom_venue');
        const venueHidden = $('#venue_hidden'); // Hidden input for final value

        // Function to handle venue selection change
        function handleVenueChange() {
            const selectedValue = venueSelect.val();
            
            if (selectedValue === 'Other') {
                // Show custom venue input
                customVenueContainer.slideDown(300);
                customVenueInput.prop('disabled', false).focus();
                customVenueInput.prop('required', true);
                
                // If custom venue already has a value, keep it
                if (!customVenueInput.val().trim()) {
                    customVenueInput.val('');
                }
                
                // Update hidden field with custom venue value if exists
                if (customVenueInput.val().trim()) {
                    venueHidden.val(customVenueInput.val().trim());
                } else {
                    venueHidden.val('');
                }
            } else {
                // Hide custom venue input
                customVenueContainer.slideUp(300);
                customVenueInput.prop('disabled', true);
                customVenueInput.prop('required', false);
                
                // Update hidden field with selected venue
                venueHidden.val(selectedValue);
            }
        }

        // Update hidden field when custom venue input changes
        customVenueInput.on('input', function() {
            if (venueSelect.val() === 'Other') {
                venueHidden.val($(this).val().trim());
            }
        });

        // Initial check on page load
        handleVenueChange();

        // Event listener for venue change
        venueSelect.on('change', handleVenueChange);

        // ============================================
        // FORM VALIDATION AND SUBMISSION
        // ============================================
        // Form submission validation
        $('#activityForm').on('submit', function(e) {
            if (venueSelect.val() === 'Other') {
                const customVenueValue = customVenueInput.val().trim();
                
                if (!customVenueValue) {
                    e.preventDefault();
                    customVenueInput.focus();
                    alert('Please enter a custom venue name.');
                    return;
                }
                
                // Ensure hidden field has the custom value
                venueHidden.val(customVenueValue);
            }
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
