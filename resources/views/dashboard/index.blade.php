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
                    Welcome, {{ $employee->first_name }} ({{ $employee->role->role_name ?? 'No role' }})
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
                        
                        <h5>Your Accessible Modules</h5>
                        <ul>
                            @php
                                $accessibleModules = $employee->getAccessibleModules();
                            @endphp
                            
                            @if(in_array('all', $accessibleModules))
                                <li><strong>ALL Modules (Full Access)</strong> ✅</li>
                            @else
                                @foreach($accessibleModules as $module)
                                    @php
                                        // Get the highest access level for this module
                                        $access = $employee->moduleAccess()
                                            ->where('module', $module)
                                            ->whereNull('resource_id')
                                            ->first();
                                        $accessLevel = $access ? $access->access_level : 'view';
                                    @endphp
                                    <li>{{ ucfirst($module) }} ({{ $accessLevel }} access)</li>
                                @endforeach
                            @endif
                            
                            @if(empty($accessibleModules))
                                <li class="text-muted">No module access assigned</li>
                            @endif
                        </ul>
                        
                        <hr>
                        
                        <h5>Your Protected Links</h5>
                        <p class="text-muted"><small>These require authentication and module access:</small></p>
                        <div class="row">
                            {{-- ACTIVITIES LINK --}}
                            @if($employee->hasModuleAccess('activities', 'view'))
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('protected.activities.index') }}" class="btn btn-outline-primary w-100">
                                    Activities
                                    @if($employee->hasModuleAccess('activities', 'create'))
                                        <br><small class="text-success">Can create</small>
                                    @endif
                                </a>
                            </div>
                            @endif
                            
                            {{-- USERS LINK --}}
                            @if($employee->hasModuleAccess('users', 'view'))
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('protected.users.index') }}" class="btn btn-outline-primary w-100">
                                    Users
                                    @if($employee->hasModuleAccess('users', 'create'))
                                        <br><small class="text-success">Can create</small>
                                    @endif
                                </a>
                            </div>
                            @endif
                            
                            {{-- ACTION PLANS LINK --}}
                            @if($employee->hasModuleAccess('action_plans', 'view'))
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('action-plans.index') }}" class="btn btn-outline-primary w-100">
                                    Action Plans
                                </a>
                            </div>
                            @endif
                            
                            {{-- REPORTING LINK --}}
                            @if($employee->hasModuleAccess('reports', 'view'))
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('protected.reporting.import.import') }}" class="btn btn-outline-primary w-100">
                                    Reporting
                                </a>
                            </div>
                            @endif
                            
                            {{-- PROGRAMS LINK --}}
                            @if($employee->hasModuleAccess('programs', 'view'))
                            <div class="col-md-3 mb-2">
                                <a href="#" class="btn btn-outline-primary w-100">
                                    Programs
                                </a>
                            </div>
                            @endif
                            
                            {{-- PROJECTS LINK --}}
                            @if($employee->hasModuleAccess('projects', 'view'))
                            <div class="col-md-3 mb-2">
                                <a href="#" class="btn btn-outline-primary w-100">
                                    Projects
                                </a>
                            </div>
                            @endif
                            
                            {{-- SURVEYS LINK --}}
                            @if($employee->hasModuleAccess('surveys', 'view'))
                            <div class="col-md-3 mb-2">
                                <a href="#" class="btn btn-outline-primary w-100">
                                    Surveys
                                </a>
                            </div>
                            @endif
                        </div>
                        
                        <hr>
                        
                        <h5>Public Links (Everyone can access)</h5>
                        <p class="text-muted"><small>These don't require login:</small></p>
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary w-100">
                                    Activities (Public)
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">
                                    Users (Public)
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('action-plans.index') }}" class="btn btn-outline-secondary w-100">
                                    Action Plans (Public)
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('reporting.import.import') }}" class="btn btn-outline-secondary w-100">
                                    Reporting (Public)
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