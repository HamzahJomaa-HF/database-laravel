@extends('layouts.app')

@section('title', 'Activities Management')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                <div>
                    <h1 class="h2 fw-bold mb-1">Activities Management</h1>
                    <p class="text-muted mb-0">Manage and organize your activities efficiently</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('activities.create') }}" class="btn btn-primary">
                        <i class="bi bi-calendar-plus me-1"></i>Add Activity
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
                          action="{{ route('activities.bulk.destroy') }}" 
                          id="bulkDeleteForm" 
                          class="d-inline">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="activity_ids" id="selectedActivityIds">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Delete Selected
                        </button>
                    </form>
                    <small class="text-muted ms-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        This action cannot be undone
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
                                <i class="bi bi-funnel me-2 text-primary"></i>Search & Filter Activities
                            </h5>
                        </div>
                        
                    </div>
                </div>
                
                <div class="collapse" id="filterCollapse">
                    <div class="card-body p-4">
                        <form method="GET">
                            <div class="row g-3">
                                {{-- Title Search --}}
                                <div class="col-md-3">
                                    <label for="inlineFormTitle" class="form-label fw-semibold">Title Search</label>
                                    <input type="text" 
                                           name="title" 
                                           value="{{ request('title') }}"
                                           class="form-control" 
                                           id="inlineFormTitle"
                                           placeholder="Search by title">
                                </div>

                                {{-- Activity Type --}}
                                <div class="col-md-3">
                                    <label for="inlineFormType" class="form-label fw-semibold">Activity Type</label>
                                    <select id="inlineFormType" name="activity_type" class="form-control">
                                        <option value="">All Types</option>
                                        <option value="Training" {{ request('activity_type') == 'Training' ? 'selected' : '' }}>Training</option>
                                        <option value="Workshop" {{ request('activity_type') == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                                        <option value="Conference" {{ request('activity_type') == 'Conference' ? 'selected' : '' }}>Conference</option>
                                        <option value="Seminar" {{ request('activity_type') == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                                        <option value="Meeting" {{ request('activity_type') == 'Meeting' ? 'selected' : '' }}>Meeting</option>
                                        <option value="Event" {{ request('activity_type') == 'Event' ? 'selected' : '' }}>Event</option>
                                    </select>
                                </div>

                                {{-- Date Range --}}
                                <div class="col-md-3">
                                    <label for="inlineFormStartDate" class="form-label fw-semibold">Start Date From</label>
                                    <input type="date" 
                                           name="start_date_from" 
                                           value="{{ request('start_date_from') }}"
                                           class="form-control" 
                                           id="inlineFormStartDate">
                                </div>

                                <div class="col-md-3">
                                    <label for="inlineFormEndDate" class="form-label fw-semibold">End Date To</label>
                                    <input type="date" 
                                           name="end_date_to" 
                                           value="{{ request('end_date_to') }}"
                                           class="form-control" 
                                           id="inlineFormEndDate">
                                </div>

                                {{-- Venue --}}
                                <div class="col-md-3">
                                    <label for="inlineFormVenue" class="form-label fw-semibold">Venue</label>
                                    <input type="text" 
                                           name="venue" 
                                           value="{{ request('venue') }}"
                                           class="form-control" 
                                           id="inlineFormVenue"
                                           placeholder="Search by venue">
                                </div>

                                {{-- Status --}}
                                <div class="col-md-3">
                                    <label for="inlineFormStatus" class="form-label fw-semibold">Status</label>
                                    <select id="inlineFormStatus" name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="col-12 mt-3">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-funnel me-1"></i>Apply Filters
                                        </button>
                                        <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary">
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

    {{-- Activities Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mb-0 fw-semibold">Activities Directory</h5>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="text-muted small">Total: {{ $activities->total() }} activities</span>
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
                                    <th>Activity Information</th>
                                    <th style="width: 200px;">Dates & Duration</th>
                                    <th style="width: 150px;">Location</th>
                                    <th style="width: 150px;">Type & Status</th>
                                    <th style="width: 100px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                <tr class="activity-row align-middle">
                                    {{-- Checkbox for selection --}}
                                    <td class="text-center">
                                        <div class="form-check">
                                            <input class="form-check-input activity-checkbox" 
                                                   type="checkbox" 
                                                   value="{{ $activity->activity_id }}"
                                                   id="activity_{{ $activity->activity_id }}">
                                        </div>
                                    </td>

                                    {{-- Activity Information --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <span class="text-primary fw-bold small">
                                                    {{ strtoupper(substr($activity->activity_type ?? 'A', 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark mb-1">
                                                    {{ $activity->activity_title_en }}
                                                </div>
                                                @if($activity->activity_title_ar)
                                                    <div class="small text-muted">{{ $activity->activity_title_ar }}</div>
                                                @endif
                                                <div class="small text-muted">
                                                    ID: {{ $activity->folder_name ?? $activity->external_id ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Dates & Duration --}}
                                    <td>
                                        <div class="small">
                                            @if($activity->start_date)
                                            <div class="mb-1">
                                                <span class="text-muted">Start:</span> 
                                                {{ \Carbon\Carbon::parse($activity->start_date)->format('M d, Y') }}
                                            </div>
                                            @endif
                                            @if($activity->end_date)
                                            <div class="mb-1">
                                                <span class="text-muted">End:</span> 
                                                {{ \Carbon\Carbon::parse($activity->end_date)->format('M d, Y') }}
                                            </div>
                                            @endif
                                            @if($activity->start_date && $activity->end_date)
                                            <div>
                                                <span class="text-muted">Duration:</span> 
                                                {{ \Carbon\Carbon::parse($activity->start_date)->diffInDays($activity->end_date) + 1 }} days
                                            </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Location --}}
                                    <td>
                                        @if($activity->venue)
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bi bi-geo-alt text-muted me-2 small"></i>
                                            <span class="small">{{ $activity->venue }}</span>
                                        </div>
                                        @endif
                                        @if($activity->city || $activity->country)
                                        <div class="small">
                                            @if($activity->city)
                                            <span class="text-muted">City:</span> {{ $activity->city }}
                                            @endif
                                            @if($activity->country)
                                            <span class="text-muted ms-2">Country:</span> {{ $activity->country }}
                                            @endif
                                        </div>
                                        @endif
                                    </td>

                                    {{-- Type & Status --}}
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                {{ $activity->activity_type ?? 'Not specified' }}
                                            </span>
                                            @php
                                                $statusColor = 'secondary';
                                                if ($activity->end_date && now()->gt($activity->end_date)) {
                                                    $status = 'completed';
                                                    $statusColor = 'success';
                                                } elseif ($activity->start_date && now()->lt($activity->start_date)) {
                                                    $status = 'upcoming';
                                                    $statusColor = 'warning';
                                                } elseif ($activity->start_date && $activity->end_date && 
                                                         now()->gte($activity->start_date) && now()->lte($activity->end_date)) {
                                                    $status = 'ongoing';
                                                    $statusColor = 'primary';
                                                } else {
                                                    $status = 'unknown';
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('activities.edit', $activity->activity_id) }}" 
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                               
                                                <form method="POST" 
                                                      action="{{ route('activities.destroy', $activity->activity_id) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete {{ $activity->activity_title_en }}?');">
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
                                @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center bg-light">
                                        <div class="py-4">
                                            <i class="bi bi-calendar-x display-4 text-muted opacity-50 mb-3"></i>
                                            <h5 class="fw-bold text-muted mb-3">No activities found</h5>
                                            @if($hasSearch)
                                                <p class="text-muted mb-3">Try adjusting your search criteria</p>
                                                <a href="{{ route('activities.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-center mx-auto" style="width: 200px;">
                                                    <i class="bi bi-arrow-clockwise me-2"></i>Clear All Filters
                                                </a>
                                            @else
                                                <p class="text-muted mb-3">Get started by adding your first activity</p>
                                                <a href="{{ route('activities.create') }}" class="btn btn-primary d-flex align-items-center justify-content-center mx-auto" style="width: 200px;">
                                                    <i class="bi bi-calendar-plus me-2"></i>Add First Activity
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
                    @if($activities->hasPages())
                    <div class="card-footer bg-white border-0 pt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing <strong>{{ $activities->firstItem() ?? 0 }}</strong> to 
                                <strong>{{ $activities->lastItem() ?? 0 }}</strong> of 
                                <strong>{{ $activities->total() }}</strong> entries
                            </div>
                            <div>
                                {{ $activities->links('pagination::bootstrap-5') }}
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
                <p>Are you sure you want to delete <span id="deleteCount" class="fw-bold">0</span> selected activity(ies)?</p>
                <p class="text-danger small mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="bulkDeleteModalForm">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="activity_ids" id="modalSelectedActivityIds">
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
    const activityCheckboxes = document.querySelectorAll('.activity-checkbox');
    const bulkActionsSection = document.getElementById('bulkActionsSection');
    const selectedCount = document.getElementById('selectedCount');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    const selectedActivityIds = document.getElementById('selectedActivityIds');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    
    // Update selected count and show/hide bulk actions
    function updateSelection() {
        const selectedCheckboxes = Array.from(activityCheckboxes).filter(cb => cb.checked);
        const count = selectedCheckboxes.length;
        
        // Update count display
        if (selectedCount) selectedCount.textContent = `${count} selected`;
        
        // Show/hide bulk actions section
        if (count > 0) {
            if (bulkActionsSection) bulkActionsSection.style.display = 'block';
            
            // Update the hidden input with selected activity IDs
            const selectedIds = selectedCheckboxes.map(cb => cb.value);
            if (selectedActivityIds) {
                selectedActivityIds.value = JSON.stringify(selectedIds);
            }
        } else {
            if (bulkActionsSection) bulkActionsSection.style.display = 'none';
            if (selectedActivityIds) {
                selectedActivityIds.value = '';
            }
        }
        
        // Update select all checkbox state
        if (count === 0) {
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
        } else if (count === activityCheckboxes.length) {
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            }
        } else {
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }
        
        // Add/remove selected class from rows
        activityCheckboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (cb.checked) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }
        });
    }
    
    // Select all checkbox handler
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            activityCheckboxes.forEach(cb => {
                cb.checked = isChecked;
            });
            updateSelection();
        });
    }
    
    // Individual checkbox handlers
    activityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelection);
    });
    
    // Clear selection button
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            activityCheckboxes.forEach(cb => {
                cb.checked = false;
            });
            updateSelection();
        });
    }
    
    // Bulk delete form submission
    if (bulkDeleteForm) {
        bulkDeleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const selectedCheckboxes = Array.from(activityCheckboxes).filter(cb => cb.checked);
            const count = selectedCheckboxes.length;
            
            if (count === 0) {
                alert('No activities selected.');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${count} selected activity(ies)? This action cannot be undone.`)) {
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Deleting...';
                    submitBtn.disabled = true;
                    
                    // Submit the form
                    this.submit();
                }
            }
        });
    }
    
    // Initialize selection state
    updateSelection();
});
</script>
@endsection