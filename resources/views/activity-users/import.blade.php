{{-- resources/views/activity-users/import.blade.php --}}
@extends('layouts.app')

@section('title', 'Import Users to Activity')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('activity-users.index') }}" class="text-decoration-none">Activity Users</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Import Users</li>
                        </ol>
                    </nav>
                    <h1 class="h2 fw-bold mb-1">Import Users to Activity</h1>
                    <p class="text-muted mb-0">Bulk import users and assign them to a specific activity</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">Import Configuration</h5>
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
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('activity-users.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Activity Selection --}}
                        <div class="mb-4">
                            <label for="activity_id" class="form-label fw-semibold">
                                Select Activity <span class="text-danger">*</span>
                            </label>
                            <select name="activity_id" id="activity_id" class="form-control form-select" required>
                                <option value="">-- Select an Activity --</option>
                                @foreach($activities as $activity)
                                    <option value="{{ $activity->activity_id }}" {{ old('activity_id') == $activity->activity_id ? 'selected' : '' }}>
                                        {{ $activity->activity_title_en }} 
                                        @if($activity->start_date)
                                            ({{ \Carbon\Carbon::parse($activity->start_date)->format('Y-m-d') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Users will be assigned to this activity
                            </div>
                        </div>

                        {{-- COP Selection (Optional) --}}
                        <div class="mb-4">
                            <label for="cop_id" class="form-label fw-semibold">
                                Community of Practice (Optional)
                            </label>
                            <select name="cop_id" id="cop_id" class="form-control form-select">
                                <option value="">-- Select COP --</option>
                                @foreach($cops as $cop)
                                    <option value="{{ $cop->cop_id }}" {{ old('cop_id') == $cop->cop_id ? 'selected' : '' }}>
                                        {{ $cop->cop_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Assign users to a specific Community of Practice
                            </div>
                        </div>

                        {{-- File Upload --}}
                        <div class="mb-4">
                            <label for="import_file" class="form-label fw-semibold">
                                Import File <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="import_file" id="import_file" 
                                   class="form-control @error('import_file') is-invalid @enderror" 
                                   accept=".csv,.xlsx,.xls" required>
                            @error('import_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2">
                                <i class="bi bi-file-excel me-1"></i>
                                Supported formats: CSV, Excel (.xlsx, .xls)<br>
                                <a href="{{ route('activity-users.download-template') }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-download me-1"></i> Download Template
                                </a>
                            </div>
                        </div>

                        {{-- Default Values --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="invited_default" id="invited_default" 
                                           class="form-check-input" value="1" {{ old('invited_default') ? 'checked' : '' }}>
                                    <label for="invited_default" class="form-check-label">
                                        Set all imported users as <strong>Invited</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="attended_default" id="attended_default" 
                                           class="form-check-input" value="1" {{ old('attended_default') ? 'checked' : '' }}>
                                    <label for="attended_default" class="form-check-label">
                                        Set all imported users as <strong>Attended</strong>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- User Type Default --}}
                        <div class="mb-4">
                            <label for="default_user_type" class="form-label fw-semibold">
                                Default User Type (if not specified in file)
                            </label>
                            <select name="default_user_type" id="default_user_type" class="form-control form-select">
                                <option value="">-- Keep as null --</option>
                                <option value="Stakeholder" {{ old('default_user_type') == 'Stakeholder' ? 'selected' : '' }}>Stakeholder</option>
                                <option value="Beneficiary" {{ old('default_user_type') == 'Beneficiary' ? 'selected' : '' }}>Beneficiary</option>
                            </select>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cloud-upload me-1"></i> Import Users
                            </button>
                            <a href="{{ route('activity-users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
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
    .form-label {
        font-weight: 500;
    }
    .form-check-input {
        cursor: pointer;
    }
</style>
@endsection