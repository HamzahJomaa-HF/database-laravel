Here's the Action Plan index blade based on your Activities index, but adapted for Action Plans:

```blade
@extends('layouts.app')

@section('title', 'Action Plans Management')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                <div>
                    <h1 class="h2 fw-bold mb-1">Action Plans Management</h1>
                    <p class="text-muted mb-0">Manage and organize your imported action plans</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reporting.import.import') }}" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>Import New Action Plan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Actions Section --}}
    <div class="row mb-4" id="bulkActionsSection" style="display: none;">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mb-0 fw-semibold">
                                <i class="bi bi-check-all me-2"></i>Bulk Actions
                            </h5>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-white text-dark me-3" id="selectedCount">0 selected</span>
                            <button type="button" class="btn btn-outline-secondary" id="clearSelectionBtn">
                                <i class="bi bi-x-circle me-1"></i>Clear Selection
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-3">
                    <form method="POST" 
                          action="{{ route('action-plans.bulk.destroy') }}" 
                          id="bulkDeleteForm" 
                          class="d-inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="action_plan_ids" id="selectedActionPlanIds">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Delete Selected
                        </button>
                    </form>
                    <small class="text-muted ms-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        This will delete action plans and their Excel files permanently
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom py-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse" style="cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mb-0 fw-semibold">
                                <i class="bi bi-funnel me-2 text-primary"></i>Search & Filter Action Plans
                            </h5>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-chevron-down transition-rotate" id="filterChevron"></i>
                        </div>
                    </div>
                </div>
                
                <div class="collapse" id="filterCollapse">
                    <div class="card-body p-4">
                        <form method="GET">
                            <div class="row g-3">
                                {{-- Title Search --}}
                                <div class="col-md-4">
                                    <label for="inlineFormTitle" class="form-label fw-semibold">Title Search</label>
                                    <input type="text" 
                                           name="title" 
                                           value="{{ request('title') }}"
                                           class="form-control" 
                                           id="inlineFormTitle"
                                           placeholder="Search by action plan title">
                                </div>

                                {{-- External ID --}}
                                <div class="col-md-4">
                                    <label for="inlineFormExternalId" class="form-label fw-semibold">External ID</label>
                                    <input type="text" 
                                           name="external_id" 
                                           value="{{ request('external_id') }}"
                                           class="form-control" 
                                           id="inlineFormExternalId"
                                           placeholder="e.g., AP_2026_01_001">
                                </div>

                                {{-- Date Range --}}
                                <div class="col-md-4">
                                    <label for="inlineFormStartDate" class="form-label fw-semibold">Imported From</label>
                                    <input type="date" 
                                           name="imported_from" 
                                           value="{{ request('imported_from') }}"
                                           class="form-control" 
                                           id="inlineFormStartDate">
                                </div>

                                <div class="col-md-4">
                                    <label for="inlineFormEndDate" class="form-label fw-semibold">Imported To</label>
                                    <input type="date" 
                                           name="imported_to" 
                                           value="{{ request('imported_to') }}"
                                           class="form-control" 
                                           id="inlineFormEndDate">
                                </div>

                                {{-- Start Date --}}
                                <div class="col-md-4">
                                    <label for="inlineFormPlanStartDate" class="form-label fw-semibold">Plan Start Date</label>
                                    <input type="date" 
                                           name="plan_start_date" 
                                           value="{{ request('plan_start_date') }}"
                                           class="form-control" 
                                           id="inlineFormPlanStartDate">
                                </div>

                                {{-- End Date --}}
                                <div class="col-md-4">
                                    <label for="inlineFormPlanEndDate" class="form-label fw-semibold">Plan End Date</label>
                                    <input type="date" 
                                           name="plan_end_date" 
                                           value="{{ request('plan_end_date') }}"
                                           class="form-control" 
                                           id="inlineFormPlanEndDate">
                                </div>

                                {{-- Action Buttons --}}
                                <div class="col-12 mt-3">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-funnel me-1"></i>Apply Filters
                                        </button>
                                        <a href="{{ route('action-plans.index') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-clockwise me-1"></i>Reset Filters
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Plans Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mb-0 fw-semibold">Action Plans Directory</h5>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="text-muted small">Total: {{ $actionPlans->total() }} action plans</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                                        </div>
                                    </th>
                                    <th>Action Plan Information</th>
                                    <th style="width: 200px;">Dates & Import Info</th>
                                    <th style="width: 150px;">Excel File</th>
                                    <th style="width: 150px;">Import Statistics</th>
                                    <th style="width: 100px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($actionPlans as $actionPlan)
                                <tr class="action-plan-row align-middle">
                                    {{-- Checkbox for selection --}}
                                    <td class="text-center">
                                        <div class="form-check">
                                            <input class="form-check-input action-plan-checkbox" 
                                                   type="checkbox" 
                                                   value="{{ $actionPlan->action_plan_id }}"
                                                   id="action_plan_{{ $actionPlan->action_plan_id }}">
                                        </div>
                                    </td>

                                    {{-- Action Plan Information --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <span class="text-primary fw-bold small">
                                                    <i class="bi bi-file-earmark-spreadsheet"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark mb-1">
                                                    {{ $actionPlan->title ?? 'Untitled Action Plan' }}
                                                </div>
                                                <div class="small text-muted">
                                                    @if($actionPlan->external_id)
                                                    <span class="me-2">ID: {{ $actionPlan->external_id }}</span>
                                                    @endif
                                                    @if($actionPlan->component && $actionPlan->component->code)
                                                    <span class="badge bg-info bg-opacity-10 text-info">
                                                        {{ $actionPlan->component->code }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Dates & Import Info --}}
                                    <td>
                                        <div class="small">
                                            @if($actionPlan->start_date)
                                            <div class="mb-1">
                                                <span class="text-muted">Plan Start:</span> 
                                                {{ \Carbon\Carbon::parse($actionPlan->start_date)->format('M d, Y') }}
                                            </div>
                                            @endif
                                            @if($actionPlan->end_date)
                                            <div class="mb-1">
                                                <span class="text-muted">Plan End:</span> 
                                                {{ \Carbon\Carbon::parse($actionPlan->end_date)->format('M d, Y') }}
                                            </div>
                                            @endif
                                            @if($actionPlan->excel_uploaded_at)
                                            <div class="mb-1">
                                                <span class="text-muted">Imported:</span> 
                                                {{ \Carbon\Carbon::parse($actionPlan->excel_uploaded_at)->format('M d, Y') }}
                                            </div>
                                            @endif
                                            @if($actionPlan->start_date && $actionPlan->end_date)
                                            <div>
                                                <span class="text-muted">Duration:</span> 
                                                {{ \Carbon\Carbon::parse($actionPlan->start_date)->diffInDays($actionPlan->end_date) + 1 }} days
                                            </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Excel File --}}
<td>
    @if($actionPlan->excel_filename)
    <div class="d-flex align-items-center mb-1">
        <i class="bi bi-file-earmark-excel text-success me-2 small"></i>
        <div class="small">
            <div>{{ Str::limit($actionPlan->excel_filename, 25) }}</div>
            @if($actionPlan->excel_metadata && isset($actionPlan->excel_metadata['original_name']))
            <div class="text-muted">
                {{ Str::limit($actionPlan->excel_metadata['original_name'], 20) }}
            </div>
            @endif
        </div>
    </div>
    
    {{-- Download Button --}}
    <a href="{{ route('action-plans.download', $actionPlan->action_plan_id) }}" 
       class="btn btn-sm btn-outline-success"
       title="Download original Excel file">
        <i class="bi bi-download me-1"></i>Download
    </a>
    
    {{-- Debug info (optional - remove in production) --}}
    @if(!$actionPlan->excel_path)
    <span class="badge bg-warning ms-1" title="File path not set in database">⚠️</span>
    @endif
    @endif
</td>

                                 {{-- Import Statistics --}}
<td>
    @php
        // Decode the JSON metadata
        $metadata = json_decode($actionPlan->excel_metadata, true) ?? [];
        $importResults = $metadata['import_results'] ?? null;
    @endphp
    
    @if($importResults)
        @php
            $hierarchy = $importResults['hierarchy'] ?? [];
            $activities = $importResults['activities'] ?? [];
        @endphp
        <div class="small">
            @if(isset($hierarchy['processed']))
            <div class="mb-1">
                <span class="text-muted">Hierarchy:</span> 
                <span class="fw-semibold">{{ $hierarchy['processed'] }}</span> rows
            </div>
            @endif
            
            @if(isset($activities['processed']))
            <div class="mb-1">
                <span class="text-muted">Activities:</span> 
                <span class="fw-semibold">{{ $activities['processed'] }}</span> rows
            </div>
            @endif
            
            {{-- Show more details if available --}}
            @if(isset($hierarchy['details']))
            <div class="mt-1">
                @if(isset($hierarchy['details']['components']['new']))
                <div class="d-inline-block me-2">
                    <span class="badge bg-success" title="New components">
                        {{ $hierarchy['details']['components']['new'] }} C
                    </span>
                </div>
                @endif
                
                @if(isset($hierarchy['details']['actions']['new']))
                <div class="d-inline-block">
                    <span class="badge bg-info" title="New actions">
                        {{ $hierarchy['details']['actions']['new'] }} A
                    </span>
                </div>
                @endif
            </div>
            @endif
            
            @if(isset($activities['created']))
            <div class="mt-1">
                @if(isset($activities['created']['activities']))
                <div class="d-inline-block me-2">
                    <span class="badge bg-primary" title="Activities created">
                        {{ $activities['created']['activities'] }} AC
                    </span>
                </div>
                @endif
            </div>
            @endif
        </div>
    @else
        <span class="badge bg-secondary">No import data</span>
    @endif
</td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="text-center">
                                            <div class="btn-group" role="group">
                                               
                                                <form method="POST" 
                                                      action="{{ route('action-plans.destroy', $actionPlan->action_plan_id) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this action plan? This will also delete the Excel file.');">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                {{-- View Modal for each Action Plan --}}
                                <div class="modal fade" id="viewModal{{ $actionPlan->action_plan_id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                                                    Action Plan Details
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6 class="fw-semibold mb-3">Basic Information</h6>
                                                        <dl class="row">
                                                            <dt class="col-sm-4">Title:</dt>
                                                            <dd class="col-sm-8">{{ $actionPlan->title }}</dd>
                                                            
                                                            <dt class="col-sm-4">External ID:</dt>
                                                            <dd class="col-sm-8">
                                                                <span class="badge bg-primary">{{ $actionPlan->external_id }}</span>
                                                            </dd>
                                                            
                                                            @if($actionPlan->start_date)
                                                            <dt class="col-sm-4">Start Date:</dt>
                                                            <dd class="col-sm-8">{{ \Carbon\Carbon::parse($actionPlan->start_date)->format('F d, Y') }}</dd>
                                                            @endif
                                                            
                                                            @if($actionPlan->end_date)
                                                            <dt class="col-sm-4">End Date:</dt>
                                                            <dd class="col-sm-8">{{ \Carbon\Carbon::parse($actionPlan->end_date)->format('F d, Y') }}</dd>
                                                            @endif
                                                            
                                                            @if($actionPlan->excel_uploaded_at)
                                                            <dt class="col-sm-4">Imported On:</dt>
                                                            <dd class="col-sm-8">{{ \Carbon\Carbon::parse($actionPlan->excel_uploaded_at)->format('F d, Y H:i') }}</dd>
                                                            @endif
                                                            
                                                            @if($actionPlan->excel_processed_at)
                                                            <dt class="col-sm-4">Processed On:</dt>
                                                            <dd class="col-sm-8">{{ \Carbon\Carbon::parse($actionPlan->excel_processed_at)->format('F d, Y H:i') }}</dd>
                                                            @endif
                                                        </dl>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <h6 class="fw-semibold mb-3">File Information</h6>
                                                        <dl class="row">
                                                            <dt class="col-sm-4">Filename:</dt>
                                                            <dd class="col-sm-8">{{ $actionPlan->excel_filename }}</dd>
                                                            
                                                            @if($actionPlan->excel_metadata && isset($actionPlan->excel_metadata['original_name']))
                                                            <dt class="col-sm-4">Original Name:</dt>
                                                            <dd class="col-sm-8">{{ $actionPlan->excel_metadata['original_name'] }}</dd>
                                                            @endif
                                                            
                                                            @if($actionPlan->excel_metadata && isset($actionPlan->excel_metadata['size']))
                                                            <dt class="col-sm-4">File Size:</dt>
                                                            <dd class="col-sm-8">{{ number_format($actionPlan->excel_metadata['size'] / 1024, 2) }} KB</dd>
                                                            @endif
                                                            
                                                          @if($actionPlan->excel_filename)
<dt class="col-sm-4">Download:</dt>
<dd class="col-sm-8">
    <a href="{{ route('action-plans.download', $actionPlan->action_plan_id) }}" 
       class="btn btn-sm btn-success">
        <i class="bi bi-download me-1"></i>Download Excel
    </a>
</dd>
@endif
                                                        </dl>
                                                    </div>
                                                </div>
                                                
                                                @if($actionPlan->excel_metadata && isset($actionPlan->excel_metadata['import_results']))
                                                <hr>
                                                <h6 class="fw-semibold mb-3">Import Statistics</h6>
                                                <div class="row">
                                                    @php
                                                        $importResults = $actionPlan->excel_metadata['import_results'];
                                                        $hierarchy = $importResults['hierarchy'] ?? [];
                                                        $activities = $importResults['activities'] ?? [];
                                                    @endphp
                                                    
                                                    <div class="col-md-6">
                                                        <h6 class="fw-semibold mb-2 text-primary">Hierarchy Import</h6>
                                                        @if(isset($hierarchy['processed']))
                                                        <div class="mb-2">
                                                            <span class="text-muted">Total Rows:</span> 
                                                            <span class="fw-bold">{{ $hierarchy['processed'] }}</span>
                                                        </div>
                                                        @endif
                                                        @if(isset($hierarchy['details']))
                                                        <div class="row small">
                                                            <div class="col-6">
                                                                <span class="text-muted">Components:</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="badge bg-success">{{ $hierarchy['details']['components']['new'] ?? 0 }} new</span>
                                                                <span class="badge bg-secondary">{{ $hierarchy['details']['components']['existing'] ?? 0 }} existing</span>
                                                            </div>
                                                            
                                                            <div class="col-6">
                                                                <span class="text-muted">Programs:</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="badge bg-success">{{ $hierarchy['details']['programs']['new'] ?? 0 }} new</span>
                                                                <span class="badge bg-secondary">{{ $hierarchy['details']['programs']['existing'] ?? 0 }} existing</span>
                                                            </div>
                                                            
                                                            <div class="col-6">
                                                                <span class="text-muted">Units:</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="badge bg-success">{{ $hierarchy['details']['units']['new'] ?? 0 }} new</span>
                                                                <span class="badge bg-secondary">{{ $hierarchy['details']['units']['existing'] ?? 0 }} existing</span>
                                                            </div>
                                                            
                                                            <div class="col-6">
                                                                <span class="text-muted">Actions:</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="badge bg-success">{{ $hierarchy['details']['actions']['new'] ?? 0 }} new</span>
                                                                <span class="badge bg-secondary">{{ $hierarchy['details']['actions']['existing'] ?? 0 }} existing</span>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <h6 class="fw-semibold mb-2 text-success">Activities Import</h6>
                                                        @if(isset($activities['processed']))
                                                        <div class="mb-2">
                                                            <span class="text-muted">Total Rows:</span> 
                                                            <span class="fw-bold">{{ $activities['processed'] }}</span>
                                                        </div>
                                                        @endif
                                                        @if(isset($activities['created']))
                                                        <div class="row small">
                                                            <div class="col-6">
                                                                <span class="text-muted">Activities:</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="badge bg-success">{{ $activities['created']['activities'] ?? 0 }} created</span>
                                                            </div>
                                                            
                                                            <div class="col-6">
                                                                <span class="text-muted">Indicators:</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="badge bg-success">{{ $activities['created']['indicators'] ?? 0 }} created</span>
                                                            </div>
                                                            
                                                            <div class="col-6">
                                                                <span class="text-muted">Focal Points:</span>
                                                            </div>
                                                            <div class="col-6">
                                                                <span class="badge bg-success">{{ $activities['created']['focalpoints'] ?? 0 }} created</span>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center bg-light">
                                        <div class="py-4">
                                            <i class="bi bi-file-earmark-excel display-4 text-muted opacity-50 mb-3"></i>
                                            <h5 class="fw-bold text-muted mb-3">No action plans found</h5>
                                            @if($hasSearch)
                                                <p class="text-muted mb-3">Try adjusting your search criteria</p>
                                                <a href="{{ route('action-plans.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-center mx-auto" style="width: 200px;">
                                                    <i class="bi bi-arrow-clockwise me-2"></i>Clear All Filters
                                                </a>
                                            @else
                                                <p class="text-muted mb-3">Get started by importing your first action plan</p>
                                                <a href="{{ route('reporting.import.import') }}" class="btn btn-primary d-flex align-items-center justify-content-center mx-auto" style="width: 250px;">
                                                    <i class="bi bi-upload me-2"></i>Import First Action Plan
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($actionPlans->hasPages())
                    <div class="card-footer bg-white border-0 pt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing <strong>{{ $actionPlans->firstItem() ?? 0 }}</strong> to 
                                <strong>{{ $actionPlans->lastItem() ?? 0 }}</strong> of 
                                <strong>{{ $actionPlans->total() }}</strong> entries
                            </div>
                            <div>
                                {{ $actionPlans->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bulk Delete Confirmation Modal --}}
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Confirm Bulk Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <span id="deleteCount" class="fw-bold">0</span> selected action plan(s)?</p>
                <p class="text-danger small mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    This will permanently delete the Excel files and cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="bulkDeleteModalForm">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="action_plan_ids" id="modalSelectedActionPlanIds">
                    <button type="submit" class="btn btn-danger">Delete Selected</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- TOAST NOTIFICATIONS --}}
@if(session('success'))
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast bg-success text-white border-0 fade show" role="alert">
        <div class="d-flex align-items-center">
            <div class="toast-body d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>
                    <strong>Success</strong>
                    <div class="small">{{ session('success') }}</div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast bg-danger text-white border-0 fade show" role="alert">
        <div class="d-flex align-items-center">
            <div class="toast-body d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>
                    <strong>Error</strong>
                    <div class="small">{{ session('error') }}</div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif
@endsection

@section('styles')
<style>
    /* Keep all the same CSS styles from your activities index */
    .card {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        overflow: hidden;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        transition: background-color 0.2s ease;
    }
    
    .card-header[aria-expanded="true"] {
        background-color: #e9ecef;
    }
    
    .card-header:hover {
        background-color: #e9ecef;
    }
    
    .form-label {
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .form-control, .form-select {
        border-radius: 6px;
        border: 1px solid #ced4da;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0a58ca;
        box-shadow: 0 0 0 0.2rem rgba(10, 88, 202, 0.25);
    }
    
    .btn {
        border-radius: 6px;
        font-weight: 500;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    .btn-primary {
        background-color: #0a58ca;
        border-color: #0a58ca;
    }
    
    .btn-primary:hover {
        background-color: #0a58ca;
        border-color: #0a58ca;
    }
    
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }
    
    .btn-outline-primary {
        color: #0a58ca;
        border-color: #0a58ca;
    }
    
    .btn-outline-primary:hover {
        background-color: #0a58ca;
        border-color: #0a58ca;
        color: white;
    }
    
    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }
    
    .btn-outline-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    
    .btn-outline-info {
        color: #0dcaf0;
        border-color: #0dcaf0;
    }
    
    .btn-outline-info:hover {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
        color: white;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        margin: 1px;
    }
    
    .btn-group .btn {
        border-radius: 4px;
        margin: 0 1px;
    }
    
    .btn-group .btn:first-child {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    
    .btn-group .btn:last-child {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    
    .table th {
        background: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }

    .table td {
        padding: 1rem 0.75rem;
        border-color: #f8f9fa;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.65em;
        font-weight: 500;
        border-radius: 6px;
    }

    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .toast {
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .transition-rotate {
        transition: transform 0.3s ease;
    }

    .bi-chevron-down.rotated {
        transform: rotate(180deg);
    }

    .collapse:not(.show) {
        display: none;
    }

    .collapsing {
        height: 0;
        overflow: hidden;
        transition: height 0.35s ease;
    }
    
    /* Checkbox styling */
    .form-check-input {
        width: 1.2em;
        height: 1.2em;
        cursor: pointer;
    }
    
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    /* Selected row styling */
    tr.selected {
        background-color: #e7f3ff !important;
    }
    
    tr.selected:hover {
        background-color: #d4e7ff !important;
    }
    
    /* Bulk actions section */
    #bulkActionsSection {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Filter collapse functionality
    const filterCollapse = document.getElementById('filterCollapse');
    const filterChevron = document.getElementById('filterChevron');
    
    if (filterCollapse && filterChevron) {
        filterCollapse.addEventListener('show.bs.collapse', function () {
            filterChevron.classList.add('rotated');
        });
        
        filterCollapse.addEventListener('hide.bs.collapse', function () {
            filterChevron.classList.remove('rotated');
        });
    }

    // Auto-hide toasts after delay
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toastEl => {
        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    });

    // Bulk Selection Functionality
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const actionPlanCheckboxes = document.querySelectorAll('.action-plan-checkbox');
    const bulkActionsSection = document.getElementById('bulkActionsSection');
    const selectedCount = document.getElementById('selectedCount');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    const selectedActionPlanIds = document.getElementById('selectedActionPlanIds');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    
    // Update selected count and show/hide bulk actions
    function updateSelection() {
        const selectedCheckboxes = Array.from(actionPlanCheckboxes).filter(cb => cb.checked);
        const count = selectedCheckboxes.length;
        
          // Update selected count
    selectedCount.textContent = `${count} selected`;
    
    // Update hidden input value with comma-separated IDs
    const selectedIds = selectedCheckboxes.map(cb => cb.value).join(',');
    selectedActionPlanIds.value = selectedIds;
    
    // Show/hide bulk actions section
    if (count > 0) {
        bulkActionsSection.style.display = 'block';
        
        // Add selected class to rows
        selectedCheckboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (row) row.classList.add('selected');
        });
        
        // Remove selected class from unselected rows
        Array.from(actionPlanCheckboxes)
            .filter(cb => !cb.checked)
            .forEach(cb => {
                const row = cb.closest('tr');
                if (row) row.classList.remove('selected');
            });
    } else {
        bulkActionsSection.style.display = 'none';
        actionPlanCheckboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (row) row.classList.remove('selected');
        });
    }
    
    // Update select all checkbox state
    if (selectAllCheckbox) {
        if (count === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (count === actionPlanCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }
}

// Select All checkbox functionality
if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        actionPlanCheckboxes.forEach(cb => {
            cb.checked = isChecked;
        });
        updateSelection();
    });
}

// Individual checkbox functionality
actionPlanCheckboxes.forEach(cb => {
    cb.addEventListener('change', updateSelection);
});

// Clear selection button
if (clearSelectionBtn) {
    clearSelectionBtn.addEventListener('click', function() {
        actionPlanCheckboxes.forEach(cb => {
            cb.checked = false;
        });
        updateSelection();
    });
}

// Bulk delete form handling
if (bulkDeleteForm) {
    bulkDeleteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const selectedIds = selectedActionPlanIds.value;
        if (!selectedIds) {
            alert('Please select at least one action plan to delete.');
            return;
        }
        
        const count = selectedIds.split(',').length;
        const deleteCount = document.getElementById('deleteCount');
        const modalSelectedActionPlanIds = document.getElementById('modalSelectedActionPlanIds');
        const bulkDeleteModalForm = document.getElementById('bulkDeleteModalForm');
        
        if (deleteCount) deleteCount.textContent = count;
        if (modalSelectedActionPlanIds) modalSelectedActionPlanIds.value = selectedIds;
        
        // Update the modal form action to match original form
        if (bulkDeleteModalForm) {
            bulkDeleteModalForm.action = this.action;
        }
        
        // Show confirmation modal
        const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
        modal.show();
    });
}

// Bulk delete modal form submission
const bulkDeleteModalForm = document.getElementById('bulkDeleteModalForm');
if (bulkDeleteModalForm) {
    bulkDeleteModalForm.addEventListener('submit', function() {
        // The form will submit normally after confirmation
    });
}

// View modal data handling (for future enhancements)
const viewModals = document.querySelectorAll('[id^="viewModal"]');
viewModals.forEach(modalEl => {
    modalEl.addEventListener('show.bs.modal', function(event) {
        // You can add AJAX loading here if needed
        console.log('Showing modal for action plan');
    });
});

// Filter date range validation
const startDateInput = document.getElementById('inlineFormStartDate');
const endDateInput = document.getElementById('inlineFormEndDate');

if (startDateInput && endDateInput) {
    startDateInput.addEventListener('change', function() {
        if (this.value && endDateInput.value && this.value > endDateInput.value) {
            endDateInput.value = this.value;
        }
    });
    
    endDateInput.addEventListener('change', function() {
        if (this.value && startDateInput.value && this.value < startDateInput.value) {
            startDateInput.value = this.value;
        }
    });
}

// Plan date range validation
const planStartDateInput = document.getElementById('inlineFormPlanStartDate');
const planEndDateInput = document.getElementById('inlineFormPlanEndDate');

if (planStartDateInput && planEndDateInput) {
    planStartDateInput.addEventListener('change', function() {
        if (this.value && planEndDateInput.value && this.value > planEndDateInput.value) {
            planEndDateInput.value = this.value;
        }
    });
    
    planEndDateInput.addEventListener('change', function() {
        if (this.value && planStartDateInput.value && this.value < planStartDateInput.value) {
            planStartDateInput.value = this.value;
        }
    });
}

// Initialize selection state on page load
updateSelection();

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+A to select all checkboxes
    if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
        e.preventDefault();
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = !selectAllCheckbox.checked;
            selectAllCheckbox.dispatchEvent(new Event('change'));
        }
    }
    
    // Escape to clear selection
    if (e.key === 'Escape') {
        if (clearSelectionBtn) {
            clearSelectionBtn.click();
        }
    }
});

// Responsive table adjustments
function adjustTableLayout() {
    const table = document.querySelector('.table-responsive');
    if (table && window.innerWidth < 768) {
        table.classList.add('table-sm');
    } else if (table) {
        table.classList.remove('table-sm');
    }
}

// Initial adjustment
adjustTableLayout();

// Adjust on resize
window.addEventListener('resize', adjustTableLayout);

// Export functionality (for future enhancement)
function exportSelectedActionPlans() {
    const selectedIds = selectedActionPlanIds.value;
    if (!selectedIds) {
        alert('Please select at least one action plan to export.');
        return;
    }
    
    // This would be implemented with an export endpoint
    console.log('Exporting action plans:', selectedIds);
    // window.location.href = `/action-plans/export?ids=${selectedIds}`;
}

// Add export button functionality if needed
// document.getElementById('exportBtn').addEventListener('click', exportSelectedActionPlans);
});
</script>
@endsection