@extends('layouts.app')

@section('title', 'Hospital Financial Records')

@section('styles')
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

        .table-container {
            min-width: 1000px;
            overflow-x: auto;
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
        }

        .bg-hospital { background-color: #ef4444 !important; color: white; }
        .bg-success  { background-color: #198754 !important; color: white; }
        .bg-warning  { background-color: #ffc107 !important; color: black; }
        .bg-info     { background-color: #0dcaf0 !important; color: black; }
        .bg-danger   { background-color: #dc3545 !important; color: white; }
        .bg-secondary{ background-color: #6c757d !important; color: white; }

        .reset-button {
            padding: 0.5rem 1rem;
            background-color: #ef4444;
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
        }

        .reset-button:hover { background-color: #dc2626; }

        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .pagination-info { font-size: 0.875rem; color: var(--secondary-color); }

        .pagination-numbers { display: flex; gap: 0.5rem; align-items: center; }

        .pagination-button {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border-color);
            background-color: white;
            color: #374151;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .pagination-button:hover { background-color: #f3f4f6; text-decoration: none; }

        .pagination-button.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination-nav-button {
            padding: 0.5rem 0.625rem;
            border: 1px solid var(--border-color);
            background-color: white;
            color: #374151;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .pagination-nav-button:disabled { opacity: 0.5; cursor: not-allowed; }

        .pagination-nav-button:hover:not(:disabled) {
            background-color: #f3f4f6;
            text-decoration: none;
            color: #374151;
        }

        .pagination-ellipsis { padding: 0.5rem 0.25rem; color: #6b7280; font-size: 0.875rem; }

        .pagination-perpage { display: flex; align-items: center; gap: 0.5rem; }
        .pagination-perpage .filter-select { width: auto; padding: 0.375rem 0.75rem; }

        .empty-state { text-align: center; padding: 3rem; color: #6b7280; }
        .empty-state-icon { width: 4rem; height: 4rem; color: #d1d5db; margin-bottom: 1rem; }

        .action-buttons { display: flex; gap: 0.75rem; }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary:hover { background-color: #1d4ed8; color: white; text-decoration: none; }

        .btn-outline {
            background-color: white;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-outline:hover { background-color: #eff6ff; color: var(--primary-color); text-decoration: none; }

        .btn-details {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            text-decoration: none;
        }

        .btn-details:hover { background-color: #1d4ed8; color: white; }

        .btn-edit-modal {
            background-color: #f59e0b;
            color: white;
            border: none;
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            margin-right: 0.5rem;
        }

        .btn-edit-modal:hover { background-color: #d97706; color: white; }

        .btn-save-modal {
            background-color: #10b981;
            color: white;
            border: none;
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .btn-save-modal:hover { background-color: #059669; color: white; }

        .btn-cancel-modal {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .edit-input {
            width: 100%;
            padding: 0.25rem 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            font-size: 0.75rem;
        }

        .edit-select {
            width: 100%;
            padding: 0.25rem 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            background-color: white;
        }

        .filters-container {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
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

        .d-flex   { display: flex; }
        .gap-2    { gap: 0.5rem; }
        .mt-4     { margin-top: 1rem; }
        .mb-4     { margin-bottom: 1rem; }
        .text-muted { color: #6b7280; }
        .fw-medium  { font-weight: 500; }

        .form-check-input { width: 1.2em; height: 1.2em; cursor: pointer; }

        tr.selected { background-color: #eff6ff !important; }

        .toast-container { z-index: 9999; }

        .modal-content { border-radius: 0.75rem; }

        .modal-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 0.75rem 0.75rem 0 0;
        }

        .details-section {
            margin-bottom: 1rem;
            padding: 0.75rem;
            background-color: #f8fafc;
            border-radius: 0.5rem;
        }

        .details-section-title {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.75rem;
            padding-bottom: 0.25rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 0.75rem;
        }

        .details-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background-color: white;
            border-radius: 0.375rem;
            border: 1px solid #e2e8f0;
        }

        .details-label { font-weight: 600; color: #475569; text-transform: capitalize; }

        .details-value { color: #1e293b; font-family: monospace; }

        .details-value.highlight { color: var(--primary-color); font-weight: 600; }

        @media (max-width: 768px) {
            .filtering-bar-container { flex-direction: column; align-items: stretch; }
            .search-input-container  { max-width: 100%; }
            .table-container         { overflow-x: auto; }
            .pagination-container    { flex-direction: column; align-items: center; gap: 1rem; }
            .pagination-numbers      { order: 2; }
            .pagination-perpage      { order: 1; }
            .pagination-info         { order: 3; }
        }
    </style>
@endsection

@section('content')
<div class="dashboard-content">
    {{-- Toast Notifications --}}
    @if(session('success'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast bg-success text-white border-0 fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
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
                    <i class="fas fa-exclamation-circle me-2"></i>
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

    @if(session('info'))
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div class="toast bg-info text-white border-0 fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="toast-body d-flex align-items-center">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>
                        <strong>Info</strong>
                        <div class="small">{{ session('info') }}</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    @endif

    {{-- Bulk Actions Section --}}
    <div class="row mb-4" id="bulkActionsSection" style="display: none;">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="mb-0">
                                <i class="bi bi-check-all me-2"></i>Bulk Actions
                            </h5>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-white text-dark me-3" id="selectedCount">0 selected</span>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clearSelectionBtn">
                                <i class="bi bi-x-circle me-1"></i>Clear Selection
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('financials.bulk.destroy') }}" id="bulkDeleteForm" class="d-inline"
                          onsubmit="return confirm('Delete the selected records? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <div id="selectedFinancialIds"></div>
                        <button type="submit" class="btn btn-danger btn-sm">
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
            <h4 class="page-title">Hospital Financial Records</h4>
            <div class="action-buttons">
                <a href="{{ route('financials.visualization') }}" class="btn btn-outline-primary me-2" style="background-color:white;border-color:#7c3aed;color:#7c3aed;">
                    <i class="fas fa-chart-pie me-1"></i> Visualization
                </a>
                <a href="{{ route('financials.import.form') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-file-import me-1"></i> Import
                </a>
                <a href="{{ route('financials.medical.medicine') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-pills me-1"></i> Medicine
                </a>
            </div>
        </div>

        <!-- Filtering Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="filtering-bar-container" style="flex-direction: column; align-items: stretch;">
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <div class="search-input-container" style="flex: 1; min-width: 200px;">
                            <input type="text" class="search-input" placeholder="Search by activity..." value="{{ request('activity_search') }}" id="activitySearchInput">
                        </div>
                        <div class="search-input-container" style="flex: 1; min-width: 200px;">
                            <input type="text" class="search-input" placeholder="Search by user..." value="{{ request('user_search') }}" id="userSearchInput">
                        </div>
                    </div>

                    <div class="filters-container">
                        <select class="filter-select" id="operationTypeFilter">
                            <option value="">All Operation Types</option>
                            @foreach($operationTypes as $operationType)
                                <option value="{{ $operationType }}" {{ request('operation_type') == $operationType ? 'selected' : '' }}>
                                    {{ $operationType }}
                                </option>
                            @endforeach
                        </select>

                        <select class="filter-select" id="paymentStatusFilter">
                            <option value="">All Status</option>
                            <option value="paid"    {{ request('payment_status') == 'paid'    ? 'selected' : '' }}>Paid</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="overdue" {{ request('payment_status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>

                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="date" class="filter-select" id="startDateFilter" value="{{ request('start_date') }}" style="min-width: 130px;">
                            <span>to</span>
                            <input type="date" class="filter-select" id="endDateFilter" value="{{ request('end_date') }}" style="min-width: 130px;">
                        </div>

                        <button class="btn-outline" onclick="resetFilters()">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab">
                    Records ({{ $financials->total() }})
                </button>
            </li>
        </ul>

        <!-- Table -->
        <div class="table-container">
            @if($financials->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">
                                <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                            </th>
                            <th>Activity</th>
                            <th>User</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Transaction Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($financials as $financial)
                        <tr data-id="{{ $financial->activity_financial_id }}"
                            data-amount="{{ $financial->amount }}"
                            data-payment-status="{{ $financial->payment_status }}"
                            data-tx-date="{{ $financial->tx_date }}"
                            data-notes="{{ $financial->notes }}"
                            data-financial-data='{{ json_encode($financial->financial_data) }}'>
                            <td>
                                <input class="form-check-input financial-checkbox" type="checkbox" value="{{ $financial->activity_financial_id }}">
                            </td>
                            <td>
                                @if($financial->activity)
                                    <strong>{{ $financial->activity->title ?? $financial->activity->activity_title_en ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $financial->activity->start_date ? date('d M Y', strtotime($financial->activity->start_date)) : 'No date' }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($financial->user)
                                    <strong>{{ $financial->user->first_name }} {{ $financial->user->last_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $financial->user->email }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-hospital">Medical - Hospital</span>
                            </td>
                            <td>
                                <strong class="amount-display">${{ number_format($financial->amount, 2) }}</strong>
                            </td>
                            <td>
                                @php
                                    $statusColor = match($financial->payment_status) {
                                        'paid'    => 'success',
                                        'pending' => 'warning',
                                        'partial' => 'info',
                                        'overdue' => 'danger',
                                        default   => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }} status-badge">
                                    {{ ucfirst($financial->payment_status ?? 'N/A') }}
                                </span>
                            </td>
                            <td>
                                <span class="tx-date-display">{{ $financial->tx_date ? date('d M Y', strtotime($financial->tx_date)) : '-' }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn-details" onclick="showDetails('{{ $financial->activity_financial_id }}')">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination Footer -->
                <div class="pagination-container">
                    <div class="pagination-info">
                        Showing {{ $financials->firstItem() }} to {{ $financials->lastItem() }} of {{ $financials->total() }} entries
                    </div>

                    <div class="pagination-numbers">
                        @if($financials->onFirstPage())
                            <button class="pagination-nav-button" disabled title="First Page"><i class="fas fa-angle-double-left"></i></button>
                        @else
                            <a href="{{ $financials->appends(request()->query())->url(1) }}" class="pagination-nav-button" title="First Page"><i class="fas fa-angle-double-left"></i></a>
                        @endif

                        @if($financials->onFirstPage())
                            <button class="pagination-nav-button" disabled title="Previous Page"><i class="fas fa-chevron-left"></i></button>
                        @else
                            <a href="{{ $financials->appends(request()->query())->previousPageUrl() }}" class="pagination-nav-button" title="Previous Page"><i class="fas fa-chevron-left"></i></a>
                        @endif

                        @php
                            $currentPage = $financials->currentPage();
                            $lastPage    = $financials->lastPage();
                            $start       = max(1, $currentPage - 2);
                            $end         = min($lastPage, $currentPage + 2);
                            if ($start > 1) {
                                echo '<a href="' . $financials->appends(request()->query())->url(1) . '" class="pagination-button">1</a>';
                                if ($start > 2) echo '<span class="pagination-ellipsis">...</span>';
                            }
                            for ($i = $start; $i <= $end; $i++) {
                                if ($i == $currentPage) {
                                    echo '<span class="pagination-button active">' . $i . '</span>';
                                } else {
                                    echo '<a href="' . $financials->appends(request()->query())->url($i) . '" class="pagination-button">' . $i . '</a>';
                                }
                            }
                            if ($end < $lastPage) {
                                if ($end < $lastPage - 1) echo '<span class="pagination-ellipsis">...</span>';
                                echo '<a href="' . $financials->appends(request()->query())->url($lastPage) . '" class="pagination-button">' . $lastPage . '</a>';
                            }
                        @endphp

                        @if($financials->hasMorePages())
                            <a href="{{ $financials->appends(request()->query())->nextPageUrl() }}" class="pagination-nav-button" title="Next Page"><i class="fas fa-chevron-right"></i></a>
                        @else
                            <button class="pagination-nav-button" disabled title="Next Page"><i class="fas fa-chevron-right"></i></button>
                        @endif

                        @if($financials->hasMorePages())
                            <a href="{{ $financials->appends(request()->query())->url($financials->lastPage()) }}" class="pagination-nav-button" title="Last Page"><i class="fas fa-angle-double-right"></i></a>
                        @else
                            <button class="pagination-nav-button" disabled title="Last Page"><i class="fas fa-angle-double-right"></i></button>
                        @endif
                    </div>

                    <div class="pagination-perpage">
                        <label class="text-muted small me-2">Rows per page:</label>
                        <select class="filter-select" onchange="changePerPage(this.value)">
                            <option value="10"  {{ $financials->perPage() == 10  ? 'selected' : '' }}>10</option>
                            <option value="25"  {{ $financials->perPage() == 25  ? 'selected' : '' }}>25</option>
                            <option value="50"  {{ $financials->perPage() == 50  ? 'selected' : '' }}>50</option>
                            <option value="100" {{ $financials->perPage() == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-hospital empty-state-icon"></i>
                    <h5>No Hospital records found</h5>
                    <p class="text-muted">No hospital financial records available</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Details / Edit Modal --}}
<div class="modal fade" id="financialDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-hospital me-2"></i>Hospital Financial Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="financialDetailsModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="editModeBtn" onclick="enableEditMode()">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button type="button" class="btn btn-success" id="saveModeBtn" onclick="saveEditMode()" style="display: none;">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <button type="button" class="btn btn-secondary" id="cancelModeBtn" onclick="cancelEditMode()" style="display: none;">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentFinancialId   = null;
    let currentFinancialData = null;
    let originalData         = null;
    let isEditMode           = false;

    const activitySearchInput  = document.getElementById('activitySearchInput');
    const userSearchInput      = document.getElementById('userSearchInput');
    const operationTypeFilter  = document.getElementById('operationTypeFilter');
    const paymentStatusFilter  = document.getElementById('paymentStatusFilter');
    const startDateFilter      = document.getElementById('startDateFilter');
    const endDateFilter        = document.getElementById('endDateFilter');

    let searchTimeout;

    function applyFilters() {
        const params = new URLSearchParams();
        if (activitySearchInput?.value)  params.set('activity_search',   activitySearchInput.value);
        if (userSearchInput?.value)      params.set('user_search',        userSearchInput.value);
        if (operationTypeFilter?.value)  params.set('operation_type',     operationTypeFilter.value);
        if (paymentStatusFilter?.value)  params.set('payment_status',     paymentStatusFilter.value);
        if (startDateFilter?.value)      params.set('start_date',         startDateFilter.value);
        if (endDateFilter?.value)        params.set('end_date',           endDateFilter.value);
        window.location.href = '{{ route("financials.medical.hospital") }}?' + params.toString();
    }

    function resetFilters() {
        window.location.href = '{{ route("financials.medical.hospital") }}';
    }

    function changePerPage(value) {
        const params = new URLSearchParams(window.location.search);
        params.set('per_page', value);
        window.location.href = '{{ route("financials.medical.hospital") }}?' + params.toString();
    }

    function formatLabel(label) {
        return label
            .replace(/_/g, ' ')
            .replace(/([A-Z])/g, ' $1')
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
            .join(' ');
    }

    function formatValue(value, key) {
        if (typeof value === 'number') {
            if (key.includes('percentage') || key.includes('percent')) return value + '%';
            if (key.includes('cost') || key.includes('fee') || key.includes('amount') || key.includes('allowance'))
                return '$' + value.toLocaleString();
            return value.toLocaleString();
        }
        if (key.includes('date') && value) return new Date(value).toLocaleDateString();
        return value;
    }

    function showDetails(financialId) {
        currentFinancialId = financialId;
        const row = document.querySelector(`tr[data-id="${financialId}"]`);
        if (!row) return;

        try {
            currentFinancialData = JSON.parse(row.getAttribute('data-financial-data') || '{}');
        } catch (e) {
            currentFinancialData = {};
        }

        originalData = {
            amount:         row.getAttribute('data-amount'),
            payment_status: row.getAttribute('data-payment-status'),
            tx_date:        row.getAttribute('data-tx-date'),
            notes:          row.getAttribute('data-notes'),
            financial_data: JSON.parse(JSON.stringify(currentFinancialData))
        };

        renderViewMode();
        isEditMode = false;
        document.getElementById('editModeBtn').style.display   = 'inline-flex';
        document.getElementById('saveModeBtn').style.display   = 'none';
        document.getElementById('cancelModeBtn').style.display = 'none';

        new bootstrap.Modal(document.getElementById('financialDetailsModal')).show();
    }

    function renderViewMode() {
        const modalBody     = document.getElementById('financialDetailsModalBody');
        const row           = document.querySelector(`tr[data-id="${currentFinancialId}"]`);
        const amount        = row?.getAttribute('data-amount')         || originalData.amount;
        const paymentStatus = row?.getAttribute('data-payment-status') || originalData.payment_status;
        const txDate        = row?.getAttribute('data-tx-date')        || originalData.tx_date;
        const notes         = row?.getAttribute('data-notes')          || originalData.notes;

        let html = `
            <div class="details-section">
                <div class="details-section-title">
                    <i class="fas fa-hospital me-2"></i>Hospital Financial Details
                </div>
                <div class="details-grid">
                    <div class="details-item">
                        <span class="details-label">Amount:</span>
                        <span class="details-value">$${parseFloat(amount).toLocaleString()}</span>
                    </div>
                    <div class="details-item">
                        <span class="details-label">Payment Status:</span>
                        <span class="details-value highlight">${paymentStatus ? paymentStatus.charAt(0).toUpperCase() + paymentStatus.slice(1) : 'N/A'}</span>
                    </div>
                    <div class="details-item">
                        <span class="details-label">Transaction Date:</span>
                        <span class="details-value">${txDate ? new Date(txDate).toLocaleDateString() : '-'}</span>
                    </div>
        `;

        for (const [key, value] of Object.entries(currentFinancialData)) {
            if (value !== null && value !== '' && value !== undefined) {
                const isHighlight = key === 'operation_type' || key === 'medication_type';
                html += `
                    <div class="details-item" data-key="${key}">
                        <span class="details-label">${formatLabel(key)}:</span>
                        <span class="details-value ${isHighlight ? 'highlight' : ''}">${formatValue(value, key)}</span>
                    </div>
                `;
            }
        }

        if (notes && notes !== 'null') {
            html += `
                <div class="details-item">
                    <span class="details-label">Notes:</span>
                    <span class="details-value">${notes}</span>
                </div>
            `;
        }

        html += `</div></div>`;
        modalBody.innerHTML = html;
    }

    function enableEditMode() {
        isEditMode = true;
        const row           = document.querySelector(`tr[data-id="${currentFinancialId}"]`);
        const currentAmount        = row?.getAttribute('data-amount')         || originalData.amount;
        const currentPaymentStatus = row?.getAttribute('data-payment-status') || originalData.payment_status;
        const currentTxDate        = row?.getAttribute('data-tx-date')        || originalData.tx_date;
        const currentNotes         = row?.getAttribute('data-notes')          || originalData.notes;

        let html = `
            <div class="details-section">
                <div class="details-section-title">
                    <i class="fas fa-edit me-2"></i>Edit Hospital Financial Details
                </div>
                <div class="details-grid">
                    <div class="details-item">
                        <span class="details-label">Amount:</span>
                        <input type="number" class="edit-input" id="edit_amount" value="${currentAmount}" step="0.01">
                    </div>
                    <div class="details-item">
                        <span class="details-label">Payment Status:</span>
                        <select class="edit-select" id="edit_payment_status">
                            <option value="pending" ${currentPaymentStatus === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="partial" ${currentPaymentStatus === 'partial' ? 'selected' : ''}>Partial</option>
                            <option value="paid"    ${currentPaymentStatus === 'paid'    ? 'selected' : ''}>Paid</option>
                            <option value="overdue" ${currentPaymentStatus === 'overdue' ? 'selected' : ''}>Overdue</option>
                        </select>
                    </div>
                    <div class="details-item">
                        <span class="details-label">Transaction Date:</span>
                        <input type="date" class="edit-input" id="edit_tx_date" value="${currentTxDate || ''}">
                    </div>
        `;

        for (const [key, value] of Object.entries(currentFinancialData)) {
            html += `
                <div class="details-item">
                    <span class="details-label">${formatLabel(key)}:</span>
                    <input type="text" class="edit-input" id="edit_${key}" value="${value ?? ''}">
                </div>
            `;
        }

        html += `
                    <div class="details-item">
                        <span class="details-label">Notes:</span>
                        <textarea class="edit-input" id="edit_notes" rows="2">${currentNotes || ''}</textarea>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('financialDetailsModalBody').innerHTML = html;
        document.getElementById('editModeBtn').style.display   = 'none';
        document.getElementById('saveModeBtn').style.display   = 'inline-flex';
        document.getElementById('cancelModeBtn').style.display = 'inline-flex';
    }

    function cancelEditMode() {
        renderViewMode();
        isEditMode = false;
        document.getElementById('editModeBtn').style.display   = 'inline-flex';
        document.getElementById('saveModeBtn').style.display   = 'none';
        document.getElementById('cancelModeBtn').style.display = 'none';
    }

    function saveEditMode() {
        const updatedAmount        = document.getElementById('edit_amount')?.value;
        const updatedPaymentStatus = document.getElementById('edit_payment_status')?.value;
        const updatedTxDate        = document.getElementById('edit_tx_date')?.value;
        const updatedNotes         = document.getElementById('edit_notes')?.value;

        const updatedFinancialData = {};
        for (const key of Object.keys(currentFinancialData)) {
            const input = document.getElementById(`edit_${key}`);
            if (input) updatedFinancialData[key] = input.value;
        }

        // Preserve medication_type = 'hospital'
        updatedFinancialData.medication_type = 'hospital';

        fetch(`/financials/${currentFinancialId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                amount: updatedAmount,
                payment_status: updatedPaymentStatus,
                tx_date: updatedTxDate,
                notes: updatedNotes,
                financial_data: updatedFinancialData,
                _method: 'PUT'
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-id="${currentFinancialId}"]`);
                if (row) {
                    row.setAttribute('data-amount',         updatedAmount);
                    row.setAttribute('data-payment-status', updatedPaymentStatus);
                    row.setAttribute('data-tx-date',        updatedTxDate);
                    row.setAttribute('data-notes',          updatedNotes);
                    row.setAttribute('data-financial-data', JSON.stringify(updatedFinancialData));

                    const amountDisplay = row.querySelector('.amount-display');
                    if (amountDisplay) amountDisplay.textContent = '$' + parseFloat(updatedAmount).toLocaleString();

                    const statusBadge = row.querySelector('.status-badge');
                    if (statusBadge) {
                        const sc = updatedPaymentStatus === 'paid' ? 'success' : (updatedPaymentStatus === 'pending' ? 'warning' : (updatedPaymentStatus === 'partial' ? 'info' : 'danger'));
                        statusBadge.className   = `badge bg-${sc} status-badge`;
                        statusBadge.textContent = updatedPaymentStatus.charAt(0).toUpperCase() + updatedPaymentStatus.slice(1);
                    }

                    const txDateDisplay = row.querySelector('.tx-date-display');
                    if (txDateDisplay && updatedTxDate)
                        txDateDisplay.textContent = new Date(updatedTxDate).toLocaleDateString();
                }

                currentFinancialData = updatedFinancialData;
                originalData = {
                    amount: updatedAmount, payment_status: updatedPaymentStatus,
                    tx_date: updatedTxDate, notes: updatedNotes,
                    financial_data: JSON.parse(JSON.stringify(updatedFinancialData))
                };

                renderViewMode();
                isEditMode = false;
                document.getElementById('editModeBtn').style.display   = 'inline-flex';
                document.getElementById('saveModeBtn').style.display   = 'none';
                document.getElementById('cancelModeBtn').style.display = 'none';

                showToast('success', 'Hospital record updated successfully!');
            } else {
                showToast('error', data.message || 'Error updating record');
            }
        })
        .catch(() => showToast('error', 'Failed to update hospital record'));
    }

    function showToast(type, message) {
        const toastHtml = `
            <div class="toast bg-${type === 'success' ? 'success' : 'danger'} text-white border-0 fade show" role="alert">
                <div class="d-flex align-items-center">
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                        <div>
                            <strong>${type === 'success' ? 'Success' : 'Error'}</strong>
                            <div class="small">${message}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        const container = document.querySelector('.toast-container');
        if (container) {
            container.insertAdjacentHTML('beforeend', toastHtml);
            setTimeout(() => container.lastElementChild?.remove(), 5000);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (activitySearchInput) activitySearchInput.addEventListener('keyup', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });
        if (userSearchInput) userSearchInput.addEventListener('keyup', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });
        if (operationTypeFilter) operationTypeFilter.addEventListener('change', applyFilters);
        if (paymentStatusFilter)  paymentStatusFilter.addEventListener('change', applyFilters);
        if (startDateFilter)      startDateFilter.addEventListener('change', applyFilters);
        if (endDateFilter)        endDateFilter.addEventListener('change', applyFilters);

        document.querySelectorAll('.toast').forEach(t => setTimeout(() => t.remove(), 5000));

        // Bulk selection
        const selectAll             = document.getElementById('selectAllCheckbox');
        const checkboxes            = document.querySelectorAll('.financial-checkbox');
        const bulkSection           = document.getElementById('bulkActionsSection');
        const selectedCountSpan     = document.getElementById('selectedCount');
        const clearBtn              = document.getElementById('clearSelectionBtn');
        const selectedIdsContainer  = document.getElementById('selectedFinancialIds');

        function updateBulkUI() {
            const checked = Array.from(checkboxes).filter(cb => cb.checked);
            const count   = checked.length;

            if (selectedCountSpan) selectedCountSpan.textContent = count + ' selected';

            if (count > 0) {
                if (bulkSection) bulkSection.style.display = 'block';
                if (selectedIdsContainer) {
                    selectedIdsContainer.innerHTML = '';
                    checked.forEach(cb => {
                        const input = document.createElement('input');
                        input.type  = 'hidden';
                        input.name  = 'financial_ids[]';
                        input.value = cb.value;
                        selectedIdsContainer.appendChild(input);
                    });
                }
            } else {
                if (bulkSection) bulkSection.style.display = 'none';
                if (selectedIdsContainer) selectedIdsContainer.innerHTML = '';
            }

            if (selectAll) {
                if (count === 0)                   { selectAll.checked = false; selectAll.indeterminate = false; }
                else if (count === checkboxes.length) { selectAll.checked = true;  selectAll.indeterminate = false; }
                else                               { selectAll.checked = false; selectAll.indeterminate = true;  }
            }

            checkboxes.forEach(cb => {
                const r = cb.closest('tr');
                if (r) cb.checked ? r.classList.add('selected') : r.classList.remove('selected');
            });
        }

        if (checkboxes.length > 0) {
            checkboxes.forEach(cb => cb.addEventListener('change', updateBulkUI));
            if (selectAll) selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkUI();
            });
            if (clearBtn) clearBtn.addEventListener('click', function () {
                checkboxes.forEach(cb => cb.checked = false);
                updateBulkUI();
            });
            updateBulkUI();
        }
    });
</script>
@endsection
