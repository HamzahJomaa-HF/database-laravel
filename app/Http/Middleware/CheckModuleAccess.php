<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckModuleAccess
{
    // Map URL prefixes to modules
    private $moduleMap = [
        'activities' => 'activities',
        'users' => 'users',
        'projects' => 'projects',
        'programs' => 'programs',
        'action-plans' => 'action_plans',
        'surveys' => 'surveys',
        'reports' => 'reports',
        'admin' => 'all', // Admin requires 'all' module access
    ];
    
    public function handle(Request $request, Closure $next, $requiredLevel = 'view')
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            abort(401, 'Authentication required.');
        }
        
        // Determine module from URL
        $module = $this->getModuleFromRequest($request);
        
        // Check if module exists in our map
        if (!array_key_exists($module, $this->moduleMap)) {
            // Allow access if not a protected module
            return $next($request);
        }
        
        $moduleName = $this->moduleMap[$module];
        
        // Check module access
        if (!$employee->hasModuleAccess($moduleName, $requiredLevel)) {
            $moduleNames = [
                'activities' => 'Activities',
                'users' => 'Users',
                'projects' => 'Projects',
                'programs' => 'Programs',
                'action_plans' => 'Action Plans',
                'surveys' => 'Surveys',
                'reports' => 'Reports',
                'all' => 'Admin Panel',
            ];
            
            $moduleDisplay = $moduleNames[$moduleName] ?? ucfirst($moduleName);
            
            abort(403, "You don't have {$requiredLevel} access to {$moduleDisplay}.");
        }
        
        return $next($request);
    }
    
    private function getModuleFromRequest(Request $request)
    {
        $path = $request->path();
        $segments = explode('/', $path);
        
        // First segment after domain is usually the module
        // e.g., /activities/edit/123 → 'activities'
        return $segments[0] ?? '';
    }
}