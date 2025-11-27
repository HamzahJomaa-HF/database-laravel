@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- ========================================================= --}}
    {{-- PAGE HEADER --}}
    {{-- ========================================================= --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap align-items-center gap-3">

                {{-- Title & Subtitle --}}
                <div>
                    <h1 class="h3 fw-bold text-dark mb-1">
                        <i class="bi bi-people-fill me-2 text-primary"></i>User Management
                    </h1>
                    <p class="text-muted mb-0">Manage and organize your user directory efficiently.</p>
                </div>

                {{-- Primary Actions --}}
                <div class="d-flex gap-2">
                    <a href="{{ route('users.export.excel', request()->query()) }}"
                       class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                        <i class="bi bi-file-earmark-excel me-2"></i> Export Data
                    </a>

                    <a href="{{ route('users.create') }}"
                       class="btn btn-primary btn-sm d-flex align-items-center">
                        <i class="bi bi-person-plus me-2"></i> Add User
                    </a>
                </div>
            </div>
        </div>
    </div>

    
    {{-- ========================================================= --}}
    {{-- FILTERS PANEL --}}
    {{-- ========================================================= --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">

                {{-- Header --}}
                <div class="card-header bg-white pt-3 pb-2 border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="fw-semibold text-dark mb-0 d-flex align-items-center">
                            <i class="bi bi-funnel me-2 text-primary"></i>Search & Filter Users
                        </h5>

                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary">{{ $users->total() }} users</span>
                            @if($hasSearch)
                                <span class="badge bg-warning-subtle text-warning">Filters Active</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body pt-3 pb-3">

                    {{-- FILTER FORM --}}
                    <form method="GET" class="row g-3 align-items-end">
                        {{-- Name --}}
                        <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6">
                            <label class="form-label small text-muted fw-semibold mb-1">Name Search</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-person text-muted"></i>
                                </span>
                                <input type="text" name="name" value="{{ request('name') }}"
                                       class="form-control border-start-0"
                                       placeholder="First, Middle, or Last name">
                            </div>
                        </div>

                        {{-- Gender --}}
                        <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6">
                            <label class="form-label small text-muted fw-semibold mb-1">Gender</label>
                            <select name="gender" class="form-select form-select-sm">
                                <option value="">All Genders</option>
                                <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        {{-- Marital --}}
                        <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6">
                            <label class="form-label small text-muted fw-semibold mb-1">Marital Status</label>
                            <select name="marital_status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                <option value="Single" {{ request('marital_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ request('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                <option value="Divorced" {{ request('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="Widowed" {{ request('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                        </div>

                        {{-- Phone --}}
                        <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-6">
                            <label class="form-label small text-muted fw-semibold mb-1">Phone Number</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-telephone text-muted"></i>
                                </span>
                                <input type="text" name="phone_number" value="{{ request('phone_number') }}"
                                       class="form-control border-start-0" placeholder="Phone number">
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="col-xxl-4 col-xl-12 col-lg-8 d-flex justify-content-end flex-wrap gap-2">
                            <button class="btn btn-primary btn-sm d-flex align-items-center">
                                <i class="bi bi-funnel me-2"></i>Apply Filters
                            </button>

                            @if($hasSearch)
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                </a>
                            @endif

                            <a href="{{ route('users.export.excel', request()->query()) }}"
                               class="btn btn-outline-secondary btn-sm d-flex align-items-center d-lg-none">
                                <i class="bi bi-download me-2"></i>Export
                            </a>
                            <a href="{{ route('users.import.form') }}" 
   class="btn btn-outline-secondary btn-sm d-flex align-items-center">
    <i class="bi bi-cloud-upload me-2"></i> Import Users
</a>
                        </div>
                    </form>

                    {{-- ACTIVE FILTERS --}}
                    @if($hasSearch)
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="fw-semibold text-muted small">Active Filters:</span>
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                $filters = [
                                    'name' => ['icon' => 'bi-person', 'label' => 'Name'],
                                    'gender' => ['icon' => 'bi-gender-ambiguous', 'label' => 'Gender'],
                                    'marital_status' => ['icon' => 'bi-heart', 'label' => 'Marital'],
                                    'phone_number' => ['icon' => 'bi-telephone', 'label' => 'Phone']
                                ];
                                @endphp

                                @foreach($filters as $key => $meta)
                                    @if(request($key))
                                        <span class="badge bg-primary-subtle border border-primary-subtle text-primary p-2 d-flex align-items-center">
                                            <i class="bi {{ $meta['icon'] }} me-1"></i>
                                            {{ $meta['label'] }}: <strong class="ms-1">{{ request($key) }}</strong>
                                            <a href="{{ remove_filter_url($key) }}" class="text-primary ms-2">
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

    {{-- ========================================================= --}}
    {{-- USERS TABLE --}}
    {{-- ========================================================= --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                
                {{-- Table Header --}}
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 py-3">
                    <h5 class="fw-semibold text-dark mb-0 d-flex align-items-center">
                        <i class="bi bi-list-ul me-2 text-primary"></i>User Directory
                    </h5>

                    {{-- Client-side Quick Search --}}
                    <div class="input-group input-group-sm" style="max-width: 220px;">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="tableSearch" class="form-control border-start-0" placeholder="Quick search...">
                    </div>
                </div>

                {{-- Table --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="usersTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width:50px;">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </div>
                                    </th>
                                    <th>User Information</th>
                                    <th>Contact Details</th>
                                    <th>Personal Info</th>
                                    <th>Status & Role</th>
                                    <th class="text-center" style="width:160px;">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($users as $user)
                                <tr class="user-row">
                                    {{-- SELECT --}}
                                    <td class="ps-4">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->user_id }}">
                                        </div>
                                    </td>

                                    {{-- USER INFO --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center me-3">
                                                <span class="text-primary fw-bold fs-6">
                                                    {{ strtoupper($user->first_name[0] ?? '') }}{{ strtoupper($user->last_name[0] ?? '') }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="fw-semibold text-dark mb-0">{{ $user->first_name }} {{ $user->last_name }}</h6>
                                                @if($user->middle_name)
                                                    <small class="text-muted d-block">{{ $user->middle_name }}</small>
                                                @endif
                                                <div class="small text-muted mt-1">ID: {{ $user->identification_id ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- CONTACT --}}
                                    <td class="small">
                                        @if($user->phone_number)
                                        <div class="d-flex align-items-center mb-1 text-truncate">
                                            <i class="bi bi-telephone text-muted me-2"></i>
                                            <span class="text-dark">{{ $user->phone_number }}</span>
                                        </div>
                                        @endif
                                        @if($user->email)
                                        <div class="d-flex align-items-center text-truncate">
                                            <i class="bi bi-envelope text-muted me-2"></i>
                                            <span class="text-dark">{{ $user->email }}</span>
                                        </div>
                                        @endif
                                    </td>

                                    {{-- PERSONAL --}}
                                    <td class="small">
                                        @if($user->dob)
                                        <div class="mb-1">
                                            <strong class="text-muted">DOB:</strong> {{ \Carbon\Carbon::parse($user->dob)->format('M d, Y') }}
                                        </div>
                                        @endif
                                        @if($user->gender)
                                        <div class="mb-1">
                                            <strong class="text-muted">Gender:</strong> {{ $user->gender }}
                                        </div>
                                        @endif
                                        @if($user->marital_status)
                                        <div>
                                            <strong class="text-muted">Status:</strong> {{ $user->marital_status }}
                                        </div>
                                        @endif
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="small">
                                        <div class="d-flex flex-column gap-1">
                                            {{-- USER TYPE --}}
                                            <span class="badge fw-semibold py-2
                                                @class([
                                                    'bg-danger' => $user->type == 'Admin',
                                                    'bg-primary' => $user->type == 'Employee',
                                                    'bg-success' => $user->type == 'Customer',
                                                    'bg-warning text-dark' => $user->type == 'Partner',
                                                    'bg-secondary' => !in_array($user->type, ['Admin','Employee','Customer','Partner']),
                                                ])
                                            ">
                                                {{ $user->type ?? 'Stakeholder' }}
                                            </span>

                                            {{-- EMPLOYMENT --}}
                                            <span class="badge fw-semibold py-2
                                                @class([
                                                    'bg-success bg-opacity-75' => $user->employment_status == 'Employed',
                                                    'bg-danger bg-opacity-75' => $user->employment_status == 'Unemployed',
                                                    'bg-info' => $user->employment_status == 'Student',
                                                    'bg-secondary' => $user->employment_status == 'Retired',
                                                    'bg-light text-dark border border-secondary' => !in_array($user->employment_status, ['Employed','Unemployed','Student','Retired']),
                                                ])
                                            ">
                                                {{ $user->employment_status ?? 'Not specified' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- ACTIONS --}}
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            {{-- Edit --}}
                                            <a href="{{ route('users.edit', $user->user_id ) }}"
                                               class="btn btn-outline-primary border-end-0 d-flex align-items-center"
                                               title="Edit User" data-bs-toggle="tooltip">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            

                                            {{-- Delete --}}
                                            <form method="POST" action="{{ route('users.destroy', $user->user_id ) }}"
                                                  onsubmit="return confirm('Are you sure you want to delete {{ $user->first_name }} {{ $user->last_name }}?');"
                                                  class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger border-start-0 d-flex align-items-center" 
                                                        title="Delete User" data-bs-toggle="tooltip">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center bg-light">
                                        <i class="bi bi-people display-4 opacity-50 d-block mb-3"></i>
                                        <h5 class="fw-bold text-muted">No users found</h5>
                                        @if($hasSearch)
                                            <p class="mb-3 text-muted">Try adjusting your search criteria or</p>
                                            <a href="{{ route('users.index') }}" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-arrow-clockwise me-1"></i> Clear all filters
                                            </a>
                                        @else
                                            <p class="mb-3 text-muted">Get started by adding your first user.</p>
                                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-person-plus me-1"></i> Add First User
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex justify-content-between flex-wrap align-items-center">
                        <div class="text-muted small mb-2 mb-md-0">
                            Showing <strong>{{ $users->firstItem() ?? 0 }}</strong> to
                            <strong>{{ $users->lastItem() ?? 0 }}</strong> of
                            <strong>{{ $users->total() }}</strong> entries
                        </div>
                        <div>
                            {{ $users->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- BULK ACTION BAR --}}
    {{-- ========================================================= --}}
    <div class="row mt-4 fixed-bottom-bar d-none" id="bulkActionsBar">
        <div class="col-12">
            <div class="card bg-light border shadow-lg mx-auto mb-3" style="max-width:800px;">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <span id="selectedCount" class="fw-semibold text-dark small">0 users selected</span>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary btn-sm d-flex align-items-center">
                                    <i class="bi bi-file-earmark-arrow-up me-2"></i> Export Selected
                                </button>
                                <button class="btn btn-outline-danger btn-sm d-flex align-items-center">
                                    <i class="bi bi-trash me-2"></i> Delete Selected
                                </button>
                            </div>
                        </div>
                        <button class="btn-close" id="clearSelection"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ========================================================= --}}
{{-- TOAST NOTIFICATIONS --}}
{{-- ========================================================= --}}
@if(session('success'))
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;">
    <div class="toast bg-success text-white border-0 fade show" role="alert">
        <div class="d-flex align-items-center">
            <div class="toast-body d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>
                    <strong>Success</strong>
                    <div class="small">{{ session('success') }}</div>
                </div>
            </div>
            <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080;">
    <div class="toast bg-danger text-white border-0 fade show" role="alert">
        <div class="d-flex align-items-center">
            <div class="toast-body d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>
                    <strong>Error</strong>
                    <div class="small">{{ session('error') }}</div>
                </div>
            </div>
            <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif

@endsection

{{-- ========================================================= --}}
{{-- STYLES --}}
{{-- ========================================================= --}}
@section('styles')
<style>
    /* Consistent Card Styling */
    .card {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: box-shadow 0.2s ease;
    }

    .card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .card-header {
        background: #fff;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 1.25rem;
    }

    .card-body {
        padding: 1.25rem;
    }

    /* Professional Button System */
    .btn {
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border: 1px solid;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
    }

    /* Primary Button */
    .btn-primary {
        background: #4361ee;
        border-color: #4361ee;
        color: #fff;
    }

    .btn-primary:hover {
        background: #3a56d4;
        border-color: #3a56d4;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(67, 97, 238, 0.3);
    }

    /* Outline Buttons */
    .btn-outline-primary {
        background: transparent;
        border-color: #4361ee;
        color: #4361ee;
    }

    .btn-outline-primary:hover {
        background: #4361ee;
        border-color: #4361ee;
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-outline-secondary {
        background: transparent;
        border-color: #6c757d;
        color: #6c757d;
    }

    .btn-outline-secondary:hover {
        background: #6c757d;
        border-color: #6c757d;
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-outline-danger {
        background: transparent;
        border-color: #dc3545;
        color: #dc3545;
    }

    .btn-outline-danger:hover {
        background: #dc3545;
        border-color: #dc3545;
        color: #fff;
        transform: translateY(-1px);
    }

    /* Button Group Styling */
    .btn-group {
        border-radius: 6px;
        overflow: hidden;
    }

    .btn-group .btn {
        border-radius: 0;
        margin: 0;
    }

    .btn-group .btn:first-child {
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
    }

    .btn-group .btn:last-child {
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
    }

    /* Table Styling */
    .table {
        margin: 0;
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
    }

    .table td {
        padding: 1rem 0.75rem;
        border-color: #f8f9fa;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Avatar */
    .avatar-sm {
        width: 44px;
        height: 44px;
    }

    /* Badges */
    .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.65em;
        font-weight: 500;
        border-radius: 4px;
    }

    .bg-primary-subtle {
        background-color: #e7f1ff !important;
        color: #4361ee !important;
    }

    .bg-warning-subtle {
        background-color: #fff3cd !important;
        color: #856404 !important;
    }

    /* Stats Box */
    .stat-box {
        border-right: 1px solid #e9ecef;
        padding: 0.5rem 0;
    }

    .stat-box:last-child {
        border-right: none;
    }

    /* Input Groups */
    .input-group-sm {
        border-radius: 6px;
    }

    .input-group-sm .form-control,
    .input-group-sm .form-select {
        border-radius: 6px;
        font-size: 0.875rem;
    }

    .input-group-text {
        background: #f8f9fa;
        border-color: #e9ecef;
    }

    /* Bulk Actions Bar */
    .fixed-bottom-bar {
        position: fixed;
        bottom: 0;
        width: 100%;
        pointer-events: none;
        z-index: 1000;
    }

    .fixed-bottom-bar .card {
        pointer-events: all;
        border-radius: 8px 8px 0 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
        
        .btn-group {
            flex-direction: column;
        }
        
        .btn-group .btn {
            border-radius: 6px;
            margin-bottom: 0.25rem;
        }
        
        .btn-group .btn:first-child,
        .btn-group .btn:last-child {
            border-radius: 6px;
        }
    }
</style>
@endsection

{{-- ========================================================= --}}
{{-- SCRIPTS --}}
{{-- ========================================================= --}}
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    // Quick Search
    const tableSearch = document.getElementById('tableSearch');
    const rows = document.querySelectorAll('#usersTable .user-row');

    if (tableSearch) {
        tableSearch.addEventListener('input', () => {
            const term = tableSearch.value.toLowerCase();
            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    }

    // Bulk Select
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const bar = document.getElementById('bulkActionsBar');
    const countLabel = document.getElementById('selectedCount');
    const clearSel = document.getElementById('clearSelection');

    function updateBar() {
        const visible = [...checkboxes].filter(cb => cb.closest('.user-row').style.display !== 'none');
        const selected = visible.filter(cb => cb.checked).length;

        if (selected > 0) {
            bar.classList.remove('d-none');
            countLabel.textContent = `${selected} user${selected > 1 ? 's' : ''} selected`;
        } else {
            bar.classList.add('d-none');
        }

        if (selectAll) {
            selectAll.checked = selected === visible.length;
            selectAll.indeterminate = selected > 0 && selected < visible.length;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            const check = selectAll.checked;
            checkboxes.forEach(cb => {
                if (cb.closest('.user-row').style.display !== 'none') {
                    cb.checked = check;
                }
            });
            updateBar();
        });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateBar));
    if (tableSearch) tableSearch.addEventListener('input', updateBar);

    if (clearSel) {
        clearSel.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = false);
            if (selectAll) selectAll.checked = false;
            updateBar();
        });
    }

    updateBar();

    // Toasts
    document.querySelectorAll('.toast').forEach(t => {
        new bootstrap.Toast(t, { delay: 5000 }).show();
    });

});
</script>
@endsection