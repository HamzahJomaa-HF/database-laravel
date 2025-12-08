<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Check if this is OTP verification
        if ($request->has('otp') && $request->has('email') && $request->has('token')) {
            return $this->verifyOtp($request);
        }
        
        // Check if this is "Send OTP" request
        if ($request->has('send_otp') && $request->has('email') && $request->has('password')) {
            return $this->sendOtp($request);
        }
        
        // Otherwise, show regular form
        return view('auth.login');
    }

    private function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = SystemUser::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'error' => 'Account is deactivated'
            ], 403);
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        
        // Generate a unique token
        $sessionToken = Str::random(32);
        
        // Store OTP in user record
        $user->temp_otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->otp_session_token = $sessionToken;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'email' => $user->email,
            'token' => $sessionToken,
            'demo_otp' => $otp, // Remove in production
        ]);
    }

    private function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
            'email' => 'required|email',
            'token' => 'required|string'
        ]);

        // Find user by email AND token
        $user = SystemUser::where('email', $request->email)
            ->where('otp_session_token', $request->token)
            ->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid session'
            ], 400);
        }

        if (!$user->temp_otp) {
            return response()->json([
                'success' => false,
                'error' => 'OTP expired'
            ], 400);
        }

        // Check OTP expiry
        if (now()->greaterThan($user->otp_expires_at)) {
            // Clear OTP data
            $user->temp_otp = null;
            $user->otp_expires_at = null;
            $user->otp_session_token = null;
            $user->save();
            
            return response()->json([
                'success' => false,
                'error' => 'OTP expired'
            ], 400);
        }

        // Verify OTP
        if ($request->otp != $user->temp_otp) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid OTP'
            ], 400);
        }

        // Login user
        Auth::guard('system')->login($user);
        
        // Clear OTP data
        $user->temp_otp = null;
        $user->otp_expires_at = null;
        $user->otp_session_token = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => route('dashboard')
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('system')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('info', 'You have been logged out.');
    }
}