@extends('layouts.app')

@section('title', 'COPs Management - Edit Community of Practice')

@section('styles')
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --border-color: #e5e7eb;
            --card-bg: #ffffff;
        }
        
        .page-title {
            font-weight: bold;
            color: var(--primary-color);
            margin: 0;
            font-size: 1.5rem;
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
        
        .btn-danger {
            background-color: #dc2626;
            color: white;
            border-color: #dc2626;
        }
        
        .btn-danger:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
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
            justify-content: space-between;
            align-items: center;
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
            color: #dc2626;
        }
        
        .error-message {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        .projects-info {
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
        
        .delete-form-container {
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .delete-form {
            display: inline;
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
            
            .delete-form-container {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
@endsection

@section('content')
<div class="dashboard-content">
    <div class="form-container">
        <!-- Page Header with Delete Button -->
        <div class="delete-form-container">
            <h1 class="page-title">Edit Community of Practice</h1>
            
            <!-- DELETE FORM -->
            <form action="{{ route('cops.destroy', $cop->cop_id) }}" 
                  method="POST" 
                  class="delete-form"
                  onsubmit="return confirm('Are you sure you want to delete this Community of Practice? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete COP
                </button>
            </form>
        </div>

        <!-- EDIT FORM -->
        <form id="editCopForm" action="{{ route('cops.update', $cop->cop_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card">
                <div class="card-body">
                    <!-- Community of Practice Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Community of Practice Information</h3>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label required" for="cop_name">COP Name</label>
                                    <input type="text" 
                                           name="cop_name" 
                                           id="cop_name" 
                                           class="form-control" 
                                           placeholder="Enter Community of Practice name" 
                                           value="{{ old('cop_name', $cop->cop_name) }}"
                                           required>
                                    @error('cop_name')
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
                                              placeholder="Enter Community of Practice description"
                                              rows="3">{{ old('description', $cop->description) }}</textarea>
                                    @error('description')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="external_id">External ID</label>
                                    <input type="text" 
                                           name="external_id" 
                                           id="external_id" 
                                           class="form-control" 
                                           placeholder="External identifier (optional)" 
                                           value="{{ old('external_id', $cop->external_id) }}">
                                    <div class="projects-info">
                                        <i class="fas fa-info-circle"></i> Auto-generated format: COP_YYYY_MM_XXX
                                    </div>
                                    @error('external_id')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- FOOTER WITH BUTTONS - DELETE BUTTON REMOVED -->
                <div class="card-footer">
                    <div class="buttons-wrapper">
                        <a href="{{ route('cops.index') }}" class="btn btn-outline-primary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Update Community of Practice
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
        // Form validation
        document.getElementById('editCopForm').addEventListener('submit', function(e) {
            const copName = document.getElementById('cop_name').value.trim();
            
            let isValid = true;
            let errorMessage = '';
            
            // Basic validation
            if (!copName) {
                isValid = false;
                errorMessage = 'Community of Practice name is required';
            }
            
            if (!isValid) {
                e.preventDefault();
                alert(errorMessage);
                return false;
            }
            
            return true;
        });
    </script>
@endsection