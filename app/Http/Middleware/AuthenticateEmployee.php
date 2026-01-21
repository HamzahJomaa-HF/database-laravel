<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateEmployee
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if employee is authenticated through the employee guard
        if (!Auth::guard('employee')->check()) {
            // Store intended URL for redirect after login
            session(['url.intended' => $request->url()]);
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            
            // FIX: Change from 'employee.login' to 'login'
            return redirect()->route('login')
                ->with('error', 'Please log in to access this page.');
        }
        
        // Get the authenticated employee
        $employee = Auth::guard('employee')->user();
        
        // Check if employee has credentials record
        if (!$employee->credentials) {
            Auth::guard('employee')->logout();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Account not properly configured.'], 403);
            }
            
            return redirect()->route('login')
                ->with('error', 'Your account is not properly configured. Contact administrator.');
        }
        
        // Check if employee account is active (from credentials_employees table)
        if (!$employee->credentials->is_active) {
            Auth::guard('employee')->logout();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Account is inactive.'], 403);
            }
            
            return redirect()->route('login')
                ->with('error', 'Your account is inactive. Contact administrator.');
        }
        
        // Update last login time
        $employee->credentials->update(['last_login_at' => now()]);
        
        // Share employee data with all views
        view()->share('currentEmployee', $employee);
        
        return $next($request);
    }
}