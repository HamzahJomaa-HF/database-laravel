<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateEmployee
{
    public function handle(Request $request, Closure $next)
    {
        // Check if employee is authenticated
        if (!Auth::guard('employee')->check()) {
            // Store intended URL for redirect after login
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            
            return redirect()->route('login')
                ->with('error', 'Please log in to access this page.');
        }
        
        // Check if employee is active
        $employee = Auth::guard('employee')->user();
        if (!$employee->isActive()) {
            Auth::guard('employee')->logout();
            return redirect()->route('login')
                ->with('error', 'Your account is inactive.');
        }
        
        return $next($request);
    }
}