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
                        <a href="{{ route('activity-users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New
                        </a>
                        <a href="{{ route('activity-users.bulk.form') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-users"></i> Bulk Assign
                        </a>
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
                    {{-- Filter Form --}}
                    <div class="filter-section">
                        <form method="GET" action="{{ route('activity-users.index') }}" id="filter-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="activity_id" class="form-label">Activity</label>
                                    <select name="activity_id" id="activity_id" class="form-select">
                                        <option value="">All Activities</option>
                                        @foreach($activities as $activity)
                                            <option value="{{ $activity->activity_id }}" {{ request('activity_id') == $activity->activity_id ? 'selected' : '' }}>
                                                {{ $activity->activity_title_en }} 
                                                @if($activity->activity_title_ar)
                                                    ({{ $activity->activity_title_ar }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="user_id" class="form-label">User</label>
                                    <select name="user_id" id="user_id" class="form-select">
                                        <option value="">All Users</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->user_id }}" {{ request('user_id') == $user->user_id ? 'selected' : '' }}>
                                                {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label for="cop_id" class="form-label">COP</label>
                                    <select name="cop_id" id="cop_id" class="form-select">
                                        <option value="">All COPs</option>
                                        @foreach($cops as $cop)
                                            <option value="{{ $cop->cop_id }}" {{ request('cop_id') == $cop->cop_id ? 'selected' : '' }}>
                                                {{ $cop->cop_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label for="type" class="form-label">Type</label>
                                    <select name="type" id="type" class="form-select">
                                        <option value="">All Types</option>
                                        @foreach($types ?? [] as $type)
                                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endforeach
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
                                
                                <div class="col-md-2">
                                    <label for="date_from" class="form-label">Date From</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" 
                                           value="{{ request('date_from') }}">
                                </div>
                                
                                <div class="col-md-2">
                                    <label for="date_to" class="form-label">Date To</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" 
                                           value="{{ request('date_to') }}">
                                </div>
                                
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2 w-100">
                                        <i class="fas fa-filter"></i> Apply Filters
                                    </button>
                                </div>
                            </div>
                            
                            <div class="row mt-2">
                                <div class="col-12">
                                    <a href="{{ route('activity-users.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-undo"></i> Reset All Filters
                                    </a>
                                    <span class="text-muted ms-2">
                                        Total Records: {{ $activityUsers->total() }}
                                    </span>
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
                                                @if($activityUser->user->phone_number)
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-phone"></i> {{ $activityUser->user->phone_number }}
                                                    </small>
                                                @endif
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
                                                @if($activityUser->activity->venue)
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt"></i> {{ $activityUser->activity->venue }}
                                                    </small>
                                                @endif
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
                                                <br>
                                                <span class="text-muted">{{ $activityUser->created_at ? $activityUser->created_at->format('h:i A') : '' }}</span>
                                            </small>
                                        </td>
                                        <td class="action-buttons">
                                            <a href="{{ route('activity-users.show', $activityUser->activity_user_id) }}" 
                                               class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('activity-users.edit', $activityUser->activity_user_id) }}" 
                                               class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('activity-users.export-single', $activityUser->activity_user_id) }}" 
                                               class="btn btn-sm btn-warning" title="Export">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Delete"
                                                    onclick="confirmDelete('{{ $activityUser->activity_user_id }}', '{{ $activityUser->user->first_name ?? '' }} {{ $activityUser->user->last_name ?? '' }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            
                                            {{-- Quick Actions Dropdown --}}
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="toggleInvited('{{ $activityUser->activity_user_id }}')">
                                                            <i class="fas fa-envelope"></i> 
                                                            {{ $activityUser->invited ? 'Mark as Not Invited' : 'Mark as Invited' }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="toggleLead('{{ $activityUser->activity_user_id }}')">
                                                            <i class="fas fa-star"></i> 
                                                            {{ $activityUser->is_lead ? 'Remove Lead' : 'Make Lead' }}
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="confirmDelete('{{ $activityUser->activity_user_id }}', '{{ $activityUser->user->first_name ?? '' }}')">
                                                            <i class="fas fa-trash-alt"></i> Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5>No Activity-User Relationships Found</h5>
                                            <p class="text-muted">Get started by creating a new relationship or adjusting your filters.</p>
                                            <a href="{{ route('activity-users.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Add New Relationship
                                            </a>
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
                                    {{ $activity->activity_title_en }} - {{ $activity->activity_title_ar ?? '' }}
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
                                            @else
                                                <span class="text-danger">User not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $activityUser->activity->activity_title_en ?? 'N/A' }}
                                            <br>
                                            <small>{{ $activityUser->activity->start_date ?? 'No date' }}</small>
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
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    
    // Select all checkboxes
    $('#select-all').change(function() {
        $('.attendance-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Modal functionality
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

    // Sync checkbox with attended checkbox
    $('.attended-checkbox').change(function() {
        let row = $(this).closest('tr');
        let checkbox = row.find('.row-selector');
        checkbox.prop('checked', true);
    });

    // Filter in modal
    $('#modal-activity-filter').change(function() {
        let activityId = $(this).val();
        
        $('.attendance-row').each(function() {
            if (activityId === '' || $(this).data('activity-id') == activityId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        
        // Update select all visibility
        $('#select-all-modal').prop('checked', false);
    });

    // Row selection
    $('tbody tr').click(function(e) {
        if (!$(e.target).is('button') && !$(e.target).is('i') && !$(e.target).is('a') && !$(e.target).is('input')) {
            $('tbody tr').removeClass('selected-row');
            $(this).addClass('selected-row');
        }
    });

    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Alt + A to toggle attendance on selected row
        if (e.altKey && e.key === 'a') {
            let selectedRow = $('tr.selected-row');
            if (selectedRow.length) {
                selectedRow.find('.attendance-toggle').click();
            }
        }
        
        // Alt + D to delete selected row
        if (e.altKey && e.key === 'd') {
            let selectedRow = $('tr.selected-row');
            if (selectedRow.length) {
                let userId = selectedRow.data('id');
                let userName = selectedRow.find('td:eq(2)').text().trim();
                confirmDelete(userId, userName);
            }
        }
    });
});





/
</script>
@endpush