@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Bulk Import Activities</h3>
                </div>
                <div class="card-body">
                    @if(session('import_failed_rows'))
                        <div class="alert alert-warning">
                            <h5>Failed Rows:</h5>
                            <ul>
                                @foreach(session('import_failed_rows') as $failed)
                                    <li>Row {{ $failed['row'] }}: {{ $failed['error'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h5>Excel File Format Instructions:</h5>
                        <p>Your Excel file should have the following columns:</p>
                        <ul>
                            <li><strong>activity_title_en</strong> - English title (required if Arabic title not provided)</li>
                            <li><strong>activity_title_ar</strong> - Arabic title (optional)</li>
                            <li><strong>activity_type</strong> - Type of activity (required)</li>
                            <li><strong>start_date</strong> - Start date in YYYY-MM-DD format (required)</li>
                            <li><strong>end_date</strong> - End date in YYYY-MM-DD format (optional)</li>
                            <li><strong>venue</strong> - Venue location (optional)</li>
                            <li><strong>content_network</strong> - Content network (optional)</li>
                            <li><strong>maximum_capacity</strong> - Maximum capacity (optional)</li>
                            <li><strong>operational_support</strong> - Comma-separated values (optional)</li>
                            <li><strong>projects</strong> - Comma-separated project IDs (optional)</li>
                            <li><strong>portfolios</strong> - Comma-separated portfolio IDs (optional)</li>
                            <li><strong>rp_activities</strong> - Comma-separated RP activity IDs (optional)</li>
                            <li><strong>focal_points</strong> - Comma-separated employee IDs (optional)</li>
                        </ul>
                        <p><a href="{{ route('activities.import.template') }}" class="btn btn-sm btn-primary">Download Template</a></p>
                    </div>

                    <form action="{{ route('activities.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group">
                            <label for="excel_file">Select Excel File</label>
                            <input type="file" class="form-control @error('excel_file') is-invalid @enderror" 
                                   id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                            @error('excel_file')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-success">Import Activities</button>
                            <a href="{{ route('activities.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection