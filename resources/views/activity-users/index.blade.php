{{-- resources/views/activity-users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Activity Users Management')

@section('styles')
    <!-- Kendo UI CSS (for similar styling) -->
    <link href="https://kendo.cdn.telerik.com/themes/6.7.0/default/default-main.css" rel="stylesheet">
  
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --border-color: #e5e7eb;
            --table-header-bg: #f9fafb;
            --table-row-alt-bg: #f8fafc;
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
        
        .nav-tabs {
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1.5rem;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: var(--secondary-color);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            background: none;
            border-bottom: 2px solid transparent;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        
        .scrollable-grid {
            overflow-x: auto;
        }
        
        .table-container {
            min-width: 1000px;
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
            background-color: #0d6efd !important;
            color: white;
        }
        
        .bg-secondary {
            background-color: #6c757d !important;
            color: white;
        }
        
        .bg-success {
            background-color: #198754 !important;
            color: white;
        }
        
        .bg-info {
            background-color: #0dcaf0 !important;
            color: black;
        }
        
        .bg-warning {
            background-color: #ffc107 !important;
            color: black;
        }
        
        .bg-danger {
            background-color: #dc3545 !important;
            color: white;
        }
        
        .info-icon {
            color: #757575;
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .info-icon:hover {
            color: var(--primary-color);
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
            color: #0d6efd;
            border: 1px solid #0d6efd;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-outline-primary:hover {
            background-color: #e7f1ff;
            color: #0d6efd;
            text-decoration: none;
        }
        
        .btn-outline-secondary {
            background-color: white;
            color: #6c757d;
            border: 1px solid #6c757d;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            color: #6c757d;
            text-decoration: none;
        }
        
        .btn-outline-success {
            background-color: white;
            color: #198754;
            border: 1px solid #198754;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-outline-success:hover {
            background-color: #d1e7dd;
            color: #198754;
            text-decoration: none;
        }
        
        /* Red delete button */
        .btn-outline-danger {
            background-color: white;
            color: #dc3545;
            border: 1px solid #dc3545;
            border-radius: 0.375rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
            text-decoration: none;
        }
        
        .filters-container {
            display: flex;
            gap: 1rem;
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
        
        .attendance-toggle {
            min-width: 70px;
        }

        /* Toast container and toast styles */
        .toast-container {
            z-index: 9999;
        }
        
        .toast {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            min-width: 300px;
        }
        
        .toast .btn-close-white {
            filter: brightness(0) invert(1);
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
            
            .toast {
                min-width: 250px;
            }
        }
    </style>
@endsection

@section('content')
<div class="dashboard-content">
    {{-- Toast Notifications --}}
    @if(session('success'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast bg-success text-white border-0 fade show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-center">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>
                        <strong>Success</strong>
                        <div class="small">{{ session('success') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast bg-danger text-white border-0 fade show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-center">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div>
                        <strong>Error</strong>
                        <div class="small">{{ session('error') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    @if(session('warning'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast bg-warning text-dark border-0 fade show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-center">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Warning</strong>
                        <div class="small">{{ session('warning') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    @if(session('info'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast bg-info text-white border-0 fade show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-center">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>
                        <strong>Info</strong>
                        <div class="small">{{ session('info') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    {{-- Validation Errors as Toast --}}
    @if($errors->any())
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast bg-danger text-white border-0 fade show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex align-items-center">
                <div class="toast-body">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Validation Error</strong>
                    <ul class="mb-0 mt-2 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    {{-- Bulk Actions Section --}}
    <div class="row mb-4" id="bulkActionsSection" style="display: none;">
        <div class="col-12">
            <div class="card shadow-sm border-0">
               <div class="card-header bg-white border-bottom py-4">
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
                
                <div class="card-body p-4">
                    <form method="POST" 
                          action="{{ route('activity-users.bulk.destroy') }}" 
                          id="bulkDeleteForm" 
                          class="d-inline">
                        @csrf
                        @method('DELETE')
<div id="selectedActivityUserIds"></div>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Delete Selected
                        </button>
                    </form>
                    <small class="text-muted ms-3">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        This action cannot be undone
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="main-div">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="page-title">Activity Users Management</h4>
            <div class="action-buttons">
                <a href="{{ route('activity-users.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Add Assignment
                </a>
            </div>
        </div>

        <!-- Filtering Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="filtering-bar-container" style="flex-direction: column; align-items: stretch;">
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                     
                        
                        <!-- User Search -->
                        <div class="search-input-container" style="flex: 1; min-width: 200px;">
                            <input type="text" 
                                   class="search-input" 
                                   placeholder="Search users..." 
                                   value="{{ request('user_search') }}"
                                   id="userSearchInput">
                           
                        </div>
                        
                        <!-- Activity Search -->
                        <div class="search-input-container" style="flex: 1; min-width: 200px;">
                            <input type="text" 
                                   class="search-input" 
                                   placeholder="Search activities..." 
                                   value="{{ request('activity_search') }}"
                                   id="activitySearchInput">
                            
                        </div>
                    </div>
                    
                    <div class="filters-container" style="margin-top: 0.5rem;">
                        <select class="filter-select" id="typeFilter">
    <option value="">All Types</option>
    <option value="Beneficiary" {{ request('type') == 'Beneficiary' ? 'selected' : '' }}>Beneficiary</option>
    <option value="Stakeholder" {{ request('type') == 'Stakeholder' ? 'selected' : '' }}>Stakeholder</option>
</select>
                        
                        <select class="filter-select" id="attendedFilter">
                            <option value="">Attendance: All</option>
                            <option value="1" {{ request('attended') === '1' ? 'selected' : '' }}>Attended</option>
                            <option value="0" {{ request('attended') === '0' ? 'selected' : '' }}>Absent</option>
                        </select>
                        
                        <select class="filter-select" id="invitedFilter">
                            <option value="">Invitation: All</option>
                            <option value="1" {{ request('invited') === '1' ? 'selected' : '' }}>Invited</option>
                            <option value="0" {{ request('invited') === '0' ? 'selected' : '' }}>Not Invited</option>
                        </select>
                        
                        <button class="btn-outline" onclick="resetFilters()">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#assignments-tab">
                    Assignments ({{ $activityUsers->total() }})
                </button>
            </li>
        </ul>

        <!-- Table -->
        <div class="mt-4">
            <div class="table-container">
                @if($activityUsers->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                                    </div>
                                </th>
                                <th>User</th>
                                <th>Activity</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Invited</th>
                                <th>Attendance</th>
                                <th>COP</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityUsers as $activityUser)
                            <tr class="activity-user-row align-middle {{ $loop->even ? 'table-row-alt' : '' }}">
                                <td class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input activity-user-checkbox" 
                                               type="checkbox" 
                                               value="{{ $activityUser->activity_user_id }}"
                                               id="activity_user_{{ $activityUser->activity_user_id }}">
                                    </div>
                                </td>
                                <td>
                                    @if($activityUser->user)
                                        <div>
                                            <strong>{{ $activityUser->user->first_name }} {{ $activityUser->user->last_name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope"></i> {{ $activityUser->user->email }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">User not found</span>
                                    @endif
                                </td>
                                <td>
                                    @if($activityUser->activity)
                                        <div>
                                            <strong>{{ $activityUser->activity->activity_title_en }}</strong>
                                            @if($activityUser->activity->activity_title_ar)
                                                <br>
                                                <small class="text-muted">{{ $activityUser->activity->activity_title_ar }}</small>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> 
                                                {{ $activityUser->activity->start_date ? date('d M Y', strtotime($activityUser->activity->start_date)) : 'No date' }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">Activity not found</span>
                                    @endif
                                </td>
                                <!-- New Location column -->
<td>
    @if($activityUser->activity && $activityUser->activity->venue)
        <div>
            <i class="bi bi-geo-alt text-muted me-2 small"></i>
            <span >{{ $activityUser->activity->venue }}</span>
        </div>
    @else
        <span class="text-muted">-</span>
    @endif
</td>



                                <td>
                                    @if($activityUser->user && $activityUser->user->type)
                                        @php
                                            $typeClass = match($activityUser->user->type) {
                                                'Beneficiary' => 'bg-success',
                                                'Stakeholder' => 'bg-primary',
                                                'trainer' => 'bg-warning text-dark',
                                                'staff' => 'bg-info',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $typeClass }}">
                                            {{ ucfirst($activityUser->user->type) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($activityUser->invited)
                                        <span class="badge bg-info">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($activityUser->attended)
                                        <span class="badge bg-success">Attended</span>
                                    @else
                                        <span class="badge bg-danger">Absent</span>
                                    @endif
                                </td>
                                <td>
                                    @if($activityUser->cop)
                                        <span class="badge bg-secondary">{{ $activityUser->cop->cop_name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('activity-users.edit', $activityUser->activity_user_id) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" 
                                              action="{{ route('activity-users.destroy', $activityUser->activity_user_id) }}" 
                                              style="display: inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this relationship for {{ $activityUser->user->first_name ?? '' }} {{ $activityUser->user->last_name ?? '' }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <i class="fas fa-users empty-state-icon"></i>
                        <h5 class="mb-2">No assignments found</h5>
                        <p class="text-muted">Add your first user-activity assignment to get started</p>
                        <a href="{{ route('activity-users.create') }}" class="btn-primary mt-3">
                            <i class="fas fa-plus"></i> Add Assignment
                        </a>
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
                
                @foreach(range(1, min(5, $activityUsers->lastPage())) as $page)
                    <a href="{{ $activityUsers->url($page) }}" 
                       class="pagination-button {{ $activityUsers->currentPage() == $page ? 'active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach
                
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

{{-- Bulk Delete Confirmation Modal --}}
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirm Bulk Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <span id="deleteCount" class="fw-bold">0</span> selected relationship(s)?</p>
                <p class="text-danger small mb-0">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="bulkDeleteModalForm">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="activity_user_ids" id="modalSelectedActivityUserIds">
                    <button type="submit" class="btn btn-danger">Delete Selected</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        // DEFINE confirmDelete FIRST - at the very top of the script
        window.confirmDelete = function(id, name) {
            $('#delete-user-name').text(name);
            $('#delete-form').attr('action', '/activity-users/' + id);
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        };
        
        // Search functionality
        const userSearchInput = document.getElementById('userSearchInput');
        const activitySearchInput = document.getElementById('activitySearchInput');
        const typeFilter = document.getElementById('typeFilter');
        const attendedFilter = document.getElementById('attendedFilter');
        const invitedFilter = document.getElementById('invitedFilter');
        
        let searchTimeout;
        
        function applyFilters() {
            const params = new URLSearchParams();
            
            if (userSearchInput && userSearchInput.value) {
                params.set('user_search', userSearchInput.value);
            }
            
            if (activitySearchInput && activitySearchInput.value) {
                params.set('activity_search', activitySearchInput.value);
            }
            
            if (typeFilter && typeFilter.value) {
                params.set('type', typeFilter.value);
            }
            
            if (attendedFilter && attendedFilter.value) {
                params.set('attended', attendedFilter.value);
            }
            
            if (invitedFilter && invitedFilter.value) {
                params.set('invited', invitedFilter.value);
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
        
        // Notification function (for AJAX responses)
        function showNotification(type, message) {
            let toastContainer = $('<div>').addClass('toast-container position-fixed top-0 end-0 p-3');
            let toastClass = type === 'success' ? 'bg-success' : 'bg-danger';
            let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            let title = type === 'success' ? 'Success' : 'Error';
            
            let toast = $('<div>').addClass(`toast ${toastClass} text-white border-0 fade show`)
                .attr('role', 'alert')
                .attr('aria-live', 'assertive')
                .attr('aria-atomic', 'true');
            
            toast.html(`
                <div class="d-flex align-items-center">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas ${icon} me-2"></i>
                        <div>
                            <strong>${title}</strong>
                            <div class="small">${message}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `);
            
            toastContainer.append(toast);
            $('body').append(toastContainer);
            
            let bsToast = new bootstrap.Toast(toast[0], { delay: 5000 });
            bsToast.show();
            
            setTimeout(function() {
                toastContainer.remove();
            }, 6000);
        }
        
        // Main initialization - single DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded');
            
            // Initialize page with current filters
            const urlParams = new URLSearchParams(window.location.search);
            
            if (userSearchInput && urlParams.get('user_search')) {
                userSearchInput.value = urlParams.get('user_search');
            }
            
            if (activitySearchInput && urlParams.get('activity_search')) {
                activitySearchInput.value = urlParams.get('activity_search');
            }
            
            // Add event listeners for search and filters
            if (userSearchInput) {
                userSearchInput.addEventListener('keyup', function(e) {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(applyFilters, 500);
                });
            }
            
            if (activitySearchInput) {
                activitySearchInput.addEventListener('keyup', function(e) {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(applyFilters, 500);
                });
            }
            
            if (typeFilter) {
                typeFilter.addEventListener('change', applyFilters);
            }
            
            if (attendedFilter) {
                attendedFilter.addEventListener('change', applyFilters);
            }
            
            if (invitedFilter) {
                invitedFilter.addEventListener('change', applyFilters);
            }
            
            // Auto-initialize toasts
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 5000
                });
            });
            toastList.forEach(toast => toast.show());

            // Bulk Selection Functionality
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const activityUserCheckboxes = document.querySelectorAll('.activity-user-checkbox');
            const bulkActionsSection = document.getElementById('bulkActionsSection');
            const selectedCount = document.getElementById('selectedCount');
            const clearSelectionBtn = document.getElementById('clearSelectionBtn');
            const selectedActivityUserIds = document.getElementById('selectedActivityUserIds');
            const bulkDeleteForm = document.getElementById('bulkDeleteForm');
            
            // Log to see if elements are found (for debugging)
            console.log('Select All Checkbox:', selectAllCheckbox);
            console.log('Number of checkboxes:', activityUserCheckboxes.length);
            console.log('Bulk Actions Section:', bulkActionsSection);
            
            // If no checkboxes found, exit bulk functionality
            if (activityUserCheckboxes.length === 0) {
                console.log('No checkboxes found on the page');
                return;
            }
            
            // Function to update selection
            function updateSelection() {
                console.log('updateSelection called');
                
                // Get all checked checkboxes
                const selectedCheckboxes = [];
                activityUserCheckboxes.forEach(cb => {
                    if (cb.checked) {
                        selectedCheckboxes.push(cb);
                    }
                });
                
                const count = selectedCheckboxes.length;
                console.log('Selected count:', count);
                
                // Update count display
                if (selectedCount) {
                    selectedCount.textContent = count + ' selected';
                }
                
                // Show/hide bulk actions section
                if (count > 0) {
                    if (bulkActionsSection) {
                        bulkActionsSection.style.display = 'block';
                        console.log('Showing bulk actions');
                    }
                    
                    // Update hidden input container with selected activity user IDs
                    if (selectedActivityUserIds) {
                        selectedActivityUserIds.innerHTML = '';
                        
                        selectedCheckboxes.forEach(cb => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'activity_user_ids[]';
                            input.value = cb.value;
                            selectedActivityUserIds.appendChild(input);
                        });
                        console.log('Updated hidden inputs with ' + selectedCheckboxes.length + ' IDs');
                    }
                } else {
                    if (bulkActionsSection) {
                        bulkActionsSection.style.display = 'none';
                        console.log('Hiding bulk actions');
                    }
                    if (selectedActivityUserIds) {
                        selectedActivityUserIds.innerHTML = '';
                    }
                }
                
                // Update select all checkbox state
                if (selectAllCheckbox) {
                    if (count === 0) {
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = false;
                    } else if (count === activityUserCheckboxes.length) {
                        selectAllCheckbox.checked = true;
                        selectAllCheckbox.indeterminate = false;
                    } else {
                        selectAllCheckbox.checked = false;
                        selectAllCheckbox.indeterminate = true;
                    }
                }
                
                // Add/remove selected class from rows
                activityUserCheckboxes.forEach(cb => {
                    const row = cb.closest('tr');
                    if (row) {
                        if (cb.checked) {
                            row.classList.add('selected');
                        } else {
                            row.classList.remove('selected');
                        }
                    }
                });
            }
            
            // Add change event to each checkbox
            activityUserCheckboxes.forEach(function(checkbox, index) {
                console.log('Adding event to checkbox', index);
                checkbox.addEventListener('change', function(e) {
                    console.log('Checkbox changed', e.target.value, e.target.checked);
                    updateSelection();
                });
            });
            
            // Select all checkbox handler
            if (selectAllCheckbox) {
                console.log('Adding event to select all');
                selectAllCheckbox.addEventListener('change', function(e) {
                    console.log('Select all changed', e.target.checked);
                    const isChecked = this.checked;
                    activityUserCheckboxes.forEach(cb => {
                        cb.checked = isChecked;
                    });
                    updateSelection();
                });
            }
            
            // Clear selection button
            if (clearSelectionBtn) {
                console.log('Adding event to clear button');
                clearSelectionBtn.addEventListener('click', function() {
                    console.log('Clear button clicked');
                    activityUserCheckboxes.forEach(cb => {
                        cb.checked = false;
                    });
                    updateSelection();
                });
            }
            
            // Bulk delete form submission
            if (bulkDeleteForm) {
                console.log('Adding event to bulk delete form');
                bulkDeleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    console.log('Bulk delete form submitted');
                    
                    // Get checked checkboxes
                    const selectedCheckboxes = [];
                    activityUserCheckboxes.forEach(cb => {
                        if (cb.checked) {
                            selectedCheckboxes.push(cb);
                        }
                    });
                    
                    const count = selectedCheckboxes.length;
                    console.log('Selected for delete:', count);
                    
                    if (count === 0) {
                        alert('No assignments selected.');
                        return;
                    }
                    
                    if (confirm('Are you sure you want to delete ' + count + ' selected relationship(s)? This action cannot be undone.')) {
                        // Update hidden inputs one more time before submit
                        if (selectedActivityUserIds) {
                            selectedActivityUserIds.innerHTML = '';
                            selectedCheckboxes.forEach(cb => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'activity_user_ids[]';
                                input.value = cb.value;
                                selectedActivityUserIds.appendChild(input);
                            });
                            console.log('Final hidden inputs updated');
                        }
                        
                        // Show loading state
                        const submitBtn = this.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Deleting...';
                            submitBtn.disabled = true;
                        }
                        
                        // Submit the form
                        console.log('Submitting form...');
                        this.submit();
                    }
                });
            }
            
            // Initialize selection state
            console.log('Initializing selection state');
            updateSelection();

            // Attendance toggle functionality (if needed)
            const attendanceToggles = document.querySelectorAll('.attendance-toggle');
            attendanceToggles.forEach(button => {
                button.addEventListener('click', function() {
                    const btn = this;
                    const activityUserId = btn.dataset.id;
                    const url = '/activity-users/' + activityUserId + '/toggle-attendance';
                    
                    btn.disabled = true;
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            btn.dataset.attended = data.attended ? '1' : '0';
                            
                            if (data.attended) {
                                btn.classList.remove('btn-outline-secondary');
                                btn.classList.add('btn-outline-success');
                                btn.innerHTML = '<i class="fas fa-check-circle"></i> <span class="attendance-text">Attended</span>';
                                btn.closest('tr').classList.add('table-success');
                            } else {
                                btn.classList.remove('btn-outline-success');
                                btn.classList.add('btn-outline-secondary');
                                btn.innerHTML = '<i class="fas fa-circle"></i> <span class="attendance-text">Absent</span>';
                                btn.closest('tr').classList.remove('table-success');
                            }
                            
                            showNotification('success', data.message);
                        } else {
                            showNotification('error', 'Failed to update attendance');
                            btn.innerHTML = originalHtml;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('error', 'Failed to update attendance');
                        btn.innerHTML = originalHtml;
                    })
                    .finally(() => {
                        btn.disabled = false;
                    });
                });
            });

        }); // closes DOMContentLoaded
    </script>
@endsection