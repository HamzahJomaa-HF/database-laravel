@extends('layouts.app')

@section('title', 'Portfolios Management')

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
            background-color: var(--primary-color);
            color: white;
        }
        
        .bg-secondary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .bg-success {
            background-color: #10b981;
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
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8;
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
        }
        
        .btn-outline:hover {
            background-color: #eff6ff;
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
        }
        
        .btn-outline-primary:hover {
            background-color: #eff6ff;
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
        }
        
        .btn-outline-secondary:hover {
            background-color: #f3f4f6;
        }
        
        .filters-container {
            display: flex;
            gap: 1rem;
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
        
        .cops-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
            max-width: 300px;
        }
        
        .cop-badge {
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 0.125rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            border: 1px solid #bae6fd;
        }
        
        .more-cops {
            background-color: #f3f4f6;
            color: #6b7280;
            padding: 0.125rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            border: 1px solid #e5e7eb;
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
            <h4 class="page-title">Portfolios</h4>
            <div class="action-buttons">

   <a href="{{ route('cops.index') }}" class="btn-outline">

      <i class="fas fa-building"></i> COPs
                </a>
                <a href="{{ route('portfolios.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Add Portfolio
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
                               placeholder="Search by portfolio name or description..." 
                               value="{{ request('search') }}"
                               id="searchInput">
                            <path d="M21.53 20.47l-3.66-3.66A8.98 8.98 0 0 0 20 11a9 9 0 1 0-9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66a.75.75 0 1 0 1.06-1.06zM3.5 11a7.5 7.5 0 1 1 15 0 7.5 7.5 0 0 1-15 0z"></path>
                        </svg>
                    </div>
                    
                    <div class="filters-container">
                        <select class="filter-select" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="Active Citizenship" {{ request('type') == 'Active Citizenship' ? 'selected' : '' }}>Active Citizenship</option>
                            <option value="Education & Digital Transformation" {{ request('type') == 'Education & Digital Transformation' ? 'selected' : '' }}>Education & Digital Transformation</option>
                            <option value="Public Health & Wellbeing" {{ request('type') == 'Public Health & Wellbeing' ? 'selected' : '' }}>Public Health & Wellbeing</option>
                            <option value="Sustainability & Climate Action" {{ request('type') == 'Sustainability & Climate Action' ? 'selected' : '' }}>Sustainability & Climate Action</option>
                            <option value="Socio-economic Development" {{ request('type') == 'Socio-economic Development' ? 'selected' : '' }}>Socio-economic Development</option>
                            <option value="Regional" {{ request('type') == 'Regional' ? 'selected' : '' }}>Regional</option>
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
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#portfolios-tab">
                    Portfolios ({{ $portfolios->total() }})
                </button>
            </li>
        </ul>

        <!-- Table -->
        <div class="scrollable-grid mt-4">
            <div class="table-container">
                @if($portfolios->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Portfolio Name</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Associated COPs</th>
                                <th>External ID</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($portfolios as $portfolio)
                            <tr class="{{ $loop->even ? 'table-row-alt' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        
                                        <div>
                                            <div class="fw-medium">{{ $portfolio->name }}</div>
                                            <div class="text-muted small">{{ $portfolio->portfolio_id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($portfolio->type)
                                        <span class="badge bg-primary">{{ $portfolio->type }}</span>
                                    @else
                                        <span class="badge bg-secondary">No Type</span>
                                    @endif
                                </td>
                                <td>
                                    @if($portfolio->description)
                                        {{ Str::limit($portfolio->description, 50) }}
                                    @else
                                        <span class="text-muted">No description</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="cops-badges">
                                        @foreach($portfolio->cops->take(3) as $cop)
                                            <span class="cop-badge">{{ $cop->cop_name }}</span>
                                        @endforeach
                                        @if($portfolio->cops->count() > 3)
                                            <span class="more-cops">+{{ $portfolio->cops->count() - 3 }} more</span>
                                        @endif
                                        @if($portfolio->cops->count() == 0)
                                            <span class="text-muted small">No COPs</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $portfolio->external_id ?? '-' }}</td>
                                <td>{{ $portfolio->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                    
                                        <button class="btn btn-sm btn-outline-secondary"
                                                onclick="editPortfolio('{{ $portfolio->portfolio_id }}')"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('portfolios.destroy', $portfolio->portfolio_id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this portfolio?');">
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
                        <i class="fas fa-folder-open empty-state-icon"></i>
                        <h5 class="mb-2">No portfolios found</h5>
                        <p class="text-muted">Add your first portfolio to get started</p>
                        <a href="{{ route('portfolios.create') }}" class="btn-primary mt-3">
                            <i class="fas fa-plus"></i> Add Portfolio
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if($portfolios->hasPages())
        <div class="pagination-container">
            <div class="pagination-numbers">
                @if($portfolios->onFirstPage())
                    <button class="pagination-nav-button" disabled>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @else
                    <a href="{{ $portfolios->previousPageUrl() }}" class="pagination-nav-button">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif
                
                @foreach(range(1, min(5, $portfolios->lastPage())) as $page)
                    <a href="{{ $portfolios->url($page) }}" 
                       class="pagination-button {{ $portfolios->currentPage() == $page ? 'active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach
                
                @if($portfolios->hasMorePages())
                    <a href="{{ $portfolios->nextPageUrl() }}" class="pagination-nav-button">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <button class="pagination-nav-button" disabled>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                @endif
            </div>
            
            <div class="pagination-info">
                Showing {{ $portfolios->firstItem() }} to {{ $portfolios->lastItem() }} of {{ $portfolios->total() }} entries
            </div>
            
            <div>
                <select class="filter-select" onchange="changePerPage(this.value)">
                    <option value="10" {{ $portfolios->perPage() == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $portfolios->perPage() == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $portfolios->perPage() == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $portfolios->perPage() == 100 ? 'selected' : '' }}>100</option>
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
        const typeFilter = document.getElementById('typeFilter');
        
        let searchTimeout;
        
        searchInput.addEventListener('keyup', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 500);
        });
        
        typeFilter.addEventListener('change', applyFilters);
        
        function applyFilters() {
            const params = new URLSearchParams();
            
            if (searchInput.value) {
                params.set('search', searchInput.value);
            }
            
            if (typeFilter.value) {
                params.set('type', typeFilter.value);
            }
            
            window.location.href = '{{ route("portfolios.index") }}?' + params.toString();
        }
        
        function resetFilters() {
            window.location.href = '{{ route("portfolios.index") }}';
        }
        
        function changePerPage(value) {
            const params = new URLSearchParams(window.location.search);
            params.set('per_page', value);
            window.location.href = '{{ route("portfolios.index") }}?' + params.toString();
        }
        
        function viewPortfolio(portfolioId) {
            window.location.href = `/portfolios/${portfolioId}`;
        }
        
        function editPortfolio(portfolioId) {
            window.location.href = `/portfolios/${portfolioId}/edit`;
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