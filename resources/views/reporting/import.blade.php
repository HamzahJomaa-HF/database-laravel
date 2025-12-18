<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Import Reporting Data</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .upload-container {
            max-width: 700px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .import-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .import-section {
            margin-bottom: 30px;
        }
        .section-title {
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="upload-container">
        <h2 class="mb-4 text-center">
            📊 Import Reporting Data
        </h2>

        @if(session('success'))
            <div class="alert alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                ❌ {{ session('error') }}
            </div>
        @endif

        <!-- HIERARCHY IMPORT RESULTS -->
        @if(session('import_results'))
            <div class="import-section">
                <h5 class="section-title">🏛️ Hierarchy Import Details:</h5>
                @php $results = session('import_results'); @endphp
                <div class="import-details">
                    
                    <div class="detail-item">
                        <span>Rows Processed:</span>
                        <strong>{{ $results['processed'] ?? 0 }}</strong>
                    </div>
                    
                    <div class="detail-item">
                        <span>Components (New/Existing):</span>
                        <strong>
                            {{ $results['details']['components']['new'] ?? 0 }} /
                            {{ $results['details']['components']['existing'] ?? 0 }}
                        </strong>
                    </div>
                    
                    <div class="detail-item">
                        <span>Programs (New/Existing):</span>
                        <strong>
                            {{ $results['details']['programs']['new'] ?? 0 }} /
                            {{ $results['details']['programs']['existing'] ?? 0 }}
                        </strong>
                    </div>
                    
                    <div class="detail-item">
                        <span>Units (New/Existing):</span>
                        <strong>
                            {{ $results['details']['units']['new'] ?? 0 }} /
                            {{ $results['details']['units']['existing'] ?? 0 }}
                        </strong>
                    </div>
                    
                    <div class="detail-item">
                        <span>Actions (New/Existing):</span>
                        <strong>
                            {{ $results['details']['actions']['new'] ?? 0 }} /
                            {{ $results['details']['actions']['existing'] ?? 0 }}
                        </strong>
                    </div>
                    
                    @if(!empty($results['errors']))
                        <div class="detail-item text-danger">
                            <span>Errors Found:</span>
                            <strong>{{ count($results['errors']) }}</strong>
                        </div>
                        <div class="mt-2">
                            <small class="text-danger">
                                First error: {{ $results['errors'][0] }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- ACTIVITIES IMPORT RESULTS -->
        @if(session('activities_results'))
            <div class="import-section">
                <h5 class="section-title">📝 Activities Import Details:</h5>
                @php $activitiesResults = session('activities_results'); @endphp
                <div class="import-details">
                    
                    <div class="detail-item">
                        <span>Rows Processed:</span>
                        <strong>{{ $activitiesResults['processed'] ?? 0 }}</strong>
                    </div>
                    
                    <div class="detail-item">
                        <span>Activities (New/Existing):</span>
                        <strong>
                            {{ $activitiesResults['details']['activities']['new'] ?? 0 }} /
                            {{ $activitiesResults['details']['activities']['existing'] ?? 0 }}
                        </strong>
                    </div>
                    
                    @if(isset($activitiesResults['details']['indicators']))
                    <div class="detail-item">
                        <span>Indicators (New/Existing):</span>
                        <strong>
                            {{ $activitiesResults['details']['indicators']['new'] ?? 0 }} /
                            {{ $activitiesResults['details']['indicators']['existing'] ?? 0 }}
                        </strong>
                    </div>
                    @endif
                    
                    @if(isset($activitiesResults['details']['focalpoints']))
                    <div class="detail-item">
                        <span>Focal Points (New/Existing):</span>
                        <strong>
                            {{ $activitiesResults['details']['focalpoints']['new'] ?? 0 }} /
                            {{ $activitiesResults['details']['focalpoints']['existing'] ?? 0 }}
                        </strong>
                    </div>
                    @endif
                    
                    @if(isset($activitiesResults['details']['activity_indicators']))
                    <div class="detail-item">
                        <span>Activity Indicators Created:</span>
                        <strong>{{ $activitiesResults['details']['activity_indicators'] ?? 0 }}</strong>
                    </div>
                    @endif
                    
                    @if(isset($activitiesResults['details']['activity_focalpoints']))
                    <div class="detail-item">
                        <span>Activity Focal Points Created:</span>
                        <strong>{{ $activitiesResults['details']['activity_focalpoints'] ?? 0 }}</strong>
                    </div>
                    @endif
                    
                    @if(!empty($activitiesResults['errors']))
                        <div class="detail-item text-danger">
                            <span>Errors Found:</span>
                            <strong>{{ count($activitiesResults['errors']) }}</strong>
                        </div>
                        <div class="mt-2">
                            <small class="text-danger">
                                First error: {{ $activitiesResults['errors'][0] }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- DATABASE COUNTS -->
        @if(session('db_counts'))
            <div class="import-section">
                <h5 class="section-title">🗃️ Database Totals:</h5>
                @php $dbCounts = session('db_counts'); @endphp
                <div class="import-details">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <span>Components:</span>
                                <strong>{{ $dbCounts['components'] ?? 0 }}</strong>
                            </div>
                            <div class="detail-item">
                                <span>Programs:</span>
                                <strong>{{ $dbCounts['programs'] ?? 0 }}</strong>
                            </div>
                            <div class="detail-item">
                                <span>Units:</span>
                                <strong>{{ $dbCounts['units'] ?? 0 }}</strong>
                            </div>
                            <div class="detail-item">
                                <span>Actions:</span>
                                <strong>{{ $dbCounts['actions'] ?? 0 }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if(isset($dbCounts['activities']))
                            <div class="detail-item">
                                <span>Activities:</span>
                                <strong>{{ $dbCounts['activities'] ?? 0 }}</strong>
                            </div>
                            @endif
                            @if(isset($dbCounts['indicators']))
                            <div class="detail-item">
                                <span>Indicators:</span>
                                <strong>{{ $dbCounts['indicators'] ?? 0 }}</strong>
                            </div>
                            @endif
                            @if(isset($dbCounts['focalpoints']))
                            <div class="detail-item">
                                <span>Focal Points:</span>
                                <strong>{{ $dbCounts['focalpoints'] ?? 0 }}</strong>
                            </div>
                            @endif
                            @if(isset($dbCounts['activity_indicators']))
                            <div class="detail-item">
                                <span>Activity Indicators:</span>
                                <strong>{{ $dbCounts['activity_indicators'] ?? 0 }}</strong>
                            </div>
                            @endif
                        </div>
                        @if(env('APP_DEBUG', false))
    <div class="alert alert-warning">
        <strong>Debug Mode Active</strong> - Check Laravel logs for detailed import information
    </div>
@endif
                    </div>
                </div>
            </div>
        @endif

<form action="{{ route('reporting.import.import') }}" method="POST" enctype="multipart/form-data">            @csrf
            
            <div class="mb-4">
                <label for="excel_file" class="form-label fw-bold">Select Excel/CSV File</label>
                <input type="file" 
                       class="form-control" 
                       id="excel_file" 
                       name="excel_file" 
                       accept=".xlsx,.xls"
                       required>
                <div class="form-text">
                    Supported formats: Excel (.xlsx, .xls)
                </div>
            </div>

            <div class="mb-3">
                <a href="{{ route('reporting.import.download-template') }}" class="btn btn-outline-primary btn-sm">
                    📥 Download Template
                </a>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    ⬆️ Upload & Import
                </button>
                <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                    ❌ Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Simple file name display -->
    <script>
        document.getElementById('excel_file').addEventListener('change', function(e) {
            var fileName = e.target.files[0].name;
            var nextSibling = e.target.nextElementSibling;
            if (nextSibling && nextSibling.tagName === 'DIV') {
                nextSibling.textContent = 'Selected: ' + fileName;
            }
        });
    </script>
</body>
</html>