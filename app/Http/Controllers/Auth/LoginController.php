<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // If already logged in as employee, redirect to dashboard
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
        // Validate credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        // Remember me checkbox
        $remember = $request->filled('remember');
        
        // Attempt login with EMPLOYEE guard
        if (Auth::guard('employee')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Check if employee is active
            $employee = Auth::guard('employee')->user();
            if (!$employee->isActive()) {
                Auth::guard('employee')->logout();
                return back()->withErrors([
                    'email' => 'Your account is inactive. Please contact administrator.',
                ]);
            }
            
            return redirect()->intended(route('dashboard'));
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    
    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}