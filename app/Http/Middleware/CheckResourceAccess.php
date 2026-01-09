<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckResourceAccess
{
    // Map parameter names to resource types
    private $resourceMap = [
        'activity' => ['module' => 'activities', 'type' => 'activity'],
        'user' => ['module' => 'users', 'type' => 'user'],
        'project' => ['module' => 'projects', 'type' => 'project'],
        'program' => ['module' => 'programs', 'type' => 'program'],
        'action_plan' => ['module' => 'action_plans', 'type' => 'action_plan'],
        'survey' => ['module' => 'surveys', 'type' => 'survey'],
    ];
    
    public function handle(Request $request, Closure $next, $requiredLevel = 'view')
    {
        $employee = Auth::guard('employee')->user();
        
        if (!$employee) {
            abort(401, 'Authentication required.');
        }
        
        // Get resource from route parameters
        $resource = $this->getResourceFromRequest($request);
        
        if (!$resource) {
            // No specific resource in route (e.g., /activities index page)
            return $next($request);
        }
        
        // Check if we have mapping for this resource type
        if (!array_key_exists($resource['type'], $this->resourceMap)) {
            return $next($request);
        }
        
        $mapping = $this->resourceMap[$resource['type']];
        
        // Check resource access using appropriate method
        $methodName = 'has' . ucfirst($resource['type']) . 'Access';
        
        if (method_exists($employee, $methodName)) {
            if (!$employee->{$methodName}($resource['id'], $requiredLevel)) {
                abort(403, "You don't have {$requiredLevel} access to this {$resource['type']}.");
            }
        } else {
            // Fallback to generic hasAccess method
            if (!$employee->hasAccess($mapping['module'], $requiredLevel, $resource['id'], $resource['type'])) {
                abort(403, "You don't have {$requiredLevel} access to this {$resource['type']}.");
            }
        }
        
        return $next($request);
    }
    
    private function getResourceFromRequest(Request $request)
    {
        $route = $request->route();
        
        if (!$route) {
            return null;
        }
        
        // Check common parameter names
        $parameters = $route->parameters();
        
        foreach ($parameters as $paramName => $paramValue) {
            // Try to determine resource type from parameter name
            if (in_array($paramName, ['activity', 'user', 'project', 'program', 'action_plan', 'survey'])) {
                return [
                    'id' => $paramValue,
                    'type' => $paramName
                ];
            }
            
            // Check if parameter name contains resource type
            foreach ($this->resourceMap as $type => $mapping) {
                if (str_contains($paramName, $type)) {
                    return [
                        'id' => $paramValue,
                        'type' => $type
                    ];
                }
            }
        }
        
        return null;
    }
}