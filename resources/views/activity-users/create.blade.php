{{-- resources/views/activity-users/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Assign User to Activity')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Assign User to Activity</h3>
                    <div class="card-tools">
                        <a href="{{ route('activity-users.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                
                <div class="card-body">
                    <form id="createActivityUserForm" method="POST" action="{{ route('activity-users.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <!-- User Dropdown -->
                                <div class="form-group">
                                    <label for="user_id">User <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('user_id') is-invalid @enderror" 
                                            id="user_id" name="user_id" required style="width: 100%;">
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->user_id }}" {{ old('user_id') == $user->user_id ? 'selected' : '' }}>
                                                {{ $user->first_name }} {{ $user->middle_name ? $user->middle_name . ' ' : '' }}{{ $user->last_name }} 
                                                @if($user->email) ({{ $user->email }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- Activity Dropdown -->
                                <div class="form-group">
                                    <label for="activity_id">Activity <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('activity_id') is-invalid @enderror" 
                                            id="activity_id" name="activity_id" required style="width: 100%;">
                                        <option value="">Select Activity</option>
                                        @foreach($activities as $activity)
                                            <option value="{{ $activity->activity_id }}" {{ old('activity_id') == $activity->activity_id ? 'selected' : '' }}>
                                                {{ $activity->activity_title_en }} - {{ $activity->activity_title_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('activity_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <!-- COP Dropdown -->
                                <div class="form-group">
                                    <label for="cop_id">COP (Community of Practice)</label>
                                    <select class="form-control select2 @error('cop_id') is-invalid @enderror" 
                                            id="cop_id" name="cop_id" style="width: 100%;">
                                        <option value="">Select COP (Optional)</option>
                                        @foreach($cops as $cop)
                                            <option value="{{ $cop->cop_id }}" {{ old('cop_id') == $cop->cop_id ? 'selected' : '' }}>
                                                {{ $cop->cop_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cop_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- Type/Role Field (Optional) -->
                                <div class="form-group">
                                    <label for="type">Role/Type</label>
                                    <input type="text" class="form-control @error('type') is-invalid @enderror" 
                                           id="type" name="type" value="{{ old('type') }}" 
                                           placeholder="e.g., Participant, Speaker, Organizer">
                                    @error('type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Invited Toggle -->
                                <div class="form-group">
                                    <label class="d-block">Invitation Status</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="invited" name="invited" value="1" {{ old('invited') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="invited">User has been invited</label>
                                    </div>
                                    <small class="form-text text-muted">Toggle on if the user has received an invitation</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- Attended Toggle -->
                                <div class="form-group">
                                    <label class="d-block">Attendance Status</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="attended" name="attended" value="1" {{ old('attended') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="attended">User has attended</label>
                                    </div>
                                    <small class="form-text text-muted">Toggle on if the user attended the activity</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Lead Toggle (Optional additional toggle) -->
                                <div class="form-group">
                                    <label class="d-block">Lead Status</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="is_lead" name="is_lead" value="1" {{ old('is_lead') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_lead">User is a lead</label>
                                    </div>
                                    <small class="form-text text-muted">Toggle on if this user is a lead for this activity</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <!-- External ID (Optional) -->
                                <div class="form-group">
                                    <label for="external_id">External ID</label>
                                    <input type="text" class="form-control @error('external_id') is-invalid @enderror" 
                                           id="external_id" name="external_id" value="{{ old('external_id') }}" 
                                           placeholder="External reference ID (optional)">
                                    @error('external_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Selected Items Summary -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h5 class="card-title">Selected Items Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Selected User:</strong>
                                                <p id="selectedUserSummary" class="text-muted">
                                                    @if(old('user_id'))
                                                        @php $selectedUser = $users->firstWhere('user_id', old('user_id')); @endphp
                                                        @if($selectedUser)
                                                            {{ $selectedUser->first_name }} {{ $selectedUser->middle_name ? $selectedUser->middle_name . ' ' : '' }}{{ $selectedUser->last_name }}
                                                        @else
                                                            None selected
                                                        @endif
                                                    @else
                                                        None selected
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Selected Activity:</strong>
                                                <p id="selectedActivitySummary" class="text-muted">
                                                    @if(old('activity_id'))
                                                        @php $selectedActivity = $activities->firstWhere('activity_id', old('activity_id')); @endphp
                                                        @if($selectedActivity)
                                                            {{ $selectedActivity->activity_title_en }}
                                                        @else
                                                            None selected
                                                        @endif
                                                    @else
                                                        None selected
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Selected COP:</strong>
                                                <p id="selectedCopSummary" class="text-muted">
                                                    @if(old('cop_id'))
                                                        @php $selectedCop = $cops->firstWhere('cop_id', old('cop_id')); @endphp
                                                        @if($selectedCop)
                                                            {{ $selectedCop->cop_name }}
                                                        @else
                                                            None selected
                                                        @endif
                                                    @else
                                                        None selected
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Assign User to Activity
                                </button>
                                <a href="{{ route('activity-users.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" />
<style>
    /* Select2 Bootstrap4 Theme Adjustments */
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.25rem + 2px) !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 2.25rem !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: 2.25rem !important;
    }
    
    /* Custom Switch Toggle Styling */
    .custom-switch {
        padding-left: 2.25rem;
    }
    .custom-switch .custom-control-label::before {
        left: -2.25rem;
        width: 1.75rem;
        pointer-events: all;
        border-radius: 0.5rem;
    }
    .custom-switch .custom-control-label::after {
        top: calc(0.25rem + 2px);
        left: calc(-2.25rem + 2px);
        width: calc(1rem - 4px);
        height: calc(1rem - 4px);
        background-color: #adb5bd;
        border-radius: 0.5rem;
        transition: transform 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .custom-switch .custom-control-input:checked ~ .custom-control-label::after {
        background-color: #fff;
        transform: translateX(0.75rem);
    }
    
    /* Select2 container width fix */
    .select2-container {
        width: 100% !important;
    }
    
    /* Summary card styling */
    .card.bg-light {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }
    
    /* Form group spacing */
    .form-group {
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for User dropdown
    $('#user_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select a user...',
        allowClear: true,
        minimumResultsForSearch: 0, // Show search box always
        language: {
            noResults: function() {
                return "No users found";
            }
        }
    });

    // Initialize Select2 for Activity dropdown
    $('#activity_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select an activity...',
        allowClear: true,
        minimumResultsForSearch: 0, // Show search box always
        language: {
            noResults: function() {
                return "No activities found";
            }
        }
    });

    // Initialize Select2 for COP dropdown
    $('#cop_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Select a COP (Optional)',
        allowClear: true,
        minimumResultsForSearch: 0, // Show search box always
        language: {
            noResults: function() {
                return "No COPs found";
            }
        }
    });

    // Update summary when user is selected
    $('#user_id').on('select2:select', function(e) {
        var data = e.params.data;
        var text = data.text;
        $('#selectedUserSummary').text(text).removeClass('text-muted').addClass('text-primary');
    });

    $('#user_id').on('select2:clear', function() {
        $('#selectedUserSummary').text('None selected').removeClass('text-primary').addClass('text-muted');
    });

    // Update summary when activity is selected
    $('#activity_id').on('select2:select', function(e) {
        var data = e.params.data;
        var text = data.text;
        $('#selectedActivitySummary').text(text).removeClass('text-muted').addClass('text-primary');
    });

    $('#activity_id').on('select2:clear', function() {
        $('#selectedActivitySummary').text('None selected').removeClass('text-primary').addClass('text-muted');
    });

    // Update summary when COP is selected
    $('#cop_id').on('select2:select', function(e) {
        var data = e.params.data;
        var text = data.text;
        $('#selectedCopSummary').text(text).removeClass('text-muted').addClass('text-primary');
    });

    $('#cop_id').on('select2:clear', function() {
        $('#selectedCopSummary').text('None selected').removeClass('text-primary').addClass('text-muted');
    });

    // Form validation before submit
    $('#createActivityUserForm').on('submit', function(e) {
        var userId = $('#user_id').val();
        var activityId = $('#activity_id').val();
        
        if (!userId) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select a user'
            });
            return false;
        }
        
        if (!activityId) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select an activity'
            });
            return false;
        }
    });

    // Initialize toggles with old values if they exist
    @if(old('invited'))
        $('#invited').prop('checked', true);
    @endif
    
    @if(old('attended'))
        $('#attended').prop('checked', true);
    @endif
    
    @if(old('is_lead'))
        $('#is_lead').prop('checked', true);
    @endif
});
</script>

<!-- Include SweetAlert if not already in your layout -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Display validation errors if any -->
@if($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        title: 'Validation Error!',
        html: '<ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>'
    });
</script>
@endif
@endpush