{{-- resources/views/activity-users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Activity Users Management')

@push('styles')
<style>
    .attendance-toggle.btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    .attendance-toggle.btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    .attendance-toggle {
        min-width: 90px;
        transition: all 0.3s ease;
    }
    .attendance-toggle i {
        margin-right: 5px;
    }
    .selected-row {
        background-color: #f0f8ff !important;
    }
    .badge-status {
        font-size: 0.9rem;
        padding: 5px 10px;
    }
    .filter-section {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .table th {
        background-color: #343a40;
        color: white;
    }
    .action-buttons .btn {
        margin: 0 2px;
    }
    
    /* Search Modal Styles */
    .search-modal-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    
    .search-result-item {
        cursor: pointer;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    
    .search-result-item:hover {
        background-color: #f0f7ff;
        border-left-color: #0d6efd;
    }
    
    .search-result-item.selected {
        background-color: #e7f1ff;
        border-left-color: #0d6efd;
    }
    
    .search-input-group {
        position: relative;
    }
    
    .search-input-group .clear-search {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
    }
    
    .search-input-group .clear-search:hover {
        color: #dc3545;
    }
    
    .search-stats {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 5px;
    }
    
    .selected-badge {
        background-color: #e7f1ff;
        border: 1px solid #0d6efd;
        color: #0d6efd;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .selected-badge .remove {
        cursor: pointer;
        font-size: 1.1rem;
        line-height: 1;
    }
    
    .selected-badge .remove:hover {
        color: #dc3545;
    }
    
    .infinite-scroll-trigger {
        height: 20px;
        width: 100%;
        margin-top: 10px;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .filter-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .filter-pill {
        background-color: #e9ecef;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .filter-pill .remove {
        cursor: pointer;
        font-weight: bold;
    }
    
    .filter-pill .remove:hover {
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Activity Users Management
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="fas fa-plus"></i> Add New
                        </button>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#bulkAssignmentModal">
                            <i class="fas fa-users"></i> Bulk Assign
                        </button>
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#bulkAttendanceModal">
                            <i class="fas fa-check-double"></i> Bulk Attendance
                        </button>
                        <a href="{{ route('activity-users.export') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-download"></i> Export
                        </a>
                        <a href="{{ route('activity-users.trash') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Trash
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    {{-- Active Filters Display --}}
                    <div class="filter-pills mb-3">
                        @if(request('activity_id'))
                            <span class="filter-pill">
                                Activity: {{ $activities->firstWhere('activity_id', request('activity_id'))?->activity_title_en ?? 'Selected' }}
                                <span class="remove" onclick="removeFilter('activity_id')">×</span>
                            </span>
                        @endif
                        @if(request('user_id'))
                            <span class="filter-pill">
                                User: {{ $users->firstWhere('user_id', request('user_id'))?->first_name ?? 'Selected' }}
                                <span class="remove" onclick="removeFilter('user_id')">×</span>
                            </span>
                        @endif
                        @if(request('type'))
                            <span class="filter-pill">
                                Type: {{ ucfirst(request('type')) }}
                                <span class="remove" onclick="removeFilter('type')">×</span>
                            </span>
                        @endif
                        @if(request('attended') !== null)
                            <span class="filter-pill">
                                Attended: {{ request('attended') == '1' ? 'Yes' : 'No' }}
                                <span class="remove" onclick="removeFilter('attended')">×</span>
                            </span>
                        @endif
                        @if(request('invited') !== null)
                            <span class="filter-pill">
                                Invited: {{ request('invited') == '1' ? 'Yes' : 'No' }}
                                <span class="remove" onclick="removeFilter('invited')">×</span>
                            </span>
                        @endif
                    </div>

                    {{-- Filter Form --}}
                    <div class="filter-section">
                        <form method="GET" action="{{ route('activity-users.index') }}" id="filter-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Activity</label>
                                    <div class="search-input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="activity-search-display" 
                                               placeholder="Search activities..."
                                               value="{{ $selectedActivity->activity_title_en ?? '' }}"
                                               readonly
                                               data-bs-toggle="modal" 
                                               data-bs-target="#activitySearchModal"
                                               style="cursor: pointer; background-color: #fff;">
                                        <input type="hidden" name="activity_id" id="selected-activity-id" value="{{ request('activity_id') }}">
                                        <i class="fas fa-search search-icon" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #6c757d; pointer-events: none;"></i>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="form-label">User</label>
                                    <div class="search-input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="user-search-display" 
                                               placeholder="Search users..."
                                               value="{{ $selectedUser->first_name ?? '' }} {{ $selectedUser->last_name ?? '' }}"
                                               readonly
                                               data-bs-toggle="modal" 
                                               data-bs-target="#userSearchModal"
                                               style="cursor: pointer; background-color: #fff;">
                                        <input type="hidden" name="user_id" id="selected-user-id" value="{{ request('user_id') }}">
                                        <i class="fas fa-search search-icon" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); color: #6c757d; pointer-events: none;"></i>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <label for="type" class="form-label">Type</label>
                                    <select name="type" id="type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="participant" {{ request('type') == 'participant' ? 'selected' : '' }}>Participant</option>
                                        <option value="trainer" {{ request('type') == 'trainer' ? 'selected' : '' }}>Trainer</option>
                                        <option value="organizer" {{ request('type') == 'organizer' ? 'selected' : '' }}>Organizer</option>
                                        <option value="speaker" {{ request('type') == 'speaker' ? 'selected' : '' }}>Speaker</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Search..." value="{{ request('search') }}">
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-2">
                                    <label for="is_lead" class="form-label">Is Lead</label>
                                    <select name="is_lead" id="is_lead" class="form-select">
                                        <option value="">All</option>
                                        <option value="1" {{ request('is_lead') === '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ request('is_lead') === '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label for="invited" class="form-label">Invited</label>
                                    <select name="invited" id="invited" class="form-select">
                                        <option value="">All</option>
                                        <option value="1" {{ request('invited') === '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ request('invited') === '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label for="attended" class="form-label">Attended</label>
                                    <select name="attended" id="attended" class="form-select">
                                        <option value="">All</option>
                                        <option value="1" {{ request('attended') === '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ request('attended') === '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-filter"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('activity-users.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-undo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="40">#</th>
                                    <th width="100">External ID</th>
                                    <th>User</th>
                                    <th>Activity</th>
                                    <th width="100">Type</th>
                                    <th width="80">Lead</th>
                                    <th width="80">Invited</th>
                                    <th width="120">Attendance</th>
                                    <th>COP</th>
                                    <th width="150">Created At</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activityUsers as $index => $activityUser)
                                    <tr class="{{ $activityUser->attended ? 'table-success' : '' }}" data-id="{{ $activityUser->activity_user_id }}">
                                        <td>{{ $activityUsers->firstItem() + $index }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $activityUser->external_id ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if($activityUser->user)
                                                <strong>{{ $activityUser->user->first_name }} {{ $activityUser->user->last_name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-envelope"></i> {{ $activityUser->user->email }}
                                                </small>
                                                <br>
                                                <small class="badge {{ $activityUser->user->type == 'beneficiary' ? 'bg-success' : 'bg-primary' }}">
                                                    {{ ucfirst($activityUser->user->type) }}
                                                </small>
                                            @else
                                                <span class="text-danger">User not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($activityUser->activity)
                                                <strong>{{ $activityUser->activity->activity_title_en }}</strong>
                                                @if($activityUser->activity->activity_title_ar)
                                                    <br>
                                                    <small class="text-muted">{{ $activityUser->activity->activity_title_ar }}</small>
                                                @endif
                                                <br>
                                                <small class="text-info">
                                                    <i class="fas fa-calendar"></i> 
                                                    {{ $activityUser->activity->start_date ? date('d M Y', strtotime($activityUser->activity->start_date)) : 'No date' }}
                                                </small>
                                            @else
                                                <span class="text-danger">Activity not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($activityUser->type)
                                                <span class="badge bg-secondary">{{ ucfirst($activityUser->type) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($activityUser->is_lead)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($activityUser->invited)
                                                <span class="badge bg-info">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" 
                                                    class="btn btn-sm attendance-toggle {{ $activityUser->attended ? 'btn-success' : 'btn-secondary' }}"
                                                    data-id="{{ $activityUser->activity_user_id }}"
                                                    data-attended="{{ $activityUser->attended ? '1' : '0' }}"
                                                    data-toggle="tooltip" 
                                                    title="{{ $activityUser->attended ? 'Click to mark as absent' : 'Click to mark as attended' }}">
                                                <i class="fas {{ $activityUser->attended ? 'fa-check-circle' : 'fa-circle' }}"></i>
                                                <span class="attendance-text">{{ $activityUser->attended ? 'Attended' : 'Absent' }}</span>
                                            </button>
                                        </td>
                                        <td>
                                            @if($activityUser->cop)
                                                <span class="badge bg-primary">{{ $activityUser->cop->cop_name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                {{ $activityUser->created_at ? $activityUser->created_at->format('d M Y') : '-' }}
                                            </small>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="{{ route('activity-users.edit', $activityUser->activity_user_id) }}" 
                                               class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Delete"
                                                    onclick="confirmDelete('{{ $activityUser->activity_user_id }}', '{{ $activityUser->user->first_name ?? '' }} {{ $activityUser->user->last_name ?? '' }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <a href="{{ route('activity-users.statistics', $activityUser->activity_id) }}" 
                                               class="btn btn-sm btn-info" title="Statistics">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5>No Activity-User Relationships Found</h5>
                                            <p class="text-muted">Get started by creating a new relationship or adjusting your filters.</p>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                                                <i class="fas fa-plus"></i> Add New Relationship
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $activityUsers->firstItem() ?? 0 }} to {{ $activityUsers->lastItem() ?? 0 }} 
                            of {{ $activityUsers->total() }} entries
                        </div>
                        <div>
                            {{ $activityUsers->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Activity Search Modal --}}
<div class="modal fade" id="activitySearchModal" tabindex="-1" aria-labelledby="activitySearchModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header search-modal-header">
                <h5 class="modal-title" id="activitySearchModalLabel">
                    <i class="fas fa-search"></i> Search Activities
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="search-input-group">
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="activity-search-input" 
                               placeholder="Type at least 3 characters to search..."
                               autocomplete="off"
                               autofocus>
                        <span class="clear-search" id="clear-activity-search" style="display: none;">×</span>
                    </div>
                    <div class="search-stats" id="activity-search-stats"></div>
                </div>
                
                <div id="activity-results-container" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center text-muted py-5" id="activity-initial-message">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <p>Start typing to search for activities...</p>
                        <small class="text-muted">Search by title, Arabic title, or external ID</small>
                    </div>
                    
                    <div class="text-center py-5 d-none" id="activity-loading">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <p>Searching activities...</p>
                    </div>
                    
                    <div class="list-group" id="activity-results"></div>
                    
                    <div class="infinite-scroll-trigger" id="activity-scroll-trigger"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-activity-selection" disabled>
                    <i class="fas fa-check"></i> Select Activity
                </button>
            </div>
        </div>
    </div>
</div>

{{-- User Search Modal --}}
<div class="modal fade" id="userSearchModal" tabindex="-1" aria-labelledby="userSearchModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header search-modal-header">
                <h5 class="modal-title" id="userSearchModalLabel">
                    <i class="fas fa-search"></i> Search Users
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <select class="form-select" id="user-type-filter">
                                <option value="">All User Types</option>
                                <option value="beneficiary">Beneficiary</option>
                                <option value="stakeholder">Stakeholder</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <div class="search-input-group">
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="user-search-input" 
                                       placeholder="Type at least 3 characters to search..."
                                       autocomplete="off">
                                <span class="clear-search" id="clear-user-search" style="display: none;">×</span>
                            </div>
                        </div>
                    </div>
                    <div class="search-stats" id="user-search-stats"></div>
                </div>
                
                <div id="user-results-container" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center text-muted py-5" id="user-initial-message">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <p>Start typing to search for users...</p>
                        <small class="text-muted">Search by name, email, phone, or ID</small>
                    </div>
                    
                    <div class="text-center py-5 d-none" id="user-loading">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <p>Searching users...</p>
                    </div>
                    
                    <div class="list-group" id="user-results"></div>
                    
                    <div class="infinite-scroll-trigger" id="user-scroll-trigger"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-user-selection" disabled>
                    <i class="fas fa-check"></i> Select User
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Create Assignment Modal --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">
                    <i class="fas fa-plus-circle"></i> Create New Assignment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('activity-users.store') }}" method="POST" id="create-form">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Activity <span class="text-danger">*</span></label>
                            <div class="search-input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="create-activity-display" 
                                       placeholder="Search and select activity"
                                       readonly
                                       data-bs-toggle="modal" 
                                       data-bs-target="#activitySearchModal"
                                       style="cursor: pointer; background-color: #fff;"
                                       required>
                                <input type="hidden" name="activity_id" id="create-activity-id" required>
                                <i class="fas fa-search search-icon"></i>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">User <span class="text-danger">*</span></label>
                            <div class="search-input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="create-user-display" 
                                       placeholder="Search and select user"
                                       readonly
                                       data-bs-toggle="modal" 
                                       data-bs-target="#userSearchModal"
                                       style="cursor: pointer; background-color: #fff;"
                                       required>
                                <input type="hidden" name="user_id" id="create-user-id" required>
                                <i class="fas fa-search search-icon"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type/Role</label>
                            <select name="type" class="form-select">
                                <option value="">Select Role</option>
                                <option value="participant">Participant</option>
                                <option value="trainer">Trainer</option>
                                <option value="organizer">Organizer</option>
                                <option value="speaker">Speaker</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">COP (Optional)</label>
                            <select name="cop_id" class="form-select">
                                <option value="">Select COP</option>
                                @foreach($cops ?? [] as $cop)
                                    <option value="{{ $cop->cop_id }}">{{ $cop->cop_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_lead" id="is_lead" value="1">
                                <label class="form-check-label" for="is_lead">
                                    Is Lead
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="invited" id="invited" value="1" checked>
                                <label class="form-check-label" for="invited">
                                    Invited
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="attended" id="attended" value="1">
                                <label class="form-check-label" for="attended">
                                    Attended
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Bulk Assignment Modal --}}
<div class="modal fade" id="bulkAssignmentModal" tabindex="-1" aria-labelledby="bulkAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkAssignmentModalLabel">
                    <i class="fas fa-users"></i> Bulk Assign Users to Activity
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('activity-users.bulk.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Activity <span class="text-danger">*</span></label>
                        <div class="search-input-group">
                            <input type="text" 
                                   class="form-control" 
                                   id="bulk-activity-display" 
                                   placeholder="Search and select activity"
                                   readonly
                                   data-bs-toggle="modal" 
                                   data-bs-target="#activitySearchModal"
                                   style="cursor: pointer; background-color: #fff;"
                                   required>
                            <input type="hidden" name="activity_id" id="bulk-activity-id" required>
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Select Users <span class="text-danger">*</span></label>
                        <div class="selected-badge-container mb-2" id="selected-users-container"></div>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userSearchModal" id="bulk-add-users">
                            <i class="fas fa-plus"></i> Add Users
                        </button>
                        <input type="hidden" name="user_ids" id="bulk-user-ids" value="">
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type/Role (for all)</label>
                            <select name="type" class="form-select">
                                <option value="">Select Role</option>
                                <option value="participant">Participant</option>
                                <option value="trainer">Trainer</option>
                                <option value="organizer">Organizer</option>
                                <option value="speaker">Speaker</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">COP (Optional)</label>
                            <select name="cop_id" class="form-select">
                                <option value="">Select COP</option>
                                @foreach($cops ?? [] as $cop)
                                    <option value="{{ $cop->cop_id }}">{{ $cop->cop_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_lead" id="bulk_is_lead" value="1">
                                <label class="form-check-label" for="bulk_is_lead">
                                    Mark as Lead
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="invited" id="bulk_invited" value="1" checked>
                                <label class="form-check-label" for="bulk_invited">
                                    Send Invitation
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="attended" id="bulk_attended" value="1">
                                <label class="form-check-label" for="bulk_attended">
                                    Mark as Attended
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-users"></i> Assign Users
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Bulk Attendance Modal --}}
<div class="modal fade" id="bulkAttendanceModal" tabindex="-1" aria-labelledby="bulkAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkAttendanceModalLabel">
                    <i class="fas fa-check-double"></i> Bulk Mark Attendance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('activity-users.update-attendance') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Filter by Activity</label>
                        <select class="form-select" id="modal-activity-filter">
                            <option value="">All Activities</option>
                            @foreach($activities as $activity)
                                <option value="{{ $activity->activity_id }}">
                                    {{ $activity->activity_title_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-success" id="mark-all-attended">
                                <i class="fas fa-check-circle"></i> Mark All Attended
                            </button>
                            <button type="button" class="btn btn-secondary" id="mark-all-absent">
                                <i class="fas fa-times-circle"></i> Mark All Absent
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="select-all-modal">
                                    </th>
                                    <th>User</th>
                                    <th>Activity</th>
                                    <th>Current Status</th>
                                    <th>Mark Attended</th>
                                </tr>
                            </thead>
                            <tbody id="modal-attendance-list">
                                @foreach($activityUsers as $activityUser)
                                    <tr class="attendance-row" data-activity-id="{{ $activityUser->activity_id }}">
                                        <td>
                                            <input type="checkbox" name="attendances[{{ $activityUser->activity_user_id }}]" 
                                                   value="1" class="row-selector">
                                        </td>
                                        <td>
                                            @if($activityUser->user)
                                                {{ $activityUser->user->first_name }} {{ $activityUser->user->last_name }}
                                                <br>
                                                <small>{{ $activityUser->user->email }}</small>
                                                <br>
                                                <small class="badge {{ $activityUser->user->type == 'beneficiary' ? 'bg-success' : 'bg-primary' }}">
                                                    {{ ucfirst($activityUser->user->type) }}
                                                </small>
                                            @else
                                                <span class="text-danger">User not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $activityUser->activity->activity_title_en ?? 'N/A' }}
                                        </td>
                                        <td>
                                            @if($activityUser->attended)
                                                <span class="badge bg-success">Attended</span>
                                            @else
                                                <span class="badge bg-secondary">Absent</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <input type="checkbox" 
                                                   class="attended-checkbox" 
                                                   data-row-id="{{ $activityUser->activity_user_id }}"
                                                   {{ $activityUser->attended ? 'checked' : '' }}>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this relationship for <span id="delete-user-name"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // ==================== STATE MANAGEMENT ====================
    let selectedActivity = {
        id: $('#selected-activity-id').val(),
        name: $('#activity-search-display').val()
    };
    
    let selectedUser = {
        id: $('#selected-user-id').val(),
        name: $('#user-search-display').val()
    };
    
    let createSelectedActivity = null;
    let createSelectedUser = null;
    
    let bulkSelectedUsers = [];
    
    // ==================== TOOLTIPS ====================
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // ==================== ATTENDANCE TOGGLE ====================
    $('.attendance-toggle').click(function() {
        let button = $(this);
        let activityUserId = button.data('id');
        let url = '/activity-users/' + activityUserId + '/toggle-attendance';
        
        button.prop('disabled', true);
        let originalHtml = button.html();
        button.html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    button.data('attended', response.attended ? '1' : '0');
                    
                    if (response.attended) {
                        button.removeClass('btn-secondary').addClass('btn-success');
                        button.html('<i class="fas fa-check-circle"></i> <span class="attendance-text">Attended</span>');
                        button.attr('title', 'Click to mark as absent');
                        button.closest('tr').addClass('table-success');
                    } else {
                        button.removeClass('btn-success').addClass('btn-secondary');
                        button.html('<i class="fas fa-circle"></i> <span class="attendance-text">Absent</span>');
                        button.attr('title', 'Click to mark as attended');
                        button.closest('tr').removeClass('table-success');
                    }
                    
                    showNotification('success', response.message);
                }
            },
            error: function() {
                showNotification('error', 'Failed to update attendance. Please try again.');
                button.html(originalHtml);
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });

    // ==================== ACTIVITY SEARCH ====================
    let activitySearchTimeout;
    let activityCurrentPage = 1;
    let activityHasMore = true;
    let activityLoading = false;
    let activitySearchTerm = '';
    
    $('#activity-search-input').on('input', function() {
        clearTimeout(activitySearchTimeout);
        let query = $(this).val().trim();
        activitySearchTerm = query;
        
        if (query.length === 0) {
            $('#clear-activity-search').hide();
            $('#activity-initial-message').removeClass('d-none');
            $('#activity-results').empty();
            $('#activity-search-stats').empty();
            $('#confirm-activity-selection').prop('disabled', true);
            return;
        }
        
        $('#clear-activity-search').show();
        
        if (query.length < 3) {
            $('#activity-initial-message').removeClass('d-none').html(`
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <p>Please type at least 3 characters</p>
                <small>You've typed ${query.length} character(s)</small>
            `);
            $('#activity-results').empty();
            $('#activity-search-stats').empty();
            return;
        }
        
        activitySearchTimeout = setTimeout(() => {
            activityCurrentPage = 1;
            activityHasMore = true;
            searchActivities(query, 1);
        }, 500);
    });
    
    function searchActivities(query, page = 1) {
        if (activityLoading || !activityHasMore) return;
        
        activityLoading = true;
        $('#activity-initial-message').addClass('d-none');
        $('#activity-loading').removeClass('d-none');
        
        
            success: function(response) {
                $('#activity-loading').addClass('d-none');
                
                if (page === 1) {
                    $('#activity-results').empty();
                }
                
                if (response.data && response.data.length > 0) {
                    response.data.forEach(activity => {
                        let activityItem = `
                            <a href="#" class="list-group-item list-group-item-action search-result-item" 
                               data-activity-id="${activity.activity_id}"
                               data-activity-name="${activity.activity_title_en}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${activity.activity_title_en}</strong>
                                        ${activity.activity_title_ar ? `<br><small>${activity.activity_title_ar}</small>` : ''}
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i> ${activity.start_date || 'No date'}
                                        </small>
                                    </div>
                                    <div>
                                        <span class="badge bg-info">${activity.external_id || 'No ID'}</span>
                                    </div>
                                </div>
                            </a>
                        `;
                        $('#activity-results').append(activityItem);
                    });
                    
                    activityHasMore = response.current_page < response.last_page;
                    activityCurrentPage = response.current_page;
                    
                    $('#activity-search-stats').text(`Found ${response.total} activities`);
                } else {
                    if (page === 1) {
                        $('#activity-results').html(`
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                <p>No activities found matching "${query}"</p>
                            </div>
                        `);
                    }
                    activityHasMore = false;
                }
            },
            error: function() {
                $('#activity-loading').addClass('d-none');
                $('#activity-results').html(`
                    <div class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Error searching activities. Please try again.</p>
                    </div>
                `);
            },
            complete: function() {
                activityLoading = false;
            }
        });
    }
    
    // Infinite scroll for activities
    $('#activity-results-container').on('scroll', function() {
        let container = $(this);
        if (container.scrollTop() + container.height() >= container[0].scrollHeight - 100) {
            if (activityHasMore && !activityLoading && activitySearchTerm.length >= 3) {
                searchActivities(activitySearchTerm, activityCurrentPage + 1);
            }
        }
    });
    
    // Activity selection
    $(document).on('click', '#activity-results .search-result-item', function(e) {
        e.preventDefault();
        
        $('#activity-results .search-result-item').removeClass('selected');
        $(this).addClass('selected');
        
        let activityId = $(this).data('activity-id');
        let activityName = $(this).data('activity-name');
        
        $('#confirm-activity-selection').data('id', activityId)
                                       .data('name', activityName)
                                       .prop('disabled', false);
    });
    
    $('#confirm-activity-selection').click(function() {
        let activityId = $(this).data('id');
        let activityName = $(this).data('name');
        
        if (!activityId) return;
        
        // Update filter inputs
        $('#activity-search-display').val(activityName);
        $('#selected-activity-id').val(activityId);
        
        // Update create modal if open
        if ($('#createModal').hasClass('show')) {
            $('#create-activity-display').val(activityName);
            $('#create-activity-id').val(activityId);
            createSelectedActivity = { id: activityId, name: activityName };
        }
        
        // Update bulk modal if open
        if ($('#bulkAssignmentModal').hasClass('show')) {
            $('#bulk-activity-display').val(activityName);
            $('#bulk-activity-id').val(activityId);
        }
        
        $('#activitySearchModal').modal('hide');
    });
    
    $('#clear-activity-search').click(function() {
        $('#activity-search-input').val('').trigger('input');
        $(this).hide();
    });
    
    // ==================== USER SEARCH ====================
    let userSearchTimeout;
    let userCurrentPage = 1;
    let userHasMore = true;
    let userLoading = false;
    let userSearchTerm = '';
    let userTypeFilter = '';
    
    $('#user-search-input, #user-type-filter').on('input change', function() {
        clearTimeout(userSearchTimeout);
        
        userSearchTerm = $('#user-search-input').val().trim();
        userTypeFilter = $('#user-type-filter').val();
        
        if (userSearchTerm.length === 0 && !userTypeFilter) {
            $('#clear-user-search').hide();
            $('#user-initial-message').removeClass('d-none');
            $('#user-results').empty();
            $('#user-search-stats').empty();
            $('#confirm-user-selection').prop('disabled', true);
            return;
        }
        
        $('#clear-user-search').show();
        
        if (userSearchTerm.length > 0 && userSearchTerm.length < 3) {
            $('#user-initial-message').removeClass('d-none').html(`
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <p>Please type at least 3 characters</p>
                <small>You've typed ${userSearchTerm.length} character(s)</small>
            `);
            $('#user-results').empty();
            $('#user-search-stats').empty();
            return;
        }
        
        userSearchTimeout = setTimeout(() => {
            userCurrentPage = 1;
            userHasMore = true;
            searchUsers(userSearchTerm, userTypeFilter, 1);
        }, 500);
    });
    
    function searchUsers(query, type, page = 1) {
        if (userLoading || !userHasMore) return;
        
        userLoading = true;
        $('#user-initial-message').addClass('d-none');
        $('#user-loading').removeClass('d-none');
        
      
            success: function(response) {
                $('#user-loading').addClass('d-none');
                
                if (page === 1) {
                    $('#user-results').empty();
                }
                
                if (response.data && response.data.length > 0) {
                    response.data.forEach(user => {
                        let userItem = `
                            <a href="#" class="list-group-item list-group-item-action search-result-item" 
                               data-user-id="${user.user_id}"
                               data-user-name="${user.first_name} ${user.last_name}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${user.first_name} ${user.last_name}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-envelope"></i> ${user.email || 'No email'}
                                        </small>
                                        <br>
                                        <span class="badge ${user.type == 'beneficiary' ? 'bg-success' : 'bg-primary'}">
                                            ${user.type || 'No type'}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="badge bg-info">${user.external_id || 'No ID'}</span>
                                    </div>
                                </div>
                            </a>
                        `;
                        $('#user-results').append(userItem);
                    });
                    
                    userHasMore = response.current_page < response.last_page;
                    userCurrentPage = response.current_page;
                    
                    $('#user-search-stats').text(`Found ${response.total} users`);
                } else {
                    if (page === 1) {
                        $('#user-results').html(`
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-exclamation-circle fa-2x mb-2"></i>
                                <p>No users found</p>
                            </div>
                        `);
                    }
                    userHasMore = false;
                }
            },
            error: function() {
                $('#user-loading').addClass('d-none');
                $('#user-results').html(`
                    <div class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p>Error searching users. Please try again.</p>
                    </div>
                `);
            },
            complete: function() {
                userLoading = false;
            }
        });
    }
    
    // Infinite scroll for users
    $('#user-results-container').on('scroll', function() {
        let container = $(this);
        if (container.scrollTop() + container.height() >= container[0].scrollHeight - 100) {
            if (userHasMore && !userLoading) {
                searchUsers(userSearchTerm, userTypeFilter, userCurrentPage + 1);
            }
        }
    });
    
    // User selection
    $(document).on('click', '#user-results .search-result-item', function(e) {
        e.preventDefault();
        
        $('#user-results .search-result-item').removeClass('selected');
        $(this).addClass('selected');
        
        let userId = $(this).data('user-id');
        let userName = $(this).data('user-name');
        
        $('#confirm-user-selection').data('id', userId)
                                   .data('name', userName)
                                   .prop('disabled', false);
    });
    
    $('#confirm-user-selection').click(function() {
        let userId = $(this).data('id');
        let userName = $(this).data('name');
        
        if (!userId) return;
        
        // Update filter inputs
        $('#user-search-display').val(userName);
        $('#selected-user-id').val(userId);
        
        // Update create modal if open
        if ($('#createModal').hasClass('show')) {
            $('#create-user-display').val(userName);
            $('#create-user-id').val(userId);
            createSelectedUser = { id: userId, name: userName };
        }
        
        // Handle bulk selection
        if ($('#bulkAssignmentModal').hasClass('show')) {
            if (!bulkSelectedUsers.some(u => u.id === userId)) {
                bulkSelectedUsers.push({ id: userId, name: userName });
                updateBulkSelectedUsers();
            }
        }
        
        $('#userSearchModal').modal('hide');
    });
    
    $('#clear-user-search').click(function() {
        $('#user-search-input').val('');
        $('#user-type-filter').val('');
        $(this).hide();
        $('#user-initial-message').removeClass('d-none');
        $('#user-results').empty();
        $('#user-search-stats').empty();
        $('#confirm-user-selection').prop('disabled', true);
    });
    
    // ==================== BULK SELECTION ====================
    function updateBulkSelectedUsers() {
        let container = $('#selected-users-container');
        container.empty();
        
        bulkSelectedUsers.forEach(user => {
            container.append(`
                <span class="selected-badge me-2 mb-2">
                    ${user.name}
                    <span class="remove" onclick="removeBulkUser('${user.id}')">×</span>
                </span>
            `);
        });
        
        $('#bulk-user-ids').val(bulkSelectedUsers.map(u => u.id).join(','));
    }
    
    window.removeBulkUser = function(userId) {
        bulkSelectedUsers = bulkSelectedUsers.filter(u => u.id !== userId);
        updateBulkSelectedUsers();
    };
    
    // ==================== MODAL RESET ====================
    $('#activitySearchModal').on('hidden.bs.modal', function() {
        $('#activity-search-input').val('');
        $('#clear-activity-search').hide();
        $('#activity-initial-message').removeClass('d-none');
        $('#activity-results').empty();
        $('#activity-search-stats').empty();
        $('#confirm-activity-selection').prop('disabled', true).removeData('id').removeData('name');
        activitySearchTerm = '';
    });
    
    $('#userSearchModal').on('hidden.bs.modal', function() {
        $('#user-search-input').val('');
        $('#user-type-filter').val('');
        $('#clear-user-search').hide();
        $('#user-initial-message').removeClass('d-none');
        $('#user-results').empty();
        $('#user-search-stats').empty();
        $('#confirm-user-selection').prop('disabled', true).removeData('id').removeData('name');
        userSearchTerm = '';
        userTypeFilter = '';
    });
    
    $('#createModal').on('hidden.bs.modal', function() {
        $('#create-form')[0].reset();
        $('#create-activity-display').val('');
        $('#create-activity-id').val('');
        $('#create-user-display').val('');
        $('#create-user-id').val('');
        createSelectedActivity = null;
        createSelectedUser = null;
    });
    
    $('#bulkAssignmentModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $('#bulk-activity-display').val('');
        $('#bulk-activity-id').val('');
        bulkSelectedUsers = [];
        updateBulkSelectedUsers();
    });
    
    // ==================== FILTER HANDLING ====================
    window.removeFilter = function(filterName) {
        let url = new URL(window.location.href);
        url.searchParams.delete(filterName);
        window.location.href = url.toString();
    };
    
    // ==================== BULK ATTENDANCE MODAL ====================
    $('#select-all-modal').change(function() {
        $('.row-selector:visible').prop('checked', $(this).prop('checked'));
    });

    $('#mark-all-attended').click(function() {
        $('.attended-checkbox:visible').prop('checked', true);
        $('.row-selector:visible').prop('checked', true);
    });

    $('#mark-all-absent').click(function() {
        $('.attended-checkbox:visible').prop('checked', false);
        $('.row-selector:visible').prop('checked', true);
    });

    $('.attended-checkbox').change(function() {
        let row = $(this).closest('tr');
        let checkbox = row.find('.row-selector');
        checkbox.prop('checked', true);
    });

    $('#modal-activity-filter').change(function() {
        let activityId = $(this).val();
        
        $('.attendance-row').each(function() {
            if (activityId === '' || $(this).data('activity-id') == activityId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        $('#select-all-modal').prop('checked', false);
    });
    
    // ==================== DELETE CONFIRMATION ====================
    window.confirmDelete = function(id, name) {
        $('#delete-user-name').text(name);
        $('#delete-form').attr('action', '/activity-users/' + id);
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    };
    
    // ==================== NOTIFICATION ====================
    function showNotification(type, message) {
        let alertDiv = $('<div>').addClass('alert alert-' + (type === 'success' ? 'success' : 'danger') + 
            ' alert-dismissible fade show position-fixed top-0 end-0 m-3').css('z-index', '9999');
        alertDiv.html(message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>');
        $('body').append(alertDiv);
        
        setTimeout(function() {
            alertDiv.alert('close');
        }, 3000);
    }
    
    // ==================== KEYBOARD SHORTCUTS ====================
    $(document).keydown(function(e) {
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            $('#search').focus();
        }
    });
});
</script>
@endpush