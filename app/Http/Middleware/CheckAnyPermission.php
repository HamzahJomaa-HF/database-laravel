<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAnyPermission
{
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        // Try to authenticate via Sanctum first (API token)
        $user = null;
        
        // Check for Bearer token (Sanctum)
        if ($request->bearerToken()) {
            $user = Auth::guard('sanctum')->user();
        }
        
        // If no token, try session auth
        if (!$user && Auth::guard('employee')->check()) {
            $user = Auth::guard('employee')->user();
        }
        
        // If still no user, return unauthorized
        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'You must be logged in.'
                ], 401);
            }
            return redirect()->route('login');
        }

        // For /me endpoint, always allow access
        if ($request->is('*/employees/me') || $request->is('*/me')) {
            return $next($request);
        }

        if (!$user->role) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => 'No role assigned to your account.'
                ], 403);
            }
            return response()->view('errors.403', [
                'message' => 'No role has been assigned to your account. Contact your administrator.'
            ], 403);
        }

        // Check if user has ANY of the permissions
        foreach ($permissions as $permission) {
            $parts = explode(".", $permission);

            if (count($parts) !== 2) {
                continue;
            }

            $module = $parts[0];
            $accessLevel = $parts[1];

            if ($user->hasPermission($module, $accessLevel)) {
                return $next($request);
            }
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'You do not have permission to access this resource.'
            ], 403);
        }

        return response()->view('errors.403', [
            'message' => 'You do not have permission to access this page.'
        ], 403);
    }
}