@extends('layouts.app')

@section('title', 'Programs Management')

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
            min-width: 800px;
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
        
        .bg-secondary {
            background-color: var(--secondary-color);
            color: white;
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
        
        .filters-container {
            display: flex;
            gap: 1rem;
            align-items: center;
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
        
        .small {
            font-size: 0.75rem;
        }
        
       span.program-type-badge {
      display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
    white-space: nowrap;
    background-color: #f3f4f6; /* Changed to match project program badge */
    color: #4b5563; /* Changed to match project program badge */
    border: 1px solid #e5e7eb !important; /* Added border like projects */
}
        .program-type-badge {
    width: 140px !important; /* Exact same width for all badges */
    display: inline-flex !important;
    justify-content: center !important;
    align-items: center !important;
    height: 22px !important; /* Fixed height */
    line-height: 1 !important;
}
        .hierarchy-indent {
            padding-left: 2rem;
        }
        
        .hierarchy-icon {
            color: #9ca3af;
            margin-right: 0.5rem;
        }
        
        .external-id {
            font-size: 0.875rem;
            color: #4b5563;
            font-weight: 500;
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
        }
    </style>
@endsection

@section('content')
<div class="dashboard-content">
    <div class="main-div">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="page-title">Programs Management</h4>
            <div class="action-buttons">
                <a href="{{ route('createCenter') }}" class="btn-outline">
                    <i class="fas fa-building"></i> Create Center
                </a>
                <a href="{{ route('create.flagshiplocal') }}" class="btn-outline">
                    <i class="fas fa-flag"></i> Create Flagship/Local
                </a>
                <a href="{{ route('create.subprogram') }}" class="btn-outline">
                    <i class="fas fa-project-diagram"></i> Create Sub-Program
                </a>
            </div>
        </div>

        <!-- Filtering Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="filtering-bar-container">
                    <div class="search-input-container">
                        <input type="text" 
                               class="search-input" 
                               placeholder="Search by program name..." 
                               value="{{ request('search') }}"
                               id="searchInput">
                        
                    </div>
                    
                    <div class="filters-container">
                        <select class="filter-select" id="programTypeFilter">
                            <option value="">All Program Types</option>
                            <option value="Center" {{ request('program_type') == 'Center' ? 'selected' : '' }}>Center</option>
                            <option value="Flagship" {{ request('program_type') == 'Flagship' ? 'selected' : '' }}>Flagship</option>
                            <option value="Local Program" {{ request('program_type') == 'Local Program' ? 'selected' : '' }}>Local Program</option>
                            <option value="Center Program" {{ request('program_type') == 'Center Program' ? 'selected' : '' }}>Center Program</option>
                            <option value="Sub-Program" {{ request('program_type') == 'Sub-Program' ? 'selected' : '' }}>Sub-Program</option>
                        </select>
                        
                        <button class="btn-outline" onclick="resetFilters()">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Programs Count -->
        <div class="mb-3">
            <span class="text-muted">Total Programs: {{ $programs->total() }}</span>
        </div>

        <!-- Table -->
        <div class="scrollable-grid">
            <div class="table-container">
                @if($programs->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Program Name</th>
                                <th>Program Category</th>
                                <th>External ID</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($programs as $program)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($program->parentProgram)
                                            <i class="fas fa-level-up-alt hierarchy-icon"></i>
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ $program->name }}</div>
                                            @if($program->subPrograms->count() > 0)
                                                <div class="small text-muted">
                                                    <i class="fas fa-sitemap"></i> {{ $program->subPrograms->count() }} sub-program(s)
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <span class="program-type-badge">{{ $program->program_type }}</span>
                                </td>
                                
                                <td>
                                    <span class="external-id">{{ $program->external_id ?? '-' }}</span>
                                </td>
                                
                                <td>
    <div class="d-flex gap-2">
        @if($program->type === 'Center' && $program->program_type === 'Center')
    <a href="{{ route('editCenter', $program->program_id) }}" 
       class="btn-outline-secondary btn-sm"
       title="Edit">
        <i class="fas fa-edit"></i>
    </a>
@elseif(in_array($program->program_type, ['Flagship', 'Local Program']))
    <a href="{{ route('edit.flagshiplocal', $program->program_id) }}" 
       class="btn-outline-secondary btn-sm"
       title="Edit">
        <i class="fas fa-edit"></i>
    </a>
@elseif(in_array($program->program_type, ['Sub-Program', 'Center Program']))
    <a href="{{ route('edit.subprogram', $program->program_id) }}" 
       class="btn-outline-secondary btn-sm"
       title="Edit">
        <i class="fas fa-edit"></i>
    </a>
@else
    <a href="{{ route('editCenter', $program->program_id) }}" 
       class="btn-outline-secondary btn-sm"
       title="Edit">
        <i class="fas fa-edit"></i>
    </a>
@endif
        
        <form action="{{ route('programs.destroy', $program->program_id) }}" 
              method="POST" 
              class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this program? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-outline-danger btn-sm" title="Delete">
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
                        <i class="fas fa-project-diagram empty-state-icon"></i>
                        <h5 class="mb-2">No programs found</h5>
                        <p class="text-muted">Create your first program to get started</p>
                        <div class="d-flex gap-2 justify-content-center mt-3">
                            <a href="{{ route('createCenter') }}" class="btn-primary">
                                <i class="fas fa-building"></i> Create Center
                            </a>
                            <a href="{{ route('create.flagshiplocal') }}" class="btn-outline">
                                <i class="fas fa-flag"></i> Create Flagship/Local
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if($programs->hasPages())
        <div class="pagination-container">
            <div class="pagination-numbers">
                @if($programs->onFirstPage())
                    <button class="pagination-nav-button" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $programs->previousPageUrl() }}" class="pagination-nav-button">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif
                
                @foreach(range(1, min(5, $programs->lastPage())) as $page)
                    <a href="{{ $programs->url($page) }}" 
                       class="pagination-button {{ $programs->currentPage() == $page ? 'active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach
                
                @if($programs->hasMorePages())
                    <a href="{{ $programs->nextPageUrl() }}" class="pagination-nav-button">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <button class="pagination-nav-button" disabled>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                @endif
            </div>
            
            <div class="pagination-info">
                Showing {{ $programs->firstItem() }} to {{ $programs->lastItem() }} of {{ $programs->total() }} entries
            </div>
            
            <div>
                <select class="filter-select" onchange="changePerPage(this.value)">
                    <option value="10" {{ $programs->perPage() == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $programs->perPage() == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $programs->perPage() == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $programs->perPage() == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const programTypeFilter = document.getElementById('programTypeFilter');
        
        let searchTimeout;
        
        searchInput.addEventListener('keyup', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });
        
        programTypeFilter.addEventListener('change', applyFilters);
        
        function applyFilters() {
            const params = new URLSearchParams();
            
            if (searchInput.value) {
                params.set('search', searchInput.value);
            }
            
            if (programTypeFilter.value) {
                params.set('program_type', programTypeFilter.value);
            }
            
            // Keep per_page if exists
            const currentPerPage = new URLSearchParams(window.location.search).get('per_page');
            if (currentPerPage) {
                params.set('per_page', currentPerPage);
            }
            
            window.location.href = '{{ route("programs.index") }}?' + params.toString();
        }
        
        function resetFilters() {
            window.location.href = '{{ route("programs.index") }}';
        }
        
        function changePerPage(value) {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', value);
            window.location.href = '{{ route("programs.index") }}?' + params.toString();
        }
        
        // Initialize page with current filters
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.get('search')) {
                searchInput.value = urlParams.get('search');
            }
        });
    </script>
@endsection