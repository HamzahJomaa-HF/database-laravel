<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthController extends Controller
{
    /**
     * Login and generate JWT token
     */
    public function login(Request $request)
    {
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Get credentials
        $credentials = $request->only(['email', 'password']);
        
        // Attempt to login with API guard
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }
        
        // Get authenticated employee
        $employee = Auth::guard('api')->user();
        
        // Check if account is active
        if (!$employee->isActive()) {
            Auth::guard('api')->logout();
            return response()->json([
                'success' => false,
                'message' => 'Your account is deactivated. Please contact administrator.'
            ], 401);
        }
        
        // Return success response with token
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl', 60) * 60,
            'user' => [
                'employee_id' => $employee->employee_id,
                'full_name' => $employee->full_name,
                'email' => $employee->email,
                'employee_type' => $employee->employee_type,
                'role' => $employee->role ? $employee->role->role_name : null,
                'is_active' => $employee->isActive(),
            ]
        ]);
    }

    /**
     * Logout (invalidate token)
     */
    public function logout()
    {
        try {
            Auth::guard('api')->logout();
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout, please try again'
            ], 500);
        }
    }

    /**
     * Refresh token (get new token when old one expires)
     */
    public function refresh()
    {
        try {
            $newToken = Auth::guard('api')->refresh();
            
            return response()->json([
                'success' => true,
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl', 60) * 60
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed. Please login again.'
            ], 401);
        }
    }

    /**
     * Get authenticated user details
     */
    public function me()
    {
        try {
            $employee = Auth::guard('api')->user();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            
            // Load relationships if needed
            $employee->load('role');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'employee_id' => $employee->employee_id,
                    'full_name' => $employee->full_name,
                    'first_name' => $employee->first_name,
                    'last_name' => $employee->last_name,
                    'email' => $employee->email,
                    'phone_number' => $employee->phone_number,
                    'employee_type' => $employee->employee_type,
                    'role' => $employee->role ? $employee->role->role_name : null,
                    'role_id' => $employee->role_id,
                    'is_active' => $employee->isActive(),
                    'permissions' => $employee->getPermissionStrings(),
                    'start_date' => $employee->start_date,
                    'end_date' => $employee->end_date,
                    'external_id' => $employee->external_id,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user information'
            ], 500);
        }
    }

    /**
     * Validate token (check if token is still valid)
     */
    public function validateToken()
    {
        try {
            $employee = Auth::guard('api')->user();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 401);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Token is valid',
                'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token is invalid or expired'
            ], 401);
        }
    }
}