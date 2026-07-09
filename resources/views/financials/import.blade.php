@extends('layouts.app')

@section('title', 'Import Financial Records')

@section('content')
<div class="dashboard-content">
    <div class="main-div">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="page-title">Import Financial Records</h4>
            <a href="{{ route('financials.index') }}" class="btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Error:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('financials.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- Activity Selection (Livewire Enhanced Search) --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Select Activity <span class="text-danger">*</span>
                        </label>

                        @livewire('activity-selector')

                        @error('activity_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        <div class="form-text mt-2">
                            <i class="bi bi-search me-1"></i>
                            Type to search by title, date, or venue
                        </div>
                    </div>
                    
                    {{-- Financial Type Selection --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Financial Type <span class="text-danger">*</span></label>
                        <select name="financial_type" id="financial_type" class="form-control" required>
                            <option value="">-- Select Financial Type --</option>
                            <option value="omt">OMT (Operational)</option>
                            <option value="medical">Medical Financial Help</option>
                            <option value="education">Education Financial Help</option>
                        </select>
                    </div>
                    
                    {{-- File Upload --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Import File <span class="text-danger">*</span></label>
                        <input type="file" name="import_file" class="form-control" accept=".csv,.xlsx,.xls" required>
                        <small class="text-muted">Accepted formats: CSV, Excel (.xlsx, .xls). Max size: 100MB</small>
                    </div>
                    
                    {{-- Template Download --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Download Template</label>
                        <div class="d-flex gap-2">
                            <a href="{{ route('financials.import.template', 'omt') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-file-csv"></i> OMT Template
                            </a>
                            <a href="{{ route('financials.import.template', 'medical') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-file-csv"></i> Medical Template
                            </a>
                            <a href="{{ route('financials.import.template', 'education') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-file-csv"></i> Education Template
                            </a>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-upload"></i> Import Financial Records
                    </button>
                    <a href="{{ route('financials.index') }}" class="btn-outline ms-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .page-title { font-weight: bold; color: #2563eb; margin-bottom: 1.5rem; font-size: 1.5rem; }
    .main-div { background-color: white; border-radius: 0.5rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .btn-primary { background-color: #2563eb; color: white; border: none; padding: 0.625rem 1.25rem; border-radius: 0.375rem; }
    .btn-outline { background-color: white; color: #2563eb; border: 1px solid #2563eb; padding: 0.625rem 1.25rem; border-radius: 0.375rem; text-decoration: none; }
    .btn-secondary { background-color: #6c757d; color: white; border: none; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; }
    .form-control { width: 100%; padding: 0.625rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; }
    .form-label { font-weight: 600; margin-bottom: 0.5rem; display: block; }
    .alert { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; }
    .alert-danger { background-color: #fee2e2; border: 1px solid #fecaca; color: #dc2626; }
    .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border-radius: 0.5rem; }
    .mb-3 { margin-bottom: 1rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .me-2 { margin-right: 0.5rem; }
    .ms-2 { margin-left: 0.5rem; }
    .gap-2 { gap: 0.5rem; }
    .d-flex { display: flex; }
    .text-danger { color: #dc2626; }
    .text-muted { color: #6b7280; }
</style>
@endsection