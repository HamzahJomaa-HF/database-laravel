@extends('layouts.app')

@section('title', 'Projects Management - Edit Project')

@section('styles')
    <!-- Select2 for enhanced dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --border-color: #e5e7eb;
            --card-bg: #ffffff;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }
        
        .page-title {
            font-weight: bold;
            color: var(--primary-color);
            margin: 0;
            font-size: 1.5rem;
        }
        
        .page-subtitle {
            color: var(--secondary-color);
            margin: 0.5rem 0 1.5rem 0;
            font-size: 1rem;
        }
        
        .buttons-wrapper {
            display: flex;
            gap: 0.75rem;
        }
        
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid transparent;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8;
            border-color: #c1c8dc;
        }
        
        .btn-outline-primary {
            background-color: white;
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: #eff6ff;
        }
        
        .btn-outline-secondary {
            background-color: white;
            color: var(--secondary-color);
            border-color: var(--border-color);
        }
        
        .btn-outline-secondary:hover {
            background-color: #f9fafb;
        }
        
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .card-footer {
            padding: 1rem 1.5rem;
            background-color: #f9fafb;
            border-top: 1px solid var(--border-color);
            border-radius: 0 0 0.5rem 0.5rem;
            display: flex;
            justify-content: flex-end;
        }
        
        .form-label {
            font-weight: 500;
            color: #4b5563;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            display: block;
        }
        
        .form-control {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .required::after {
            content: " *";
            color: var(--danger-color);
        }
        
        /* FIXED Select2 CSS - No Movement */
        .select2-container {
            width: 100% !important;
        }
        
        .select2-container .select2-selection--single {
            height: 42px !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 0.375rem !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
            padding-left: 12px !important;
            padding-right: 30px !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            width: 30px !important;
            right: 3px !important;
        }
        
        /* CRITICAL FIX: Prevent movement on focus */
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(33, 67, 139, 0.1) !important;
            outline: none !important;
        }
        
        /* Style for optgroup headers (program_type groups) */
        .select2-results__option[role="group"] {
            padding: 0 !important;
        }
        
        .select2-results__group {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            padding: 8px 12px !important;
            border-bottom: 1px solid #dee2e6;
            margin-top: 5px;
        }
        
        .select2-results__option {
            padding-left: 24px !important;
        }
        
        /* Hover effect for options */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #e9ecef !important;
            color: #495057 !important;
        }
        
        /* Selected option */
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #f3f4f6 !important;
            color: #4b5563 !important;
        }
        
        /* Ensure Select2 dropdown doesn't shift elements */
        .select2-dropdown {
            border-color: var(--primary-color) !important;
            margin-top: 0 !important;
            position: absolute !important;
        }
        
        .error-message {
            color: var(--danger-color);
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        .success-message {
            color: var(--success-color);
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        .warning-message {
            color: var(--warning-color);
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        .project-info {
            font-size: 0.75rem;
            color: var(--secondary-color);
            margin-top: 0.5rem;
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .form-container {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
        }
        
        /* Date inputs */
        input[type="date"] {
            padding: 0.625rem 0.875rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            width: 100%;
        }
        
        /* Add this for main container */
        .dashboard-content {
            padding: 1.5rem;
            width: 100%;
        }
        
        @media (max-width: 768px) {
            .buttons-wrapper {
                flex-direction: column;
                width: 100%;
            }
            
            .buttons-wrapper .btn {
                width: 100%;
            }
            
            .card-footer {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .dashboard-content {
                padding: 1rem;
            }
        }
        
        /* Loading overlay */
        #globalLoading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        
        /* Status indicator */
        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .status-active { background-color: var(--success-color); }
        .status-inactive { background-color: var(--danger-color); }
    </style>
@endsection

@section('content')
<div class="dashboard-content">
    <div class="form-container">
        <!-- Page Header -->
        <div class="d-flex flex-row w-100 justify-content-between mb-4">
            <div>
                <h1 class="page-title">Edit Project</h1>
                <p class="page-subtitle">Update project details below</p>
            </div>
        </div>

        <!-- Form -->
        <form id="editProjectForm" action="{{ route('projects.update', $project->project_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card">
                <div class="card-body">
                    <!-- Project Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Project Information</h3>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">Project Name</label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control" 
                                           placeholder="Enter project name" 
                                           value="{{ old('name', $project->name) }}"
                                           required>
                                    @error('name')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="folder_name">Folder Name (Optional)</label>
                                    <input type="text" 
                                           name="folder_name" 
                                           id="folder_name" 
                                           class="form-control" 
                                           placeholder="Enter folder name"
                                           value="{{ old('folder_name', $project->folder_name) }}">
                                    @error('folder_name')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="program_id">Program</label>
                                    <select name="program_id" id="program_id" class="form-control select2" required>
                                        <option value="">Select a program</option>
                                        @php
                                            // Create flat list with program type as prefix
                                            $flatPrograms = [];
                                            foreach($programsGrouped as $programType => $programsInGroup) {
                                                foreach($programsInGroup as $program) {
                                                    $flatPrograms[] = [
                                                        'id' => $program->program_id,
                                                        'text' => "[{$programType}] {$program->name}" . 
                                                                 (!empty($program->folder_name) ? " ({$program->folder_name})" : ''),
                                                        'type' => $program->type,
                                                        'program_type' => $program->program_type
                                                    ];
                                                }
                                            }
                                            
                                            // Sort alphabetically
                                            usort($flatPrograms, function($a, $b) {
                                                return strcmp($a['text'], $b['text']);
                                            });
                                        @endphp
                                        
                                        @foreach($flatPrograms as $program)
                                            <option value="{{ $program['id'] }}" 
                                                    data-type="{{ $program['type'] }}"
                                                    data-program-type="{{ $program['program_type'] }}"
                                                    {{ old('program_id', $project->program_id) == $program['id'] ? 'selected' : '' }}>
                                                {{ $program['text'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('program_id')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                    
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="parent_project_id">Parent Project (Optional)</label>
                                    <select name="parent_project_id" id="parent_project_id" class="form-control select2">
                                        <option value="">Select parent project</option>
                                        @foreach($parentProjects as $parent)
                                            <option value="{{ $parent->project_id }}" 
                                                    {{ old('parent_project_id', $project->parent_project_id) == $parent->project_id ? 'selected' : '' }}>
                                                {{ $parent->name }}
                                                @if($parent->external_id)
                                                    ({{ $parent->external_id }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_project_id')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="start_date">Start Date</label>
                                    <input type="date" 
                                           name="start_date" 
                                           id="start_date" 
                                           class="form-control" 
                                           value="{{ old('start_date', $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : date('Y-m-d')) }}"
                                           required>
                                    @error('start_date')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="end_date">End Date (Optional)</label>
                                    <input type="date" 
                                           name="end_date" 
                                           id="end_date" 
                                           class="form-control" 
                                           value="{{ old('end_date', $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '') }}">
                                    @error('end_date')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                     
                    </div>
                </div>
                
                <!-- FOOTER WITH BUTTONS -->
                <div class="card-footer">
                    <div class="buttons-wrapper">
                        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" onclick="showLoading()">
                            <i class="fas fa-save"></i> Update Project
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div id="globalLoading" style="display: none;">
    <div style="text-align: center;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3">Updating Project...</p>
    </div>
</div>
@endsection

@section('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Initialize Select2 for all dropdowns
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: function() {
                    return $(this).data('placeholder') || "Select an option";
                },
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 10
            });
            
            // Fix dropdown positioning
            $('.select2').on('select2:open', function(e) {
                setTimeout(function() {
                    $('.select2-dropdown').css({
                        'position': 'absolute',
                        'margin-top': '1px',
                        'z-index': '1051'
                    });
                }, 10);
            });
        });
        
        // Show program info when program is selected
        $('#program_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const programType = selectedOption.data('type');
            const programCategory = selectedOption.data('program-type');
            
            if (programType && programCategory) {
                $('#programType').text(programType);
                $('#programCategory').text(programCategory);
                $('#programInfo').show();
            } else {
                $('#programInfo').hide();
            }
        });
        
        // Auto-generate folder name from project name
        $('#name').on('blur', function() {
            const folderNameInput = $('#folder_name');
            const projectName = $(this).val().trim();
            
            // Only auto-generate if folder name is empty
            if (!folderNameInput.val() && projectName) {
                // Create abbreviation (first 3 letters of first word + numbers)
                const firstWord = projectName.split(' ')[0];
                let abbreviation = '';
                
                // Take first 3 characters of first word
                for (let i = 0; i < Math.min(3, firstWord.length); i++) {
                    abbreviation += firstWord.charAt(i).toUpperCase();
                }
                
                // Add timestamp for uniqueness
                if (abbreviation) {
                    const timestamp = new Date().getTime().toString().slice(-3);
                    folderNameInput.val(abbreviation + '_' + timestamp);
                }
            }
        });
        
        // Set min date for end date based on start date
        $('#start_date').on('change', function() {
            const startDate = $(this).val();
            const endDateInput = $('#end_date');
            
            if (startDate) {
                endDateInput.attr('min', startDate);
                
                // If end date is before start date, clear it
                if (endDateInput.val() && endDateInput.val() < startDate) {
                    endDateInput.val('');
                }
            }
        });
        
        // Form validation
        $('#editProjectForm').on('submit', function(e) {
            const name = $('#name').val().trim();
            const projectType = $('#project_type').val();
            const programId = $('#program_id').val();
            const startDate = $('#start_date').val();
            
            let isValid = true;
            let errorMessage = '';
            
            // Basic validation
            if (!name) {
                isValid = false;
                errorMessage = 'Project name is required';
            }  else if (!programId) {
                isValid = false;
                errorMessage = 'Program is required';
            } else if (!startDate) {
                isValid = false;
                errorMessage = 'Start date is required';
            }
            
            // Date validation
            if (isValid) {
                const endDate = $('#end_date').val();
                if (endDate && endDate < startDate) {
                    isValid = false;
                    errorMessage = 'End date cannot be before start date';
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                alert(errorMessage);
                return false;
            }
            
            // Show loading spinner
            $('#globalLoading').css('display', 'flex');
            
            return true;
        });
        
        // Loading spinner functions
        function showLoading() {
            $('#globalLoading').css('display', 'flex');
        }
        
        function hideLoading() {
            $('#globalLoading').css('display', 'none');
        }
        
        // Initialize program info if there's already a selected program
        $(document).ready(function() {
            // Trigger change event to show program info
            if ($('#program_id').val()) {
                $('#program_id').trigger('change');
            }
            
            // Initialize end date min attribute based on start date
            const startDate = $('#start_date').val();
            if (startDate) {
                $('#end_date').attr('min', startDate);
            }
            
            // Set up project info display
            const currentProgramOption = $('#program_id option:selected');
            if (currentProgramOption.length > 0 && currentProgramOption.val()) {
                const programType = currentProgramOption.data('type');
                const programCategory = currentProgramOption.data('program-type');
                
                if (programType && programCategory) {
                    $('#programType').text(programType);
                    $('#programCategory').text(programCategory);
                    $('#programInfo').show();
                }
            }
        });
    </script>
@endsection