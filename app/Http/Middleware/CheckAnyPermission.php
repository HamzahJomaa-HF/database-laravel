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
        $employee = Auth::guard('employee')->user();

        if (!$employee) {
            return $this->unauthorizedResponse($request, "You must be logged in.");
        }

        if (!$employee->role) {
            return $this->unauthorizedResponse($request, "No role assigned to your account.");
        }

        // Check if user has ANY of the permissions
        foreach ($permissions as $permission) {
            $parts = explode(".", $permission);

            if (count($parts) !== 2) {
                continue; // Skip invalid format
            }

            $module = $parts[0];
            $accessLevel = $parts[1];

            // Use the Employee model's hasPermission method
            if ($employee->hasPermission($module, $accessLevel)) {
                return $next($request);
            }
        }

        return $this->unauthorizedResponse(
            $request,
            "You do not have any of the required permissions."
        );
    }

    private function unauthorizedResponse(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                "error" => "Forbidden",
                "message" => $message
            ], 403);
        }

        abort(403, $message);
    }
}
