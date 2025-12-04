<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemUser;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check credentials in system_users table
        $user = SystemUser::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        // Check if user is active
        if (!$user->is_active) {
            return back()->withErrors(['email' => 'Account is deactivated']);
        }

        // Generate OTP
        $otp = rand(100000, 999999);
        
        // Store OTP in session (for demo, in production send via email/SMS)
        session([
            'pending_otp' => $otp,
            'pending_user_id' => $user->id,
            'otp_expiry' => now()->addMinutes(10),
        ]);

        // Show OTP for demo (remove in production)
        session()->flash('demo_otp', $otp);
        
        return redirect()->route('verify.otp.form');
    }

    public function showOtpForm()
    {
        if (!session('pending_otp')) {
            return redirect()->route('login');
        }
        
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        if (!session('pending_otp') || !session('pending_user_id')) {
            return redirect()->route('login')->withErrors(['otp' => 'Session expired']);
        }

        // Check OTP expiry
        if (now()->greaterThan(session('otp_expiry'))) {
            session()->forget(['pending_otp', 'pending_user_id', 'otp_expiry']);
            return redirect()->route('login')->withErrors(['otp' => 'OTP expired']);
        }

        // Verify OTP
        if ($request->otp != session('pending_otp')) {
            return back()->withErrors(['otp' => 'Invalid OTP']);
        }

        // Login user
        $user = SystemUser::find(session('pending_user_id'));
        Auth::guard('system')->login($user);

        // Clear OTP session
        session()->forget(['pending_otp', 'pending_user_id', 'otp_expiry']);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('system')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}