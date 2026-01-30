@extends('layouts.app')

@section('title', 'Programs Management - Edit Program')

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
        .select2-container--default .select2-results__option[aria-selected="true"] {
    background-color: #f3f4f6 !important; /* Light grey for selected */
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
        
        .program-info {
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
    </style>
@endsection

@section('content')
<div class="dashboard-content">
    <div class="form-container">
        <!-- Page Header -->
        <div class="d-flex flex-row w-100 justify-content-between mb-4">
            <div>
                <h1 class="page-title">Edit Program: {{ $program->name }}</h1>
                <p class="page-subtitle">Update the program details below</p>
            </div>
        </div>

        <!-- Form -->
       <form id="editProgramForm" action="{{ route('update.flagshiplocal', $program->program_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Hidden field for program type - default to "Program" -->
            <input type="hidden" name="type" value="Program">
            
            <div class="card">
                <div class="card-body">
                    <!-- Program Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Program Information</h3>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">Program Name</label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control" 
                                           placeholder="Enter program name" 
                                           value="{{ old('name', $program->name) }}"
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
                                           placeholder="Enter folder name (e.g., PROG001)"
                                           value="{{ old('folder_name', $program->folder_name) }}">
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
                                              placeholder="Enter program description"
                                              rows="3">{{ old('description', $program->description) }}</textarea>
                                    @error('description')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="program_type">Program Category</label>
                                    <select name="program_type" id="program_type" class="form-control select2" required>
                                        <option value="">Select program category</option>
                                        <option value="Flagship" {{ old('program_type', $program->program_type) == 'Flagship' ? 'selected' : '' }}>Flagship</option>
                                        <option value="Local Program" {{ old('program_type', $program->program_type) == 'Local Program' ? 'selected' : '' }}>Local Program</option>
                                    </select>
                                    @error('program_type')
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
                        <button type="submit" class="btn btn-primary" onclick="showLoading()">
                            <i class="fas fa-save"></i> Update Program
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
        <p class="mt-3">Updating Program...</p>
    </div>
</div>
@endsection

@section('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Initialize Select2 with minimal settings to prevent movement
        $(document).ready(function() {
            $('#program_type').select2({
                placeholder: "Select program category",
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: 10 // Only show search if more than 10 options
            });
            
            $('#parent_program_id').select2({
                placeholder: "Select parent center",
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 10
            });
            
            // IMPORTANT: Fix to prevent movement on dropdown open
            $('.select2').on('select2:open', function(e) {
                // Fix: Set dropdown position to absolute and prevent margin changes
                setTimeout(function() {
                    $('.select2-dropdown').css({
                        'position': 'absolute',
                        'margin-top': '1px',
                        'z-index': '1051'
                    });
                }, 10);
            });
        });
        
        // Form validation and loading
        document.getElementById('editProgramForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const programType = document.getElementById('program_type').value;
            
            let isValid = true;
            let errorMessage = '';
            
            // Basic validation
            if (!name) {
                isValid = false;
                errorMessage = 'Program name is required';
            } else if (!programType) {
                isValid = false;
                errorMessage = 'Program category is required';
            }
            
            if (!isValid) {
                e.preventDefault();
                alert(errorMessage);
                return false;
            }
            
            // Show loading spinner
            document.getElementById('globalLoading').style.display = 'flex';
            
            return true;
        });
        
        // Loading spinner functions
        function showLoading() {
            document.getElementById('globalLoading').style.display = 'flex';
        }
        
        function hideLoading() {
            document.getElementById('globalLoading').style.display = 'none';
        }
        
        // Display success message if exists
        @if(session('success'))
            alert('{{ session('success') }}');
        @endif
        
        // Display error message if exists
        @if(session('error'))
            alert('{{ session('error') }}');
        @endif
        
    </script>
@endsection