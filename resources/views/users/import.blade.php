@extends('layouts.app')

@section('title', 'Import Users')

@section('content')
<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap align-items-center gap-3">
                <div>
                    <nav aria-label="breadcrumb" class="mb-2">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Users</a></li>
                            <li class="breadcrumb-item active">Import Users</li>
                        </ol>
                    </nav>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-cloud-upload me-2 text-primary"></i>Import Users
                    </h1>
                    <p class="text-muted mb-0">Bulk import users from CSV file</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-semibold text-dark mb-0 d-flex align-items-center">
                        <i class="bi bi-file-earmark-spreadsheet me-2 text-primary"></i>Upload File
                    </h5>
                </div>
                <div class="card-body p-4">

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Warning Message --}}
                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-x-circle-fill me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Import Errors Details --}}
                    @if(session('error_details'))
                        <div class="alert alert-danger">
                            <h6 class="alert-heading mb-2">Import Errors:</h6>
                            <div class="small">{!! session('error_details') !!}</div>
                        </div>
                    @endif

                    {{-- Instructions --}}
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                            <div>
                                <h6 class="alert-heading mb-2">Import Instructions</h6>
                                <ul class="mb-0 ps-3 small">
                                    <li>Download the template file to ensure proper formatting</li>
                                    <li>Required fields: <strong>first_name</strong>, <strong>last_name</strong></li>
                                    <li>Supported formats: CSV, TXT</li>
                                    <li>Maximum file size: 10MB</li>
                                    <li>Date format: YYYY-MM-DD (e.g., 1990-05-15)</li>
                                    <li>Gender: Male or Female</li>
                                    <li>Type: Stakeholder, Employee, Admin, Customer, Partner, or Beneficiary</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Download Template --}}
                    <div class="mb-4">
                        <a href="{{ route('users.import.template') }}" class="btn btn-outline-primary">
                            <i class="bi bi-download me-2"></i>Download CSV Template
                        </a>
                        <small class="text-muted ms-2">Use this template for correct formatting</small>
                    </div>

                    {{-- Upload Form --}}
                    <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="import_file" class="form-label fw-semibold">Select CSV File <span class="text-danger">*</span></label>
                            <input type="file" 
                                   class="form-control @error('import_file') is-invalid @enderror" 
                                   id="import_file" 
                                   name="import_file"
                                   accept=".csv,.txt"
                                   required>
                            @error('import_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Accepted formats: CSV, TXT (Max: 10MB)</div>
                        </div>

                        {{-- Preview Section --}}
                        <div class="mb-4 d-none" id="filePreview">
                            <h6 class="fw-semibold mb-2">File Preview (First 5 rows)</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="previewTable">
                                    <thead class="table-light">
                                        <tr id="previewHeaders"></tr>
                                    </thead>
                                    <tbody id="previewRows"></tbody>
                                </table>
                            </div>
                            <div class="form-text" id="previewInfo"></div>
                        </div>

                        {{-- Required Fields Note --}}
                        <div class="alert alert-light border mb-4">
                            <div class="d-flex">
                                <i class="bi bi-lightbulb me-2 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Required Fields Note</h6>
                                    <p class="mb-0 small">Only <strong>first_name</strong> and <strong>last_name</strong> are required. All other fields are optional.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-cloud-upload me-2"></i> Import Users
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                Cancel
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
    .card {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .table-sm td, .table-sm th {
        padding: 0.5rem;
        font-size: 0.875rem;
    }
    
    .btn {
        border-radius: 6px;
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('import_file');
    const filePreview = document.getElementById('filePreview');
    const previewHeaders = document.getElementById('previewHeaders');
    const previewRows = document.getElementById('previewRows');
    const previewInfo = document.getElementById('previewInfo');
    const submitBtn = document.getElementById('submitBtn');
    const importForm = document.getElementById('importForm');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Reset preview
        filePreview.classList.add('d-none');
        previewHeaders.innerHTML = '';
        previewRows.innerHTML = '';

        // Check file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            previewInfo.textContent = 'File size exceeds 10MB limit';
            filePreview.classList.remove('d-none');
            return;
        }

        // Only preview CSV and TXT files
        if (!file.name.toLowerCase().endsWith('.csv') && !file.name.toLowerCase().endsWith('.txt')) {
            previewInfo.textContent = 'Preview available only for CSV and TXT files';
            filePreview.classList.remove('d-none');
            return;
        }

        const reader = new FileReader();
        
        reader.onload = function(e) {
            try {
                const csv = e.target.result;
                const lines = csv.split('\n').filter(line => line.trim() !== '');
                
                if (lines.length === 0) {
                    previewInfo.textContent = 'File is empty';
                    filePreview.classList.remove('d-none');
                    return;
                }

                // Parse headers
                const headers = parseCSVLine(lines[0]);
                previewHeaders.innerHTML = headers.map(header => 
                    `<th class="text-nowrap">${escapeHtml(header)}</th>`
                ).join('');

                // Parse first 5 rows for preview
                const previewData = [];
                const maxPreviewRows = 5;
                for (let i = 1; i < Math.min(maxPreviewRows + 1, lines.length); i++) {
                    const rowData = parseCSVLine(lines[i]);
                    if (rowData.some(cell => cell.trim() !== '')) { // Skip completely empty rows
                        previewData.push(rowData);
                    }
                }

                if (previewData.length === 0) {
                    previewInfo.textContent = 'No data rows found (only headers)';
                } else {
                    previewRows.innerHTML = previewData.map(row => 
                        `<tr>${row.map(cell => `<td class="text-nowrap">${escapeHtml(cell || '')}</td>`).join('')}</tr>`
                    ).join('');

                    const totalRows = lines.length - 1;
                    previewInfo.textContent = `Preview showing ${previewData.length} of ${totalRows} total data rows`;
                }

                filePreview.classList.remove('d-none');

            } catch (error) {
                console.error('Error parsing file:', error);
                previewInfo.textContent = 'Error parsing file: ' + error.message;
                filePreview.classList.remove('d-none');
            }
        };

        reader.onerror = function() {
            previewInfo.textContent = 'Error reading file';
            filePreview.classList.remove('d-none');
        };

        reader.readAsText(file);
    });

    // Form submission handling
    importForm.addEventListener('submit', function(e) {
        const file = fileInput.files[0];
        if (!file) {
            e.preventDefault();
            alert('Please select a file to import.');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Importing...';
        
        // Form will submit normally
    });

    function parseCSVLine(line) {
        const result = [];
        let current = '';
        let inQuotes = false;
        
        for (let i = 0; i < line.length; i++) {
            const char = line[i];
            const nextChar = line[i + 1];
            
            if (char === '"') {
                if (inQuotes && nextChar === '"') {
                    current += '"';
                    i++;
                } else {
                    inQuotes = !inQuotes;
                }
            } else if (char === ',' && !inQuotes) {
                result.push(current.trim());
                current = '';
            } else {
                current += char;
            }
        }
        
        result.push(current.trim());
        return result;
    }

    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return unsafe.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
</script>
@endsection