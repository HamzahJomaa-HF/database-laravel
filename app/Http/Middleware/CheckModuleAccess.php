<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckModuleAccess
{
    public function handle(Request $request, Closure $next, $module = null, $requiredLevel = 'view')
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            abort(401, 'Authentication required.');
        }
        
        // If module parameter is provided, use it
        if ($module) {
            $moduleName = $this->normalizeModuleName($module);
        } else {
            // Otherwise, determine module from URL
            $moduleName = $this->getModuleFromRequest($request);
        }
        
        // Allow access if no module specified
        if (!$moduleName) {
            return $next($request);
        }
        
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
        $rawModule = $segments[0] ?? '';
        
        return $this->normalizeModuleName($rawModule);
    }
    
    private function normalizeModuleName($rawModule)
    {
        $moduleMap = [
            'activities' => 'activities',
            'users' => 'users',
            'projects' => 'projects',
            'programs' => 'programs',
            'action-plans' => 'action_plans',
            'surveys' => 'surveys',
            'reports' => 'reports',
            'admin' => 'all',
            'action_plans' => 'action_plans', // handle both with and without dash
        ];
        
        return $moduleMap[$rawModule] ?? $rawModule;
    }
}