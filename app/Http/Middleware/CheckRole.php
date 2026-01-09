<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            abort(401, 'Authentication required.');
        }
        
        // Check if employee has required role
        $userRole = $employee->role;
        
        if (!$userRole || !in_array($userRole->role_name, $roles)) {
            $roleNames = implode(' or ', $roles);
            abort(403, "Requires {$roleNames} role.");
        }
        
        return $next($request);
    }
}