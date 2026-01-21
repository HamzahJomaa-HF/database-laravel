@extends('layouts.app')

@section('title', 'Portfolios Management - Add Portfolio')

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
            color: #dc2626;
        }
        
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
        
        .error-message {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        
        /* Multi-select chips styling */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #e0f2fe;
            border: 1px solid #bae6fd;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            color: #0369a1;
            margin-right: 0.25rem;
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
            <h1 class="page-title">Add Portfolio</h1>
        </div>

        <!-- Form -->
        <form id="createPortfolioForm" action="{{ route('portfolios.store') }}" method="POST">
            @csrf
            
            <div class="card">
                <div class="card-body">
                    <!-- Portfolio Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Portfolio Information</h3>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label required" for="name">Portfolio Name</label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           class="form-control" 
                                           placeholder="Enter portfolio name" 
                                           value="{{ old('name') }}"
                                           required>
                                    @error('name')
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
                                              placeholder="Enter portfolio description"
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
                                    <label class="form-label" for="type">Portfolio Type</label>
                                    <select name="type" id="type" class="form-control select2">
                                        <option value="">Select type (optional)</option>
                                        <option value="Active Citizenship" {{ old('type') == 'Active Citizenship' ? 'selected' : '' }}>Active Citizenship</option>
                                        <option value="Education & Digital Transformation" {{ old('type') == 'Education & Digital Transformation' ? 'selected' : '' }}>Education & Digital Transformation</option>
                                        <option value="Public Health & Wellbeing" {{ old('type') == 'Public Health & Wellbeing' ? 'selected' : '' }}>Public Health & Wellbeing</option>
                                        <option value="Sustainability & Climate Action" {{ old('type') == 'Sustainability & Climate Action' ? 'selected' : '' }}>Sustainability & Climate Action</option>
                                        <option value="Socio-economic Development" {{ old('type') == 'Socio-economic Development' ? 'selected' : '' }}>Socio-economic Development</option>
                                        <option value="Regional" {{ old('type') == 'Regional' ? 'selected' : '' }}>Regional</option>
                                    </select>
                                    @error('type')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COPs Association -->
                    <div class="form-section">
                        <h3 class="form-section-title">COP Associations</h3>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="cops">Associated COPs</label>
                                    <select name="cops[]" id="cops" class="form-control select2" multiple="multiple">
                                        @php
                                            $oldCopIds = old('cops', []);
                                        @endphp
                                        
                                        @foreach($cops as $cop)
                                            <option value="{{ $cop->cop_id }}" 
                                                {{ in_array($cop->cop_id, $oldCopIds) ? 'selected' : '' }}>
                                                {{ $cop->cop_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="projects-info">
                                    </div>
                                    @error('cops')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                    @error('cops.*')
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
                        <a href="{{ route('portfolios.index') }}" class="btn btn-outline-primary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Create Portfolio
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
        // Initialize Select2
        $(document).ready(function() {
            // Single select for portfolio type
            $('#type').select2({
                placeholder: "Select portfolio type (optional)",
                allowClear: true
            });
            
            // Multiple select for COPs
            $('#cops').select2({
                placeholder: "Select COPs (optional)",
                allowClear: true,
                closeOnSelect: false,
                width: '100%',
                tags: false,
            });
        });

        // Form validation
        document.getElementById('createPortfolioForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const cops = $('#cops').val();
            
            let isValid = true;
            let errorMessage = '';
            
            // Basic validation
            if (!name) {
                isValid = false;
                errorMessage = 'Portfolio name is required';
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