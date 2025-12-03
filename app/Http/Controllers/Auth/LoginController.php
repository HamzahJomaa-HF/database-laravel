<?php
// app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemUser;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle initial login request (username/password)
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Determine if login is email or username
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $user = SystemUser::where($loginType, $request->login)->first();

        // Check if user exists, is active, and password matches
        if (!$user || !$user->isActive() || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => __('The provided credentials are incorrect.'),
            ]);
        }

        // Check if OTP is enabled for this user
        if ($user->isOtpEnabled()) {
            // Generate and send OTP
            $otpSent = $this->otpService->generateAndSendOtp($user);
            
            if (!$otpSent) {
                throw ValidationException::withMessages([
                    'login' => __('Failed to send OTP. Please try again.'),
                ]);
            }

            // Store user ID in session for OTP verification
            Session::put('otp_user_id', $user->id);
            Session::put('otp_login_remember', $request->boolean('remember'));

            return redirect()->route('login.otp')
                ->with('success', 'OTP has been sent to your registered email.');
        }

        // If OTP is not enabled, log the user in directly
        $this->authenticateUser($user, $request->boolean('remember'));

        return redirect()->intended('/dashboard');
    }

    /**
     * Show OTP verification form
     */
    public function showOtpForm()
    {
        if (!Session::has('otp_user_id')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        return view('auth.verify-otp');
    }

    /**
     * Verify OTP and login
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = Session::get('otp_user_id');
        $remember = Session::get('otp_login_remember', false);

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = SystemUser::find($userId);

        if (!$user || !$user->isActive()) {
            Session::forget(['otp_user_id', 'otp_login_remember']);
            return redirect()->route('login')->with('error', 'User not found or inactive.');
        }

        // Verify OTP
        if (!$user->isOtpValid($request->otp)) {
            throw ValidationException::withMessages([
                'otp' => __('Invalid or expired OTP.'),
            ]);
        }

        // Clear OTP after successful verification
        $user->clearOtp();

        // Clear session
        Session::forget(['otp_user_id', 'otp_login_remember']);

        // Log the user in
        $this->authenticateUser($user, $remember);

        return redirect()->intended('/dashboard');
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        $userId = Session::get('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = SystemUser::find($userId);

        if (!$user || !$user->isActive()) {
            Session::forget(['otp_user_id', 'otp_login_remember']);
            return redirect()->route('login')->with('error', 'User not found or inactive.');
        }

        // Generate and send new OTP
        $otpSent = $this->otpService->generateAndSendOtp($user);
        
        if (!$otpSent) {
            return back()->with('error', 'Failed to resend OTP. Please try again.');
        }

        return back()->with('success', 'New OTP has been sent to your email.');
    }

    /**
     * Cancel OTP login and go back to regular login
     */
    public function cancelOtp()
    {
        Session::forget(['otp_user_id', 'otp_login_remember']);
        return redirect()->route('login');
    }

    /**
     * Helper method to authenticate user
     */
    private function authenticateUser(SystemUser $user, bool $remember = false): void
    {
        Auth::login($user, $remember);
        Session::regenerate();
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}