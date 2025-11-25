@section('content')
    {{-- Dashboard Header Area --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="text-muted small">HOME / DASHBOARD</span>
            <h1 class="fw-bold">DASHBOARD</h1>
        </div>
        <button class="btn btn-success"><i class="bi bi-file-earmark-bar-graph me-1"></i> New Report</button>
    </div>

    {{-- Top Metrics Row (similar to Total Sales, Total Visitors, Repeat Customer) --}}
    <div class="row g-4 mb-4">
        {{-- Card 1: Total Sales -> Total Users --}}
        <div class="col-lg-4">
            {{-- ... Card content from users/dashboard.blade.php for Total Users ... --}}
        </div>
        {{-- Card 2: Total Visitors -> New Users This Month --}}
        <div class="col-lg-4">
            {{-- ... Card content from users/dashboard.blade.php for New Registrations ... --}}
        </div>
        {{-- Card 3: Repeat Customer -> User Status Distribution --}}
        <div class="col-lg-4">
             {{-- ... Card content from users/dashboard.blade.php for Distribution ... --}}
        </div>
    </div>
    
    {{-- Main Content Row (similar to Current Sales & History) --}}
    <div class="row g-4">
        {{-- Card 4: Recent Activity (Large, 7 columns) --}}
        <div class="col-lg-7">
            {{-- ... Card content from users/dashboard.blade.php for Recent Activity ... --}}
        </div>
        {{-- Card 5: Users Needing Action (Smaller, 5 columns) --}}
        <div class="col-lg-5">
            {{-- ... Card content from users/dashboard.blade.php for Users Needing Action ... --}}
        </div>
    </div>
@endsection