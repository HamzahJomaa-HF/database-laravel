{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                    <h1 class="h2 fw-bold mb-1">Dashboard Overview</h1>
                    <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar-alt me-1"></i> Last 30 Days
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Today</a></li>
                            <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                            <li><a class="dropdown-item" href="#">Last 30 Days</a></li>
                            <li><a class="dropdown-item" href="#">This Year</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add New
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Total Participants
                            </div>
                            <div class="h5 mb-0 fw-bold">1,248</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2">
                                    <i class="fas fa-arrow-up me-1"></i>12%
                                </span>
                                <span>Since last month</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-primary opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Active Users
                            </div>
                            <div class="h5 mb-0 fw-bold">42</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2">
                                    <i class="fas fa-arrow-up me-1"></i>5%
                                </span>
                                <span>Since last week</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-success opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Pending Actions
                            </div>
                            <div class="h5 mb-0 fw-bold">18</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-danger me-2">
                                    <i class="fas fa-arrow-down me-1"></i>3%
                                </span>
                                <span>Since yesterday</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tasks fa-2x text-warning opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Data Accuracy
                            </div>
                            <div class="h5 mb-0 fw-bold">98.5%</div>
                            <div class="mt-2 mb-0 text-muted text-xs">
                                <span class="text-success me-2">
                                    <i class="fas fa-arrow-up me-1"></i>2.5%
                                </span>
                                <span>Completion rate</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-info opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts & Graphs --}}
    <div class="row mb-4">
        <div class="col-xl-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-bar me-2"></i>Participants Overview
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Monthly
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Daily</a></li>
                            <li><a class="dropdown-item" href="#">Weekly</a></li>
                            <li><a class="dropdown-item" href="#">Monthly</a></li>
                            <li><a class="dropdown-item" href="#">Yearly</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        {{-- Chart placeholder --}}
                        <div class="d-flex justify-content-center align-items-center h-100">
                            <div class="text-center">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Chart visualization will appear here</p>
                                <small>Integration with Chart.js or ApexCharts required</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-users me-2"></i>User Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Administrators</span>
                            <span class="fw-bold">5</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 12%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Managers</span>
                            <span class="fw-bold">12</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 28%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Viewers</span>
                            <span class="fw-bold">25</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 60%"></div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="h4 mb-0 fw-bold">42</div>
                                <small class="text-muted">Total Users</small>
                            </div>
                            <div class="col-4">
                                <div class="h4 mb-0 fw-bold">89%</div>
                                <small class="text-muted">Active</small>
                            </div>
                            <div class="col-4">
                                <div class="h4 mb-0 fw-bold">11</div>
                                <small class="text-muted">New This Month</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity & Quick Actions --}}
    <div class="row">
        {{-- Recent Activity --}}
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-history me-2"></i>Recent Activity
                    </h6>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">New Participant Added</h6>
                                        <small class="text-muted">2 min ago</small>
                                    </div>
                                    <p class="mb-0 text-muted small">John Doe was added to the system</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-success bg-opacity-10 text-success rounded-circle">
                                        <i class="fas fa-file-export"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">Data Export Completed</h6>
                                        <small class="text-muted">1 hour ago</small>
                                    </div>
                                    <p class="mb-0 text-muted small">Monthly report exported successfully</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-warning bg-opacity-10 text-warning rounded-circle">
                                        <i class="fas fa-user-edit"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">Profile Updated</h6>
                                        <small class="text-muted">3 hours ago</small>
                                    </div>
                                    <p class="mb-0 text-muted small">Jane Smith updated her profile information</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="list-group-item px-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-info bg-opacity-10 text-info rounded-circle">
                                        <i class="fas fa-database"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">System Backup</h6>
                                        <small class="text-muted">Yesterday</small>
                                    </div>
                                    <p class="mb-0 text-muted small">Automatic database backup completed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-xl-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('users.create') }}" class="card card-hover text-decoration-none h-100">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <div class="avatar avatar-lg bg-primary bg-opacity-10 text-primary rounded-circle">
                                            <i class="fas fa-user-plus fa-2x"></i>
                                        </div>
                                    </div>
                                    <h6 class="mb-2">Add Participant</h6>
                                    <p class="text-muted small mb-0">Add new foundation participant</p>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-md-6">
                            <a href="{{ route('users.import.form') }}" class="card card-hover text-decoration-none h-100">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <div class="avatar avatar-lg bg-success bg-opacity-10 text-success rounded-circle">
                                            <i class="fas fa-file-import fa-2x"></i>
                                        </div>
                                    </div>
                                    <h6 class="mb-2">Import Data</h6>
                                    <p class="text-muted small mb-0">Bulk import participants data</p>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-md-6">
                            <a href="{{ route('users.export.excel') }}" class="card card-hover text-decoration-none h-100">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <div class="avatar avatar-lg bg-warning bg-opacity-10 text-warning rounded-circle">
                                            <i class="fas fa-file-export fa-2x"></i>
                                        </div>
                                    </div>
                                    <h6 class="mb-2">Export Report</h6>
                                    <p class="text-muted small mb-0">Export data to Excel</p>
                                </div>
                            </a>
                        </div>
                        
                        <div class="col-md-6">
                            <a href="{{ route('users.statistics') }}" class="card card-hover text-decoration-none h-100">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <div class="avatar avatar-lg bg-info bg-opacity-10 text-info rounded-circle">
                                            <i class="fas fa-chart-pie fa-2x"></i>
                                        </div>
                                    </div>
                                    <h6 class="mb-2">View Statistics</h6>
                                    <p class="text-muted small mb-0">Analytics & insights</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- System Status --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-server me-2"></i>System Status
                    </h6>
                    <span class="badge bg-success">
                        <i class="fas fa-circle me-1"></i>All Systems Operational
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-success bg-opacity-10 text-success rounded-circle">
                                        <i class="fas fa-database"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Database</h6>
                                    <small class="text-muted">Online</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-success bg-opacity-10 text-success rounded-circle">
                                        <i class="fas fa-cloud"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Storage</h6>
                                    <small class="text-muted">2.4 GB / 10 GB</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-success bg-opacity-10 text-success rounded-circle">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Security</h6>
                                    <small class="text-muted">Protected</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm bg-success bg-opacity-10 text-success rounded-circle">
                                        <i class="fas fa-sync"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">Last Backup</h6>
                                    <small class="text-muted">12 hours ago</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #dee2e6;
    }
    
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        border-color: #4361ee;
    }
    
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }
    
    .avatar-sm {
        width: 36px;
        height: 36px;
    }
    
    .avatar-lg {
        width: 60px;
        height: 60px;
    }
    
    .border-start {
        border-left-width: 4px !important;
    }
    
    .progress {
        border-radius: 4px;
    }
    
    .list-group-item {
        border-left: 0;
        border-right: 0;
    }
    
    .list-group-item:first-child {
        border-top: 0;
    }
    
    .list-group-item:last-child {
        border-bottom: 0;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any dashboard-specific JavaScript here
        console.log('Dashboard loaded for user: {{ auth()->user()->name }}');
        
        // Example: Auto-refresh stats every 60 seconds
        // setInterval(function() {
        //     fetch('/api/dashboard/stats')
        //         .then(response => response.json())
        //         .then(data => {
        //             // Update stats on the page
        //             document.querySelector('.total-participants').textContent = data.totalParticipants;
        //         });
        // }, 60000);
    });
</script>
@endsection