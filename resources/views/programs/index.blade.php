@extends('layouts.app')

@section('title', 'Activity Users Management')

@section('styles')
    <!-- Kendo UI CSS (for similar styling) -->
    <link href="https://kendo.cdn.telerik.com/themes/6.7.0/default/default-main.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --border-color: #e5e7eb;
            --table-header-bg: #f9fafb;
            --table-row-alt-bg: #f8fafc;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
        }
        
        .page-title {
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        .main-div {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .filtering-bar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            gap: 1rem;
        }
        
        .search-input-container {
            flex: 1;
            max-width: 400px;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 0.625rem 2.5rem 0.625rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .search-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            width: 1.25rem;
            height: 1.25rem;
        }
        
        .scrollable-grid {
            overflow-x: auto;
        }
        
        .table-container {
            min-width: 1200px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table thead th {
            background-color: var(--table-header-bg);
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid var(--border-color);
            font-size: 0.875rem;
            white-space: nowrap;
        }
        
        .table tbody tr {
            border-bottom: 1px solid var(--border-color);
        }
        
        .table tbody tr:nth-child(even) {
            background-color: var(--table-row-alt-bg);
        }
        
        .table tbody td {
            padding: 1rem;
            font-size: 0.875rem;
            color: #4b5563;
            vertical-align: middle;
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
        }
        
        .bg-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .bg-success {
            background-color: var(--success-color);
            color: white;
        }
        
        .bg-warning {
            background-color: var(--warning-color);
            color: white;
        }
        
        .bg-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .bg-info {
            background-color: var(--info-color);
            color: white;
        }
        
        .bg-secondary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .status-badge {
            width: 80px !important;
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
            height: 24px !important;
            line-height: 1 !important;
            border-radius: 0.375rem !important;
        }
        
        .type-badge {
            width: 100px !important;
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
            height: 22px !important;
            line-height: 1 !important;
            border-radius: 0.375rem !important;
            background-color: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb !important;
        }
        
        .reset-button {
            padding: 0.5rem 1rem;
            background-color: #ef4444;
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .reset-button:hover {
            background-color: #dc2626;
        }
        
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }
        
        .pagination-numbers {
            display: flex;
            gap: 0.5rem;
        }
        
        .pagination-button {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border-color);
            background-color: white;
            color: #374151;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .pagination-button:hover {
            background-color: #f3f4f6;
        }
        
        .pagination-button.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .pagination-nav-button {
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            background-color: white;
            color: #374151;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .pagination-nav-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .pagination-nav-button:hover:not(:disabled) {
            background-color: #f3f4f6;
        }
        
        .pagination-info {
            font-size: 0.875rem;
            color: var(--secondary-color);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }
        
        .empty-state-icon {
            width: 4rem;
            height: 4rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background-color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8;
            color: white;
            text-decoration: none;
        }
        
        .btn-outline {
            background-color: white;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        
        .btn-outline:hover {
            background-color: #eff6ff;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .btn-outline-primary {
            background-color: white;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-outline-primary:hover {
            background-color: #eff6ff;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .btn-outline-secondary {
            background-color: white;
            color: var(--secondary-color);
            border: 1px solid var(--secondary-color);
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f3f4f6;
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .btn-outline-success {
            background-color: white;
            color: var(--success-color);
            border: 1px solid var(--success-color);
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-outline-success:hover {
            background-color: #d1fae5;
            color: #059669;
            text-decoration: none;
        }
        
        .btn-outline-danger {
            background-color: white;
            color: #ef4444;
            border: 1px solid #ef4444;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-outline-danger:hover {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .btn-outline-warning {
            background-color: white;
            color: #f59e0b;
            border: 1px solid #f59e0b;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-outline-warning:hover {
            background-color: #fef3c7;
            color: #d97706;
        }
        
        .filters-container {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-select {
            padding: 0.625rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            background-color: white;
            color: #374151;
            min-width: 150px;
        }
        
        .d-flex {
            display: flex;
        }
        
        .justify-content-between {
            justify-content: space-between;
        }
        
        .align-items-center {
            align-items: center;
        }
        
        .gap-2 {
            gap: 0.5rem;
        }
        
        .gap-3 {
            gap: 1rem;
        }
        
        .me-3 {
            margin-right: 1rem;
        }
        
        .mb-2 {
            margin-bottom: 0.5rem;
        }
        
        .mb-3 {
            margin-bottom: 0.75rem;
        }
        
        .mb-4 {
            margin-bottom: 1rem;
        }
        
        .mt-3 {
            margin-top: 0.75rem;
        }
        
        .mt-4 {
            margin-top: 1rem;
        }
        
        .fw-medium {
            font-weight: 500;
        }
        
        .text-muted {
            color: #6b7280;
        }
        
        .small {
            font-size: 0.75rem;
        }
        
        .external-id {
            font-size: 0.875rem;
            color: #4b5563;
            font-weight: 500;
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-name {
            font-weight: 500;
            color: #1f2937;
        }
        
        .user-email {
            font-size: 0.75rem;
            color: #6b7280;
        }
        
        .activity-info {
            display: flex;
            flex-direction: column;
        }
        
        .activity-title {
            font-weight: 500;
            color: #1f2937;
        }
        
        .activity-dates {
            font-size: 0.75rem;
            color: #6b7280;
        }
        
        .toggle-attendance {
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .toggle-attendance:hover {
            transform: scale(1.05);
        }
        
        .quick-stats {
            background-color: #f8fafc;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .stat-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .stat-info {
            display: flex;
            flex-direction: column;
        }
        
        .stat-value {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .stat-label {
            font-size: 0.75rem;
            color: #6b7280;
        }
        
        @media (max-width: 768px) {
            .filtering-bar-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-input-container {
                max-width: 100%;
            }
            
            .filters-container {
                flex-wrap: wrap;
            }
            
            .table-container {
                min-width: auto;
            }
            
            .scrollable-grid {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .quick-stats {
                gap: 1rem;
            }
            
            .stat-item {
                width: calc(50% - 0.5rem);
            }
        }
    </style>
@endsection

@section('content')
<div class="dashboard-content">
    <div class="main-div">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="page-title">Activity Users Management</h4>
            <div class="action-buttons">
                <a href="{{ route('activity-users.create') }}" class="btn-outline">
                    <i class="fas fa-plus-circle"></i> Add New
                </a>
                <a href="{{ route('activity-users.create') }}?bulk=1" class="btn-outline">
                    <i class="fas fa-users"></i> Bulk Assign
                </a>
            </div>
        </div>

        <!-- Quick Statistics -->
        <div class="quick-stats">
            <div class="stat-item">
                <div class="stat-icon" style="background-color: var(--primary-color);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ $activityUsers->total() }}</span>
                    <span class="stat-label">Total Assignments</span>
                </div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon" style="background-color: var(--success-color);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ $activityUsers->where('attended', true)->count() }}</span>
                    <span class="stat-label">Attended</span>
                </div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon" style="background-color: var(--warning-color);">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ $activityUsers->where('invited', true)->count() }}</span>
                    <span class="stat-label">Invited</span>
                </div>
            </div>
            
            <div class="stat-item">
                <div class="stat-icon" style="background-color: var(--info-color);">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-value">{{ $activityUsers->where('is_lead', true)->count() }}</span>
                    <span class="stat-label">Leads</span>
                </div>
            </div>
        </div>

        <!-- Filtering Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="filtering-bar-container">
                    <div class="search-input-container">
                        <input type="text" 
                               class="search-input" 
                               placeholder="Search by user name, email, activity..." 
                               value="{{ request('search') }}"
                               id="searchInput">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    
                    <div class="filters-container">
                        <select class="filter-select" id="activityFilter">
                            <option value="">All Activities</option>
                            @foreach($activities ?? [] as $activity)
                                <option value="{{ $activity->activity_id }}" {{ request('activity_id') == $activity->activity_id ? 'selected' : '' }}>
                                    {{ $activity->activity_title_en }}
                                </option>
                            @endforeach
                        </select>
                        
                        <select class="filter-select" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="participant" {{ request('type') == 'participant' ? 'selected' : '' }}>Participant</option>
                            <option value="trainer" {{ request('type') == 'trainer' ? 'selected' : '' }}>Trainer</option>
                            <option value="organizer" {{ request('type') == 'organizer' ? 'selected' : '' }}>Organizer</option>
                            <option value="speaker" {{ request('type') == 'speaker' ? 'selected' : '' }}>Speaker</option>
                        </select>
                        
                        <select class="filter-select" id="attendedFilter">
                            <option value="">Attendance (All)</option>
                            <option value="1" {{ request('attended') === '1' ? 'selected' : '' }}>Attended</option>
                            <option value="0" {{ request('attended') === '0' ? 'selected' : '' }}>Not Attended</option>
                        </select>
                        
                        <select class="filter-select" id="invitedFilter">
                            <option value="">Invitation (All)</option>
                            <option value="1" {{ request('invited') === '1' ? 'selected' : '' }}>Invited</option>
                            <option value="0" {{ request('invited') === '0' ? 'selected' : '' }}>Not Invited</option>
                        </select>
                        
                        <select class="filter-select" id="leadFilter">
                            <option value="">Lead Status (All)</option>
                            <option value="1" {{ request('is_lead') === '1' ? 'selected' : '' }}>Is Lead</option>
                            <option value="0" {{ request('is_lead') === '0' ? 'selected' : '' }}>Not Lead</option>
                        </select>
                        
                        <button class="btn-outline" onclick="resetFilters()">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Records Count -->
        <div class="mb-3">
            <span class="text-muted">Total Records: {{ $activityUsers->total() }}</span>
        </div>

        <!-- Table -->
        <div class="scrollable-grid">
            <div class="table-container">
                @if($activityUsers->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>External ID</th>
                                <th>User</th>
                                <th>Activity</th>
                                <th>Type</th>
                                <th>COP</th>
                                <th>Status</th>
                                <th>Attendance</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityUsers as $record)
                            <tr>
                                <td>
                                    <span class="external-id">{{ $record->external_id ?? 'N/A' }}</span>
                                </td>
                                
                                <td>
                                    @if($record->user)
                                        <div class="user-info">
                                            <span class="user-name">
                                                {{ $record->user->first_name }} {{ $record->user->last_name }}
                                            </span>
                                            <span class="user-email">{{ $record->user->email }}</span>
                                            @if($record->user->type)
                                                <span class="badge bg-secondary status-badge" style="width: auto; margin-top: 0.25rem;">
                                                    {{ ucfirst($record->user->type) }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">User not found</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if($record->activity)
                                        <div class="activity-info">
                                            <span class="activity-title">{{ $record->activity->activity_title_en }}</span>
                                            @if($record->activity->activity_title_ar)
                                                <span class="small text-muted">{{ $record->activity->activity_title_ar }}</span>
                                            @endif
                                            <span class="activity-dates">
                                                @if($record->activity->start_date)
                                                    {{ date('d M Y', strtotime($record->activity->start_date)) }}
                                                @endif
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-muted">Activity not found</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if($record->type)
                                        <span class="type-badge">{{ ucfirst($record->type) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if($record->cop)
                                        <span class="badge bg-info">{{ $record->cop->cop_name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($record->is_lead)
                                            <span class="badge bg-warning status-badge">Lead</span>
                                        @endif
                                        @if($record->invited)
                                            <span class="badge bg-info status-badge">Invited</span>
                                        @endif
                                    </div>
                                </td>
                                
                                <td>
                                    <form action="{{ route('activity-users.update', $record->activity_user_id) }}" 
                                          method="POST" 
                                          class="d-inline toggle-form">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="user_id" value="{{ $record->user_id }}">
                                        <input type="hidden" name="activity_id" value="{{ $record->activity_id }}">
                                        <input type="hidden" name="attended" value="{{ $record->attended ? 0 : 1 }}">
                                        <input type="hidden" name="invited" value="{{ $record->invited }}">
                                        <input type="hidden" name="is_lead" value="{{ $record->is_lead }}">
                                        <input type="hidden" name="quick_toggle" value="1">
                                        <input type="hidden" name="redirect_back" value="1">
                                        
                                        <button type="submit" 
                                                class="badge {{ $record->attended ? 'bg-success' : 'bg-secondary' }} status-badge toggle-attendance"
                                                title="{{ $record->attended ? 'Mark as absent' : 'Mark as attended' }}">
                                            <i class="fas {{ $record->attended ? 'fa-check-circle' : 'fa-circle' }}"></i>
                                            {{ $record->attended ? 'Attended' : 'Absent' }}
                                        </button>
                                    </form>
                                </td>
                                
                                <td>
                                    <span class="small">
                                        {{ $record->created_at ? $record->created_at->format('d M Y') : '-' }}
                                    </span>
                                </td>
                                
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('activity-users.edit', $record->activity_user_id) }}" 
                                           class="btn-outline-secondary btn-sm"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form action="{{ route('activity-users.destroy', $record->activity_user_id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-outline-danger btn-sm" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        
                                        @if($record->activity)
                                            <a href="{{ route('activity-users.statistics', $record->activity_id) }}" 
                                               class="btn-outline-primary btn-sm"
                                               title="Activity Statistics">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="fas fa-users empty-state-icon"></i>
                        <h5 class="mb-2">No activity-user assignments found</h5>
                        <p class="text-muted">Create your first assignment to get started</p>
                        <div class="d-flex gap-2 justify-content-center mt-3">
                            <a href="{{ route('activity-users.create') }}" class="btn-primary">
                                <i class="fas fa-plus-circle"></i> Add New Assignment
                            </a>
                            <a href="{{ route('activity-users.create') }}?bulk=1" class="btn-outline">
                                <i class="fas fa-users"></i> Bulk Assign
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if($activityUsers->hasPages())
        <div class="pagination-container">
            <div class="pagination-numbers">
                @if($activityUsers->onFirstPage())
                    <button class="pagination-nav-button" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $activityUsers->previousPageUrl() }}" class="pagination-nav-button">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif
                
                @php
                    $start = max(1, $activityUsers->currentPage() - 2);
                    $end = min($activityUsers->lastPage(), $activityUsers->currentPage() + 2);
                @endphp
                
                @if($start > 1)
                    <a href="{{ $activityUsers->url(1) }}" class="pagination-button">1</a>
                    @if($start > 2)
                        <span class="pagination-button" style="border: none; background: none;">...</span>
                    @endif
                @endif
                
                @foreach(range($start, $end) as $page)
                    <a href="{{ $activityUsers->url($page) }}" 
                       class="pagination-button {{ $activityUsers->currentPage() == $page ? 'active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach
                
                @if($end < $activityUsers->lastPage())
                    @if($end < $activityUsers->lastPage() - 1)
                        <span class="pagination-button" style="border: none; background: none;">...</span>
                    @endif
                    <a href="{{ $activityUsers->url($activityUsers->lastPage()) }}" class="pagination-button">
                        {{ $activityUsers->lastPage() }}
                    </a>
                @endif
                
                @if($activityUsers->hasMorePages())
                    <a href="{{ $activityUsers->nextPageUrl() }}" class="pagination-nav-button">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <button class="pagination-nav-button" disabled>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                @endif
            </div>
            
            <div class="pagination-info">
                Showing {{ $activityUsers->firstItem() }} to {{ $activityUsers->lastItem() }} of {{ $activityUsers->total() }} entries
            </div>
            
            <div>
                <select class="filter-select" onchange="changePerPage(this.value)">
                    <option value="10" {{ $activityUsers->perPage() == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $activityUsers->perPage() == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $activityUsers->perPage() == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $activityUsers->perPage() == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
    <script>
        // Search and filter functionality
        const searchInput = document.getElementById('searchInput');
        const activityFilter = document.getElementById('activityFilter');
        const typeFilter = document.getElementById('typeFilter');
        const attendedFilter = document.getElementById('attendedFilter');
        const invitedFilter = document.getElementById('invitedFilter');
        const leadFilter = document.getElementById('leadFilter');
        
        let searchTimeout;
        
        // Add event listeners
        searchInput.addEventListener('keyup', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });
        
        activityFilter.addEventListener('change', applyFilters);
        typeFilter.addEventListener('change', applyFilters);
        attendedFilter.addEventListener('change', applyFilters);
        invitedFilter.addEventListener('change', applyFilters);
        leadFilter.addEventListener('change', applyFilters);
        
        function applyFilters() {
            const params = new URLSearchParams();
            
            if (searchInput.value) {
                params.set('search', searchInput.value);
            }
            
            if (activityFilter.value) {
                params.set('activity_id', activityFilter.value);
            }
            
            if (typeFilter.value) {
                params.set('type', typeFilter.value);
            }
            
            if (attendedFilter.value !== '') {
                params.set('attended', attendedFilter.value);
            }
            
            if (invitedFilter.value !== '') {
                params.set('invited', invitedFilter.value);
            }
            
            if (leadFilter.value !== '') {
                params.set('is_lead', leadFilter.value);
            }
            
            // Keep per_page if exists
            const currentPerPage = new URLSearchParams(window.location.search).get('per_page');
            if (currentPerPage) {
                params.set('per_page', currentPerPage);
            }
            
            window.location.href = '{{ route("activity-users.index") }}?' + params.toString();
        }
        
        function resetFilters() {
            window.location.href = '{{ route("activity-users.index") }}';
        }
        
        function changePerPage(value) {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', value);
            window.location.href = '{{ route("activity-users.index") }}?' + params.toString();
        }
        
        // Initialize page with current filters
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.get('search')) {
                searchInput.value = urlParams.get('search');
            }
            
            // Auto-submit filters (optional - remove if you want manual submission)
            // This will trigger when any filter changes
            const filterSelects = document.querySelectorAll('.filter-select');
            filterSelects.forEach(select => {
                select.addEventListener('change', applyFilters);
            });
        });
        
        // Add loading state to toggle buttons
        document.querySelectorAll('.toggle-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('button[type="submit"]');
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;
            });
        });
        
        // Keyboard shortcut for search (Ctrl+K or Cmd+K)
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    </script>
@endsection