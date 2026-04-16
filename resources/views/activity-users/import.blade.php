{{-- resources/views/activity-users/import.blade.php --}}
@extends('layouts.app')

@section('title', 'Import Users to Activity')

{{-- Load Select2 CSS in the head --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* ── Make Select2 match Bootstrap's form-control look ── */
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
        color: #212529;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        top: 0;
        right: 8px;
    }

    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-dropdown {
        border: 1px solid #86b7fe;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        font-size: 1rem;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
        font-size: 0.95rem;
        outline: none;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .select2-results__option {
        padding: 0.45rem 0.75rem;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #e9f0ff;
        color: #0d6efd;
        font-weight: 500;
    }

    /* Placeholder color */
    .select2-container--default .select2-selection__placeholder {
        color: #6c757d;
    }
    
    /* Enhanced styling for better UX */
    .select2-results__option {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .select2-results__option .activity-date {
        font-size: 0.85rem;
        color: #6c757d;
        margin-left: 10px;
    }
    
    .select2-results__option--highlighted .activity-date {
        color: rgba(255, 255, 255, 0.8);
    }
    
    /* Loading state */
    .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 20px;
        font-size: 1.2rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('activity-users.index') }}" class="text-decoration-none">Activity Users</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Import Users</li>
                        </ol>
                    </nav>
                    <h1 class="h2 fw-bold mb-1">Import Users to Activity</h1>
                    <p class="text-muted mb-0">Bulk import users and assign them to a specific activity</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom py-3">
                    <h5 class="mb-0 fw-semibold">Import Configuration</h5>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <i class="bi bi-exclamation-triangle-fill me-3"></i>
                                <div>
                                    <h6 class="alert-heading mb-2">Please correct the following errors:</h6>
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('activity-users.import.process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                        @csrf

                       {{-- In your import.blade.php, replace the entire activity selection div --}}

{{-- ── Activity Selection (Livewire Enhanced Search) ── --}}
<div class="mb-4">
    <label class="form-label fw-semibold">
        Select Activity <span class="text-danger">*</span>
    </label>
    
    {{-- Livewire Activity Selector Component --}}
    @livewire('activity-selector')
    
    @error('activity_id')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    
    <div class="form-text mt-2">
        <i class="bi bi-search me-1"></i>
        Type to search by title, date, or venue
    </div>
</div>

                        {{-- ── COP Selection (Optional) ── --}}
                        <div class="mb-4">
                            <label for="cop_id" class="form-label fw-semibold">
                                Community of Practice (Optional)
                            </label>
                            <select name="cop_id" id="cop_id" class="form-control form-select">
                                <option value="">-- Select COP --</option>
                                @foreach($cops as $cop)
                                    <option value="{{ $cop->cop_id }}" {{ old('cop_id') == $cop->cop_id ? 'selected' : '' }}>
                                        {{ $cop->cop_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Assign users to a specific Community of Practice
                            </div>
                        </div>

                        {{-- ── File Upload ── --}}
                        <div class="mb-4">
                            <label for="import_file" class="form-label fw-semibold">
                                Import File <span class="text-danger">*</span>
                            </label>
                            <input type="file" name="import_file" id="import_file"
                                   class="form-control @error('import_file') is-invalid @enderror"
                                   accept=".csv,.xlsx,.xls" required>
                            @error('import_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2">
                                <i class="bi bi-file-excel me-1"></i>
                                Supported formats: CSV, Excel (.xlsx, .xls)<br>
                                
                            </div>
                        </div>

                        {{-- ── Default Values ── --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="invited_default" id="invited_default"
                                           class="form-check-input" value="1" {{ old('invited_default') ? 'checked' : '' }}>
                                    <label for="invited_default" class="form-check-label">
                                        Set all imported users as <strong>Invited</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" name="attended_default" id="attended_default"
                                           class="form-check-input" value="1" {{ old('attended_default') ? 'checked' : '' }}>
                                    <label for="attended_default" class="form-check-label">
                                        Set all imported users as <strong>Attended</strong>
                                    </label>
                                </div>
                            </div>
                        </div>

                       {{-- ── User Type Default (saved in activity_users) ── --}}
                        <div class="mb-4">
                            <label for="default_user_type" class="form-label fw-semibold">
                                User Type 
                            </label>
                            <select name="default_user_type" id="default_user_type" class="form-control form-select" required>
                                <option value="">-- No default type --</option>
                                <option value="Stakeholder" {{ old('default_user_type') == 'Stakeholder' ? 'selected' : '' }}>Stakeholder</option>
                                <option value="Beneficiary" {{ old('default_user_type') == 'Beneficiary' ? 'selected' : '' }}>Beneficiary</option>
                            </select>
                            
                        </div>

                        {{-- ── Action Buttons ── --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-cloud-upload me-1"></i> Import Users
                            </button>
                            <a href="{{ route('activity-users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- jQuery (required by Select2) – skip if already loaded globally --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        
        // ── Enhanced custom matcher for better search ──────────────────────
        function customMatcher(params, data) {
            // If there are no search terms, return all options
            if ($.trim(params.term) === '') {
                return data;
            }
            
            // Skip if no data element
            if (!data.element) {
                return null;
            }
            
            // Get search term
            const searchTerm = params.term.toLowerCase();
            
            // Get text from the option
            const originalText = data.text.toLowerCase();
            
            // Get additional data attributes for enhanced search
            const $element = $(data.element);
            const startDate = $element.data('start-date') || '';
            const endDate = $element.data('end-date') || '';
            const venue = $element.data('venue') || '';
            
            // Search in title (original text)
            if (originalText.indexOf(searchTerm) !== -1) {
                // Modify the displayed text to highlight match (optional)
                const regex = new RegExp(`(${params.term})`, 'gi');
                const highlightedText = data.text.replace(regex, '<mark>$1</mark>');
                // You can use highlightedText if you want to show highlights
                return data;
            }
            
            // Search in start date
            if (startDate && startDate.indexOf(searchTerm) !== -1) {
                return data;
            }
            
            // Search in end date
            if (endDate && endDate.indexOf(searchTerm) !== -1) {
                return data;
            }
            
            // Search in venue
            if (venue && venue.toLowerCase().indexOf(searchTerm) !== -1) {
                return data;
            }
            
            // No match found
            return null;
        }
        
        // ── Format results to show additional info ──────────────────────────
        function formatResult(option) {
            if (!option.id) {
                return option.text;
            }
            
            const $element = $(option.element);
            const startDate = $element.data('start-date');
            const endDate = $element.data('end-date');
            const venue = $element.data('venue');
            
            let additionalInfo = [];
            if (startDate) additionalInfo.push(`📅 ${startDate}`);
            if (endDate && endDate !== startDate) additionalInfo.push(`→ ${endDate}`);
            if (venue) additionalInfo.push(`📍 ${venue.substring(0, 30)}`);
            
            if (additionalInfo.length > 0) {
                return $(`
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span>${option.text}</span>
                        <small class="activity-date">${additionalInfo.join(' · ')}</small>
                    </div>
                `);
            }
            
            return option.text;
        }
        
        // ── Format selection (what shows in the select box) ─────────────────
        function formatSelection(option) {
            if (!option.id) {
                return option.text;
            }
            
            const $element = $(option.element);
            const startDate = $element.data('start-date');
            
            if (startDate) {
                return `${option.text} (${startDate})`;
            }
            
            return option.text;
        }
        
        // ── Initialize Enhanced Select2 on the activity dropdown ────────────
        $('#activity_id').select2({
            placeholder: '-- Search and select an activity --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#importForm'),
            matcher: customMatcher,  // Enhanced custom matcher
            templateResult: formatResult,  // Show additional info in dropdown
            templateSelection: formatSelection,  // Show date in selection
            language: {
                noResults: function () {
                    return '<div class="text-center py-2">🔍 No activities found</div>';
                },
                searching: function () {
                    return '🔍 Searching activities...';
                },
                errorLoading: function () {
                    return '⚠️ Error loading results';
                },
                inputTooShort: function () {
                    return 'Type at least 1 character to search';
                }
            },
            minimumInputLength: 0,  // Allow empty search to show all
            escapeMarkup: function(markup) {
                return markup;  // Allow HTML in results
            }
        });
        
        // ── Show additional info when activity is selected ──────────────────
        $('#activity_id').on('select2:select', function(e) {
            const data = e.params.data;
            const $element = $(data.element);
            const startDate = $element.data('start-date');
            const endDate = $element.data('end-date');
            const venue = $element.data('venue');
            
            let details = [];
            if (startDate) details.push(`<strong>Start Date:</strong> ${startDate}`);
            if (endDate) details.push(`<strong>End Date:</strong> ${endDate}`);
            if (venue) details.push(`<strong>Venue:</strong> ${venue}`);
            
            if (details.length > 0) {
                $('#selectedActivityDetails').html(details.join(' · '));
                $('#selectedActivityInfo').fadeIn();
            } else {
                $('#selectedActivityInfo').fadeOut();
            }
            
            // Clear any error styling
            $(this).next('.select2-container')
                .find('.select2-selection--single')
                .removeClass('border-danger')
                .css('box-shadow', '');
        });
        
        // ── Hide additional info when cleared ───────────────────────────────
        $('#activity_id').on('select2:clear', function() {
            $('#selectedActivityInfo').fadeOut();
            $(this).next('.select2-container')
                .find('.select2-selection--single')
                .removeClass('border-danger')
                .css('box-shadow', '');
        });
        
        // ── Re-apply validation border if the field was invalid on page load ─
        @if($errors->has('activity_id'))
            $('#activity_id').next('.select2-container')
                .find('.select2-selection--single')
                .addClass('border-danger');
        @endif
        
        // ── Clear the red border once user selects a value ───────────────────
        $('#activity_id').on('select2:select select2:clear', function () {
            $(this).next('.select2-container')
                .find('.select2-selection--single')
                .removeClass('border-danger')
                .css('box-shadow', '');
        });
        
        // ── Validate before form submit (Select2 hides the native <select>) ──
        $('#importForm').on('submit', function (e) {
            const activityVal = $('#activity_id').val();
            if (!activityVal) {
                e.preventDefault();
                const $select2Container = $('#activity_id').next('.select2-container');
                $select2Container
                    .find('.select2-selection--single')
                    .addClass('border-danger')
                    .css('box-shadow', '0 0 0 0.25rem rgba(220,53,69,.25)');
                
                // Scroll to the field
                $select2Container[0].scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                // Show error message
                alert('Please select an activity before importing.');
            }
        });
        
        // ── Keyboard shortcut: focus search on Ctrl+K or Cmd+K ───────────────
        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                $('#activity_id').select2('open');
            }
        });
        
        // ── Log initialization complete ─────────────────────────────────────
        console.log('Select2 enhanced dropdown initialized');
        console.log(`Loaded ${$('#activity_id option').length - 1} activities`);
    });
</script>
@endpush