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
                                            <option value="Workshop" {{ old('activity_type', 'Training') == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                                            <option value="Training" selected>Training</option>
                                            <option value="Seminar" {{ old('activity_type', 'Training') == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                                            <option value="Conference" {{ old('activity_type', 'Training') == 'Conference' ? 'selected' : '' }}>Conference</option>
                                        </select>
                                        @error('activity_type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Project --}}
                                    <div class="col-md-6">
                                        <label for="project" class="form-label fw-semibold">Project</label>
                                        <select name="project" id="project" class="form-control form-select @error('project') is-invalid @enderror">
                                            <option value="">Select Project</option>
                                            <option value="Education Development Project" selected>Education Development Project</option>
                                            <option value="Youth Empowerment Project" {{ old('project') == 'Youth Empowerment Project' ? 'selected' : '' }}>Youth Empowerment Project</option>
                                            <option value="Teacher Capacity Project" {{ old('project') == 'Teacher Capacity Project' ? 'selected' : '' }}>Teacher Capacity Project</option>
                                        </select>
                                        @error('project')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================================ --}}
                        {{-- SECTION 2: DATES AND VENUE --}}
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

                                    {{-- Venue --}}
                                    <div class="col-md-12">
                                        <label for="venue" class="form-label fw-semibold">Venue</label>
                                        <input type="text" name="venue" id="venue" 
                                               class="form-control @error('venue') is-invalid @enderror" 
                                               value="Hariri Foundation Headquarters" 
                                               placeholder="Venue location">
                                        @error('venue')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ================================== --}}
                        {{-- SECTION 3: CONTENT AND NETWORK --}}
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
                        {{-- SECTION 4: REPORTING ACTIVITIES (Multiple Select) --}}
                        {{-- ====================================== --}}
                        <div class="section-card mb-4">
                            <div class="section-header">
                                <h6 class="mb-0 fw-semibold">Reporting Activities</h6>
                                <span class="text-muted small">Select one or more reporting activities</span>
                            </div>
                            <div class="section-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-0">
                                            <label for="reporting_activities_select" class="form-label fw-semibold mb-2">Select Reporting Activities</label>
                                            <select id="reporting_activities_select" 
                                                    multiple
                                                    class="form-control @error('reporting_activities') is-invalid @enderror"
                                                    name="reporting_activities[]">
                                                @php
                                                    // Sample data - replace with actual data from controller
                                                    $reportingActivities = [
                                                        ['id' => 1, 'name' => 'Capacity Building'],
                                                        ['id' => 2, 'name' => 'Teacher Training'],
                                                        ['id' => 3, 'name' => 'Infrastructure Support'],
                                                        ['id' => 4, 'name' => 'Policy Development'],
                                                        ['id' => 5, 'name' => 'Community Outreach'],
                                                        ['id' => 6, 'name' => 'Monitoring & Evaluation'],
                                                    ];
                                                @endphp
                                                @foreach($reportingActivities as $activity)
                                                    <option value="{{ $activity['id'] }}" 
                                                            {{ in_array($activity['id'], [1, 2]) ? 'selected' : '' }}>
                                                        {{ $activity['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('reporting_activities')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            @error('reporting_activities.*')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ====================================== --}}
                        {{-- SECTION 5: FOCAL POINTS (Multiple Select) --}}
                        {{-- ====================================== --}}
                        <div class="section-card mb-5">
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
                                                    // Sample data - replace with actual data from controller
                                                    $focalPoints = [
                                                        ['id' => 1, 'name' => 'Education Officer'],
                                                        ['id' => 2, 'name' => 'Program Manager'],
                                                        ['id' => 3, 'name' => 'Monitoring Officer'],
                                                        ['id' => 4, 'name' => 'Field Coordinator'],
                                                        ['id' => 5, 'name' => 'Project Director'],
                                                        ['id' => 6, 'name' => 'Technical Advisor'],
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

                        {{-- ======================== --}}
                        {{-- SECTION 6: ACTION BUTTONS --}}
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
    /* Section Styling - Same as first form */
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
    
    /* Select2 Styling to match form controls - Same as first form */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        padding: 0.25rem 0.5rem !important;
        background-color: #fff !important;
        transition: all 0.3s ease !important;
        width: '100%' !important;
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
    
    /* Ensure proper height for all inputs */
    .form-control:not(textarea) {
        height: 42px;
    }
    
    /* Make Select2 match regular input height */
    .select2-container .select2-selection--multiple {
        min-height: 42px;
        height: auto;
    }
    
    /* Fix for Select2 dropdown positioning */
    .select2-container {
        z-index: 1055 !important;
    }
    
    .select2-dropdown {
        z-index: 1060 !important;
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
        // Initialize the custom multiple select for reporting activities
        $('#reporting_activities_select').select2({
            placeholder: 'Select reporting activities...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            dropdownParent: $('#reporting_activities_select').parent(),
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
                    return "No activities found";
                }
            }
        }).on('select2:open', function() {
            // Ensure dropdown is properly positioned
            $(this).data('select2').$dropdown.css('width', $(this).outerWidth() + 'px');
        });
        
        // Initialize the custom multiple select for focal points
        $('#focal_points_select').select2({
            placeholder: 'Select focal points...',
            allowClear: true,
            width: '100%',
            closeOnSelect: false,
            dropdownParent: $('#focal_points_select').parent(),
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
                    return "No focal points found";
                }
            }
        }).on('select2:open', function() {
            // Ensure dropdown is properly positioned
            $(this).data('select2').$dropdown.css('width', $(this).outerWidth() + 'px');
        });

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
                $('#reporting_activities_select').val(null).trigger('change');
                $('#focal_points_select').val(null).trigger('change');
                
                // Reset submit button
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
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
        $('#reporting_activities_select, #focal_points_select').on('change', function() {
            if ($(this).val() && $(this).val().length > 0) {
                $(this).removeClass('is-invalid');
            }
        });
    });
</script>
@endsection