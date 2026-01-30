@extends('layouts.app')

@section('title', 'Programs Management - Edit Center')

@section('styles')
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
                <h1 class="page-title">Edit Center: {{ $program->name }}</h1>
                <p class="page-subtitle">Update the center details below</p>
            </div>
        </div>

        <!-- Form -->
<form id="editProgramForm" action="{{ route('updateCenter', $program->program_id) }}" method="POST">            @csrf
            @method('PUT')
            
            <!-- Hidden fields for program type and category - NOT displayed -->
            <input type="hidden" name="type" value="Center">
            <input type="hidden" name="program_type" value="Center">
            
            <div class="card">
                <div class="card-body">
                    <!-- Program Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Center Information</h3>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">Center Name</label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control" 
                                           placeholder="Enter center name" 
                                           value="{{ old('name', $program->name) }}"
                                           required>
                                    @error('name')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="folder_name">Folder Name</label>
                                    <input type="text" 
                                           name="folder_name" 
                                           id="folder_name" 
                                           class="form-control" 
                                           placeholder="Enter folder name (e.g., CENT001)"
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
                                              placeholder="Enter center description"
                                              rows="3">{{ old('description', $program->description) }}</textarea>
                                    @error('description')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Display readonly fields for type and program_type -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="Center" 
                                           readonly
                                           disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Program Type</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="Center" 
                                           readonly
                                           disabled>
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
                            <i class="fas fa-save"></i> Update Center
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
    
    <script>
        // Simplified form validation
        document.getElementById('editProgramForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            
            let isValid = true;
            let errorMessage = '';
            
            // Basic validation
            if (!name) {
                isValid = false;
                errorMessage = 'Center name is required';
            }
            
            if (!isValid) {
                e.preventDefault();
                alert(errorMessage);
                return false;
            }
            
            // Show loading spinner
            showLoading();
            
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