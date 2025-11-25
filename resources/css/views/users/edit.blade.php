@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0"><i class="bi bi-person-fill-gear me-2"></i>Edit User: {{ $user->first_name }} {{ $user->last_name }}</h3>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Validation Errors</h5>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('users.update', $user->user_id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    {{-- Personal Information Group --}}
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3 text-primary"><i class="bi bi-info-circle me-1"></i>Personal Information</h5>
                    </div>
                    <div class="col-md-4">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                               value="{{ old('first_name', $user->first_name) }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="middle_name" class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" id="middle_name" class="form-control @error('middle_name') is-invalid @enderror" 
                               value="{{ old('middle_name', $user->middle_name) }}">
                        @error('middle_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                               value="{{ old('last_name', $user->last_name) }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="mother_name" class="form-label">Mother Name</label>
                        <input type="text" name="mother_name" id="mother_name" class="form-control @error('mother_name') is-invalid @enderror" 
                               value="{{ old('mother_name', $user->mother_name) }}">
                        @error('mother_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="gender" class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="">Select Gender</option>
                            <option value="Male" {{ old('gender', $user->gender)=='Male' ? 'selected':'' }}>Male</option>
                            <option value="Female" {{ old('gender', $user->gender)=='Female' ? 'selected':'' }}>Female</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" name="dob" id="dob" class="form-control @error('dob') is-invalid @enderror" 
                               value="{{ old('dob', $user->dob?->format('Y-m-d')) }}">
                        @error('dob')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                               value="{{ old('phone_number', $user->phone_number) }}">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status Group --}}
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3 mt-3 text-primary"><i class="bi bi-briefcase me-1"></i>Status Details</h5>
                    </div>
                    <div class="col-md-4">
                        <label for="marital_status" class="form-label">Marital Status</label>
                        <input type="text" name="marital_status" id="marital_status" class="form-control @error('marital_status') is-invalid @enderror" 
                               value="{{ old('marital_status', $user->marital_status) }}">
                        @error('marital_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="employment_status" class="form-label">Employment Status</label>
                        <input type="text" name="employment_status" id="employment_status" class="form-control @error('employment_status') is-invalid @enderror" 
                               value="{{ old('employment_status', $user->employment_status) }}">
                        @error('employment_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="type" class="form-label">User Type</label>
                        <input type="text" name="type" id="type" class="form-control @error('type') is-invalid @enderror" 
                               value="{{ old('type', $user->type) }}">
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Identification Group --}}
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3 mt-3 text-primary"><i class="bi bi-fingerprint me-1"></i>Identification Details</h5>
                    </div>
                    <div class="col-md-4">
                        <label for="identification_id" class="form-label">Identification ID</label>
                        <input type="text" name="identification_id" id="identification_id" class="form-control @error('identification_id') is-invalid @enderror" 
                               value="{{ old('identification_id', $user->identification_id) }}">
                        @error('identification_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="passport_number" class="form-label">Passport Number</label>
                        <input type="text" name="passport_number" id="passport_number" class="form-control @error('passport_number') is-invalid @enderror" 
                               value="{{ old('passport_number', $user->passport_number) }}">
                        @error('passport_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="register_number" class="form-label">Register Number</label>
                        <input type="text" name="register_number" id="register_number" class="form-control @error('register_number') is-invalid @enderror" 
                               value="{{ old('register_number', $user->register_number) }}">
                        @error('register_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="register_place" class="form-label">Register Place</label>
                        <input type="text" name="register_place" id="register_place" class="form-control @error('register_place') is-invalid @enderror" 
                               value="{{ old('register_place', $user->register_place) }}">
                        @error('register_place')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-arrow-clockwise me-1"></i> Update User
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Enable Bootstrap client-side form validation
    (function () {
      'use strict'
      var forms = document.querySelectorAll('.needs-validation')
      Array.prototype.slice.call(forms)
        .forEach(function (form) {
          form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }
            form.classList.add('was-validated')
          }, false)
        })
    })()
</script>
@endsection