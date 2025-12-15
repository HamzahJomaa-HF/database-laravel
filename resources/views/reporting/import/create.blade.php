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

        <form action="{{ route('reporting.import.handle') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label for="csv_file" class="form-label fw-bold">Select Excel/CSV File</label>
                <input type="file" 
                       class="form-control" 
                       id="csv_file" 
                       name="csv_file" 
                       accept=".csv,.xlsx,.xls"
                       required>
                <div class="form-text">
                    Supported formats: CSV, Excel (.xlsx, .xls)
                </div>
            </div>

            <div class="mb-3">
                <a href="{{ route('reporting.import.download-template') }}" class="btn btn-outline-primary btn-sm">
                    📥 Download Template
                </a>
            </div>

            @if(isset($summary) && is_array($summary))
            <div class="mb-4 p-3 border rounded">
                <h5 class="mb-3">📈 Import Summary:</h5>
                <ul class="list-unstyled mb-0">
                    @foreach($summary as $key => $value)
                    <li class="mb-1">
                        <span class="fw-bold">{{ ucfirst($key) }}:</span> {{ $value }} imported
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

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
        document.getElementById('csv_file').addEventListener('change', function(e) {
            var fileName = e.target.files[0].name;
            var nextSibling = e.target.nextElementSibling;
            if (nextSibling && nextSibling.tagName === 'DIV') {
                nextSibling.textContent = 'Selected: ' + fileName;
            }
        });
    </script>
</body>
</html>