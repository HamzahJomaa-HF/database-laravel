@extends('layouts.app')

@section('title', '403 - Access Forbidden')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 60vh;">
    <div class="text-center" style="max-width: 480px;">

        <div class="mb-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 mb-3"
                 style="width: 96px; height: 96px;">
                <i class="bi bi-shield-lock-fill text-danger" style="font-size: 2.75rem;"></i>
            </div>
            <h1 class="fw-bold mb-1" style="font-size: 5rem; color: #1a237e; line-height: 1;">403</h1>
            <h4 class="fw-semibold text-dark mb-2">Access Forbidden</h4>
            <p class="text-muted mb-0">
                {{ $message ?? 'You do not have permission to access this page.' }}
            </p>
            
        </div>

        <div class="d-flex gap-2 justify-content-center flex-wrap">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/dashboard') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Go Back
            </a>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                <i class="bi bi-house me-1"></i> Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
