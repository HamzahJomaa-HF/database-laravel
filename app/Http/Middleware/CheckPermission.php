<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            abort(401, 'Authentication required.');
        }
        
        // Check if employee has ANY of the specified permissions
        $hasPermission = false;
        
        foreach ($permissions as $permission) {
            if ($employee->hasPermission($permission)) {
                $hasPermission = true;
                break;
            }
        }
        
        if (!$hasPermission) {
            $permissionNames = implode(' or ', $permissions);
            abort(403, "You don't have permission: {$permissionNames}");
        }
        
        return $next($request);
    }
}