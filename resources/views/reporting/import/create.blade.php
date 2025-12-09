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
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="upload-container">
        <h2 class="mb-4 text-center">
            <i class="fas fa-file-import"></i> Import Reporting Data
        </h2>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('reporting.import.handle') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label for="excel_file" class="form-label fw-bold">Select Excel/CSV File</label>
                <input type="file" 
                       class="form-control" 
                       id="excel_file" 
                       name="excel_file" 
                       accept=".csv,.xlsx,.xls"
                       required>
                <div class="form-text">
                    Supported formats: CSV, Excel (.xlsx, .xls). Max size: 10MB
                </div>
            </div>

            <div class="alert alert-info">
                <h6 class="alert-heading mb-2">
                    <i class="fas fa-info-circle"></i> Instructions
                </h6>
                <p class="mb-0">Upload your Excel/CSV file containing reporting data. The system will automatically import all data into the database.</p>
            </div>

            @if(isset($summary) && is_array($summary))
            <div class="mb-4 p-3 border rounded">
                <h5 class="mb-3">Import Summary:</h5>
                <ul class="list-unstyled mb-0">
                    @foreach($summary as $key => $value)
                    <li class="mb-1">
                        <span class="fw-bold">{{ ucfirst($key) }}:</span> {{ $value }}
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-upload"></i> Upload & Import
                </button>
                <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
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