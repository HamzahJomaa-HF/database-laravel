<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hariri Foundation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Hariri Foundation</a>
            <div class="navbar-nav ms-auto">
                <span class="nav-item nav-link text-white">
                    Welcome, {{ $employee->first_name }}!
                </span>
                <form method="POST" action="{{ route('logout') }}" class="nav-item">
                    @csrf
                    <button type="submit" class="nav-link btn btn-link text-white">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Dashboard</h4>
                    </div>
                    <div class="card-body">
                        <h5>Employee Information</h5>
                        <p><strong>Name:</strong> {{ $employee->first_name }} {{ $employee->last_name }}</p>
                        <p><strong>Email:</strong> {{ $employee->email }}</p>
                        <p><strong>Role:</strong> {{ $employee->role->role_name ?? 'No role assigned' }}</p>
                        
                        <hr>
                        
                        <h5>Accessible Modules</h5>
                        <ul>
                            @foreach($modules as $module)
                                <li>{{ ucfirst($module) }}</li>
                            @endforeach
                        </ul>
                        
                        <hr>
                        
                        <h5>Quick Links</h5>
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('activities.index') }}" class="btn btn-outline-primary w-100">
                                    Activities
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('users.index') }}" class="btn btn-outline-primary w-100">
                                    Users
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('action-plans.index') }}" class="btn btn-outline-primary w-100">
                                    Action Plans
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('reporting.import.import') }}" class="btn btn-outline-primary w-100">
                                    Reporting
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>