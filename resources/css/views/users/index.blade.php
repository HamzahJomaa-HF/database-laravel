@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-people-fill me-2"></i>User Directory</h2>
        <a href="{{ route('users.create') }}" class="btn btn-success">
            <i class="bi bi-person-plus-fill me-1"></i> Add New User
        </a>
    </div>

    {{-- Advanced Search/Filter Form --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-dark">
                <i class="bi bi-funnel me-1"></i> Search & Filter
            </h5>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilter" aria-expanded="false" aria-controls="advancedFilter">
                <i class="bi bi-sliders me-1"></i> Advanced Options
            </button>
        </div>
        
        <div class="collapse {{ request()->except('page') ? 'show' : '' }}" id="advancedFilter">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="name" class="form-label text-muted small">Name (First/Last)</label>
                        <input type="text" name="name" id="name" value="{{ request('name') }}" class="form-control" placeholder="Name">
                    </div>
                    <div class="col-md-2">
                        <label for="gender" class="form-label text-muted small">Gender</label>
                        <select name="gender" id="gender" class="form-select">
                            <option value="">All</option>
                            <option value="Male" {{ request('gender')=='Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ request('gender')=='Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="marital_status" class="form-label text-muted small">Marital Status</label>
                        <select name="marital_status" id="marital_status" class="form-select">
                            <option value="">All</option>
                            <option value="Single" {{ request('marital_status')=='Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ request('marital_status')=='Married' ? 'selected' : '' }}>Married</option>
                            {{-- Add more statuses as needed --}}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="phone_number" class="form-label text-muted small">Phone</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ request('phone_number') }}" class="form-control" placeholder="Phone">
                    </div>
                    <div class="col-md-3 align-self-end">
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary flex-grow-1 me-2">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Users Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Phone</th>
                            <th>DOB</th>
                            <th>Marital</th>
                            <th>Employment</th>
                            <th>Type</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->first_name }} {{ $user->last_name }}</strong> 
                                <span class="d-block text-muted small">{{ $user->middle_name }}</span>
                            </td>
                            <td>{{ $user->gender }}</td>
                            <td>{{ $user->phone_number }}</td>
                            <td>{{ $user->dob?->format('Y-m-d') }}</td>
                            <td>{{ $user->marital_status }}</td>
                            <td>{{ $user->employment_status }}</td>
                            <td><span class="badge bg-info">{{ $user->type ?? 'N/A' }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('users.edit', $user->user_id) }}" class="btn btn-sm btn-primary me-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user->user_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete {{ $user->first_name }} {{ $user->last_name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-exclamation-octagon-fill text-warning me-2"></i> No users found matching your criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

{{-- Toast Notification for Success --}}
@if(session('success'))
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.getElementById('successToast');
        var toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    });
</script>
@endif
@endsection