@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                <div>
                    <h1 class="h2 fw-bold mb-1">User Management</h1>
                    <p class="text-muted mb-0">Manage and organize your user directory efficiently</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i>Add User
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
                            <span class="badge bg-white text-dark" id="selectedCount">0 selected</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-danger" id="deleteSelectedBtn">
                            <i class="bi bi-trash me-1"></i>Delete Selected
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="clearSelectionBtn">
                            <i class="bi bi-x-circle me-1"></i>Clear Selection
                        </button>
                    </div>
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
                                <i class="bi bi-funnel me-2 text-primary"></i>Search & Filter Users
                            </h5>
                        </div>
                        <div class="flex-shrink-0">
                            @if($hasSearch)
                                <span class="badge bg-warning text-dark">Filters Active</span>
                            @endif
                            <i class="bi bi-chevron-down ms-2 transition-rotate" id="filterChevron"></i>
                        </div>
                    </div>
                </div>
                
                <div class="collapse" id="filterCollapse">
                    <div class="card-body p-4">
                        <form method="GET">
                            <div class="row g-3">
                                {{-- Name --}}
                                <div class="col-md-3">
                                    <label for="inlineFormFilterBy" class="form-label fw-semibold">Name Search</label>
                                    <input type="text" 
                                           name="name" 
                                           value="{{ request('name') }}"
                                           class="form-control" 
                                           id="inlineFormFilterBy"
                                           placeholder="Type a name">
                                </div>

                                {{-- Phone --}}
                                <div class="col-md-3">
                                    <label for="inlineFormPhone" class="form-label fw-semibold">Phone Number</label>
                                    <input type="text" 
                                           name="phone_number" 
                                           value="{{ request('phone_number') }}"
                                           class="form-control" 
                                           id="inlineFormPhone" 
                                           placeholder="Phone number">
                                </div>

                                {{-- Dropdowns --}}
                                <div class="col-md-3">
                                    <label for="inlineFormRole" class="form-label fw-semibold">Role Type</label>
                                    <select id="inlineFormRole" name="type" class="form-control">
                                        <option value="">All Roles</option>
                                        <option value="Beneficiary" {{ request('type') == 'Beneficiary' ? 'selected' : '' }}>Beneficiary</option>
                                        <option value="Stakeholder" {{ request('type') == 'Stakeholder' ? 'selected' : '' }}>Stakeholder</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="inlineFormEmployment" class="form-label fw-semibold">Status</label>
                                    <select id="inlineFormEmployment" name="employment_status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="Employed" {{ request('employment_status') == 'Employed' ? 'selected' : '' }}>Employed</option>
                                        <option value="Unemployed" {{ request('employment_status') == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                                        <option value="Student" {{ request('employment_status') == 'Student' ? 'selected' : '' }}>Student</option>
                                        <option value="Retired" {{ request('employment_status') == 'Retired' ? 'selected' : '' }}>Retired</option>
                                    </select>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="col-12 mt-3">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-funnel me-1"></i>Apply Filters
                                        </button>
                                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-clockwise me-1"></i>Reset Filters
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        {{-- ACTIVE FILTERS --}}
                        @if($hasSearch)
                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="fw-semibold text-muted small">Active Filters:</span>
                                <div class="d-flex flex-wrap gap-2">
                                    @php
                                    $filters = [
                                        'name' => ['icon' => 'bi-person', 'label' => 'Name'],
                                        'type' => ['icon' => 'bi-person-badge', 'label' => 'Role'],
                                        'phone_number' => ['icon' => 'bi-telephone', 'label' => 'Phone'],
                                        'employment_status' => ['icon' => 'bi-briefcase', 'label' => 'Status']
                                    ];
                                    @endphp

                                    @foreach($filters as $key => $meta)
                                        @if(request($key))
                                            <span class="badge bg-primary p-2 d-flex align-items-center">
                                                <i class="bi {{ $meta['icon'] }} me-1"></i>
                                                {{ $meta['label'] }}: <strong class="ms-1">{{ request($key) }}</strong>
                                                <a href="{{ request()->fullUrlWithQuery([$key => null]) }}" class="text-white ms-2">
                                                    <i class="bi bi-x"></i>
                                                </a>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mb-0 fw-semibold">User Directory</h5>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="text-muted small">Total: {{ $users->total() }} users</span>
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
                                    <th>User Information</th>
                                    <th style="width: 200px;">Contact Details</th>
                                    <th style="width: 150px;">Personal Info</th>
                                    <th style="width: 150px;">Status & Role</th>
                                    <th style="width: 100px;" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr class="user-row align-middle">
                                    {{-- Checkbox for selection --}}
                                    <td class="text-center">
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" 
                                                   type="checkbox" 
                                                   value="{{ $user->user_id }}"
                                                   id="user_{{ $user->user_id }}">
                                        </div>
                                    </td>

                                    {{-- User Information --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <span class="text-primary fw-bold small">
                                                    {{ strtoupper($user->first_name[0] ?? '') }}{{ strtoupper($user->last_name[0] ?? '') }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark mb-1">
                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                </div>
                                                @if($user->middle_name)
                                                    <div class="small text-muted">{{ $user->middle_name }}</div>
                                                @endif
                                                <div class="small text-muted">ID: {{ $user->identification_id ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Contact Details --}}
                                    <td>
                                        @if($user->phone_number)
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="bi bi-telephone text-muted me-2 small"></i>
                                            <span class="small">{{ $user->phone_number }}</span>
                                        </div>
                                        @endif
                                        @if($user->email)
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-envelope text-muted me-2 small"></i>
                                            <span class="small text-truncate">{{ $user->email }}</span>
                                        </div>
                                        @endif
                                    </td>

                                    {{-- Personal Information --}}
                                    <td>
                                        <div class="small">
                                            @if($user->dob)
                                            <div class="mb-1">
                                                <span class="text-muted">DOB:</span> {{ \Carbon\Carbon::parse($user->dob)->format('M d, Y') }}
                                            </div>
                                            @endif
                                            @if($user->gender)
                                            <div class="mb-1">
                                                <span class="text-muted">Gender:</span> {{ $user->gender }}
                                            </div>
                                            @endif
                                            @if($user->marital_status)
                                            <div>
                                                <span class="text-muted">Status:</span> {{ $user->marital_status }}
                                            </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Status & Role --}}
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge {{ $user->type == 'Beneficiary' ? 'bg-success' : 'bg-primary' }}">
                                                {{ $user->type ?? 'Stakeholder' }}
                                            </span>
                                            <span class="small text-muted">
                                                {{ $user->employment_status ?? 'Not specified' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="text-center">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('users.edit', $user->user_id) }}" 
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" 
                                                      action="{{ route('users.destroy', $user->user_id) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete {{ $user->first_name }} {{ $user->last_name }}?');">
                                                    @csrf @method('DELETE')
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
                                            <i class="bi bi-people display-4 text-muted opacity-50 mb-3"></i>
                                            <h5 class="fw-bold text-muted mb-3">No users found</h5>
                                            @if($hasSearch)
                                                <p class="text-muted mb-3">Try adjusting your search criteria</p>
                                                <a href="{{ route('users.index') }}" class="btn btn-outline-primary d-flex align-items-center justify-content-center mx-auto" style="width: 200px;">
                                                    <i class="bi bi-arrow-clockwise me-2"></i>Clear All Filters
                                                </a>
                                            @else
                                                <p class="text-muted mb-3">Get started by adding your first user</p>
                                                <a href="{{ route('users.create') }}" class="btn btn-primary d-flex align-items-center justify-content-center mx-auto" style="width: 200px;">
                                                    <i class="bi bi-person-plus me-2"></i>Add First User
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
                    @if($users->hasPages())
                    <div class="card-footer bg-white border-0 pt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                Showing <strong>{{ $users->firstItem() ?? 0 }}</strong> to 
                                <strong>{{ $users->lastItem() ?? 0 }}</strong> of 
                                <strong>{{ $users->total() }}</strong> entries
                            </div>
                            <div>
                                {{ $users->links('pagination::bootstrap-5') }}
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
                <p>Are you sure you want to delete <span id="deleteCount" class="fw-bold">0</span> selected user(s)?</p>
                <p class="text-danger small mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('users.bulk-delete') }}" id="bulkDeleteForm">
                    @csrf
                    <input type="hidden" name="user_ids" id="selectedUserIds">
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
        
        // Auto-expand if there are active filters
        @if($hasSearch)
            const bsCollapse = new bootstrap.Collapse(filterCollapse, {
                toggle: false
            });
            bsCollapse.show();
        @endif
    }

    // Auto-hide toasts after delay
    const toasts = document.querySelectorAll('.toast');
    toasts.forEach(toastEl => {
        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    });

    // Bulk Selection Functionality
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkActionsSection = document.getElementById('bulkActionsSection');
    const selectedCount = document.getElementById('selectedCount');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const clearSelectionBtn = document.getElementById('clearSelectionBtn');
    const bulkDeleteModal = document.getElementById('bulkDeleteModal');
    const deleteCount = document.getElementById('deleteCount');
    const selectedUserIds = document.getElementById('selectedUserIds');
    const bulkDeleteForm = document.getElementById('bulkDeleteForm');
    
    // Update selected count and show/hide bulk actions
    function updateSelection() {
        const selectedCheckboxes = Array.from(userCheckboxes).filter(cb => cb.checked);
        const count = selectedCheckboxes.length;
        
        // Update count display
        selectedCount.textContent = `${count} selected`;
        deleteCount.textContent = count;
        
        // Show/hide bulk actions section
        if (count > 0) {
            bulkActionsSection.style.display = 'block';
            
            // Update the hidden input with selected user IDs
            const selectedIds = selectedCheckboxes.map(cb => cb.value);
            selectedUserIds.value = JSON.stringify(selectedIds);
        } else {
            bulkActionsSection.style.display = 'none';
            selectedUserIds.value = '';
        }
        
        // Update select all checkbox state
        if (count === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (count === userCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
        
        // Add/remove selected class from rows
        userCheckboxes.forEach(cb => {
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
            userCheckboxes.forEach(cb => {
                cb.checked = isChecked;
                cb.dispatchEvent(new Event('change'));
            });
            updateSelection();
        });
    }
    
    // Individual checkbox handlers
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelection);
    });
    
    // Clear selection button
    if (clearSelectionBtn) {
        clearSelectionBtn.addEventListener('click', function() {
            userCheckboxes.forEach(cb => {
                cb.checked = false;
                cb.dispatchEvent(new Event('change'));
            });
            updateSelection();
        });
    }
    
    // Delete selected button
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            const count = Array.from(userCheckboxes).filter(cb => cb.checked).length;
            if (count > 0) {
                const modal = new bootstrap.Modal(bulkDeleteModal);
                modal.show();
            }
        });
    }
    
    // Initialize selection state
    updateSelection();
    
    // Handle bulk delete form submission
    if (bulkDeleteForm) {
        bulkDeleteForm.addEventListener('submit', function(e) {
            // The form will submit normally, no need for extra handling
            // Just show loading state if needed
            deleteSelectedBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Deleting...';
            deleteSelectedBtn.disabled = true;
        });
    }
});
</script>
@endsection