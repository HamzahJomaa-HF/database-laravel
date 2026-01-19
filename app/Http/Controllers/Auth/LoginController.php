<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::guard('employee')->check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }
    
    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        try {
            Log::info('Login attempt started', ['email' => $request->email]);
            
            // Validate credentials
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);
            
            $remember = $request->filled('remember');
            
            // ✅ Use Laravel's built-in Auth with employee guard
            if (Auth::guard('employee')->attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                $employee = Auth::guard('employee')->user();
                
                // Check if credentials exist and employee is active
                if (!$employee->credentials) {
                    Log::error('No credentials record', ['employee_id' => $employee->employee_id]);
                    Auth::guard('employee')->logout();
                    return back()->withErrors(['email' => 'Account not properly configured.']);
                }
                
                if (!$employee->credentials->is_active) {
                    Log::warning('Inactive account attempt', ['employee_id' => $employee->employee_id]);
                    Auth::guard('employee')->logout();
                    return back()->withErrors(['email' => 'Account is inactive.']);
                }
                
                // Update last login
                $employee->credentials->update(['last_login_at' => now()]);
                
                Log::info('Login successful', ['employee_id' => $employee->employee_id]);
                return redirect()->intended(route('dashboard'));
                
            } else {
                Log::warning('Authentication failed', ['email' => $request->email]);
                
                // Check if email exists to give specific error
                $employeeExists = Employee::where('email', $request->email)->exists();
                
                if (!$employeeExists) {
                    return back()->withErrors(['email' => 'Email address not found.']);
                }
                
                return back()->withErrors(['email' => 'Invalid password.']);
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors());
            
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors(['email' => 'An error occurred. Please try again.']);
        }
    }
    
    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        try {
            Auth::guard('employee')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/');
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return redirect('/');
        }
    }
}