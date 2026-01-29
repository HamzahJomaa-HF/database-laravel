@extends('layouts.app')

@section('title', 'Programs Management - Add Program')

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
            border-color: #1d4ed8;
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
            transition: all 0.2s;
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
        
        /* Select2 Custom Styling */
        .select2-container {
            width: 100% !important;
        }
        
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            min-height: 42px;
            padding: 0.375rem;
        }
        
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default .select2-selection--multiple:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        /* Custom Select2 dropdown colors */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary-color) !important;
            color: white !important;
        }
        
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #f3f4f6 !important;
            color: #041329 !important;
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
        
        .program-info {
            font-size: 0.75rem;
            color: var(--secondary-color);
            margin-top: 0.5rem;
            line-height: 1.4;
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
        
        .readonly-field {
            background-color: #f9fafb;
            color: #6b7280;
            cursor: not-allowed;
            padding: 0.625rem 0.875rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            min-height: 42px;
            display: flex;
            align-items: center;
        }
        
        .program-badge {
            display: inline-block;
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
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
        }
    </style>
@endsection

@section('content')
<div class="dashboard-content">
    <div class="form-container">
        <!-- Page Header -->
        <div class="d-flex flex-row w-100 justify-content-between mb-4">
            <div>
                <h1 class="page-title">Add New Subprogram</h1>
                <p class="page-subtitle">Create a new subprogram under an existing program</p>
            </div>
        </div>

        <!-- Form -->
       <form id="createProgramForm" action="{{ route('storeSubprogram') }}" method="POST">
            @csrf
            
            <!-- Hidden fields -->
            <input type="hidden" name="type" value="Program">
            <input type="hidden" name="program_type" id="program_type_hidden" value="Sub-Program">
            
            <div class="card">
                <div class="card-body">
                    <!-- Program Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Subprogram Information</h3>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">Subprogram Name</label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control" 
                                           placeholder="Enter subprogram name" 
                                           value="{{ old('name') }}"
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
                                           placeholder="Enter folder name (e.g., SP001)"
                                           value="{{ old('folder_name') }}">
                                    @error('folder_name')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="description">Description</label>
                                    <textarea name="description" 
                                              id="description" 
                                              class="form-control form-textarea" 
                                              placeholder="Enter subprogram description"
                                              rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="program_type_select">Program Type</label>
                                    <select name="program_type_select" id="program_type_select" class="form-control select2" required>
                                        <option value="">Select program type</option>
                                        <option value="Center">Center</option>
                                        <option value="Flagship">Flagship</option>
                                        <option value="Local Program">Local Program</option>
                                        <option value="Local Program/Network">Local Program/Network</option>
                                        <option value="Management">Management</option>
                                    </select>
                                    @error('program_type_select')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                    <div class="program-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Select the type of parent program you want to choose from
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="parent_program_id">Parent Program</label>
                                    <select name="parent_program_id" id="parent_program_id" class="form-control select2" required>
                                        <option value="">First select program type</option>
                                        <!-- Options will be populated by JavaScript -->
                                    </select>
                                    <div class="program-info">
                                        <i class="fas fa-info-circle"></i> 
                                        <span id="parentProgramHelp">
                                            Select a program type first to see available parent programs
                                        </span>
                                    </div>
                                    @error('parent_program_id')
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
                        <a href="{{ route('programs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Subprogram
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Store all programs from Blade in a JavaScript array
        const allParentPrograms = [
            @foreach($parentPrograms as $parent)
            {
                id: "{{ $parent->program_id }}",
                name: "{{ $parent->name }}",
                folder_name: "{{ $parent->folder_name }}",
                type: "{{ $parent->type }}",
                program_type: "{{ $parent->program_type }}",
                display_text: "{{ $parent->name }} @if($parent->folder_name)({{ $parent->folder_name }})@endif"
            },
            @endforeach
        ];
        
        console.log('All parent programs loaded:', allParentPrograms);
        
        $(document).ready(function() {
            // Initialize Select2
            $('#program_type_select').select2({
                placeholder: "Select program type",
                allowClear: false
            });
            
            $('#parent_program_id').select2({
                placeholder: "Select parent program",
                allowClear: false,
                width: '100%'
            });
            
            // Filter parent programs when program type is selected
            $('#program_type_select').on('change', function() {
                const selectedProgramType = $(this).val();
                const parentSelect = $('#parent_program_id');
                
                console.log('Selected program type:', selectedProgramType);
                
                // Clear current options
                parentSelect.empty();
                
                if (selectedProgramType) {
                    // Filter programs based on selected program_type
                    const filteredPrograms = allParentPrograms.filter(function(program) {
                        return program.program_type === selectedProgramType;
                    });
                    
                    console.log('Filtered programs for', selectedProgramType + ':', filteredPrograms);
                    
                    if (filteredPrograms.length > 0) {
                        // Add "Select" option
                        parentSelect.append('<option value="">Select parent program</option>');
                        
                        // Add filtered options
                        filteredPrograms.forEach(function(program) {
                            parentSelect.append(
                                $('<option></option>')
                                    .val(program.id)
                                    .text(program.display_text)
                            );
                        });
                        
                        // Update help text
                        $('#parentProgramHelp').html(`Select a ${selectedProgramType} program as parent`);
                    } else {
                        parentSelect.append('<option value="">No programs found for this type</option>');
                        $('#parentProgramHelp').html(`No ${selectedProgramType} programs available to select as parent`);
                    }
                } else {
                    parentSelect.append('<option value="">First select program type</option>');
                    $('#parentProgramHelp').html('Select a program type first to see available parent programs');
                }
                
                // Trigger change to refresh Select2
                parentSelect.trigger('change');
            });
            
            // Auto-generate folder name
            document.getElementById('name').addEventListener('blur', function() {
                const folderNameInput = document.getElementById('folder_name');
                const programName = this.value.trim();
                
                if (!folderNameInput.value && programName) {
                    const words = programName.split(' ');
                    let abbreviation = '';
                    
                    for (let i = 0; i < Math.min(3, words.length); i++) {
                        if (words[i].length > 0) {
                            abbreviation += words[i].charAt(0).toUpperCase();
                        }
                    }
                    
                    if (abbreviation) {
                        folderNameInput.value = abbreviation + '001';
                    }
                }
            });
            
            // Form validation
            document.getElementById('createProgramForm').addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const programType = document.getElementById('program_type_select').value;
                const parentProgram = document.getElementById('parent_program_id').value;
                
                let isValid = true;
                let errorMessage = '';
                
                if (!name) {
                    isValid = false;
                    errorMessage = 'Subprogram name is required';
                } else if (!programType) {
                    isValid = false;
                    errorMessage = 'Program type is required';
                } else if (!parentProgram) {
                    isValid = false;
                    errorMessage = 'Parent program is required';
                }
                
                if (!isValid) {
                    e.preventDefault();
                    alert(errorMessage);
                    return false;
                }
                
                return true;
            });
        });
    </script>
@endsection