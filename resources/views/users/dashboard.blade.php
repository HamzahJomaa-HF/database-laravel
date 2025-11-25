@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-5">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            <span class="text-muted small">HOME / DASHBOARD</span>
            <h1 class="fw-bold mb-0">Dashboard</h1>
        </div>
        <button class="btn btn-success shadow-sm"><i class="bi bi-file-earmark-bar-graph me-1"></i> New Report</button>
    </div>

    {{-- Top Row Metrics --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title fw-bold text-dark mb-0">Total Users</h5>
                        <a href="{{ route('users.index') }}" class="small text-decoration-none text-muted">View</a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="display-5 fw-bold mb-0 text-dark">{{ number_format($totalUsers ?? 1845) }}</h2>
                        <span class="text-success small fw-bold">2.6% <i class="bi bi-arrow-up"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark mb-0">Active Rate</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="display-5 fw-bold mb-0 text-dark">{{ number_format($activeRate ?? 5.43,2) }}%</h2>
                        <span class="text-danger small fw-bold">9.6% <i class="bi bi-arrow-down"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark mb-0">Gender Distribution</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="display-5 fw-bold mb-0 text-dark">{{ number_format($femalePercentage ?? 48.2, 2) }}%</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
