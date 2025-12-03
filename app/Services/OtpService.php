<?php
// app/Services/OtpService.php

namespace App\Services;

use App\Models\SystemUser;
use Illuminate\Support\Facades\Log;

class OtpService
{
    /**
     * Send OTP to user (simulated - you'll integrate with email/SMS service)
     */
    public function sendOtp(SystemUser $user, string $otp): bool
    {
        // In a real application, you would:
        // 1. Send email with OTP
        // 2. Or send SMS with OTP
        // 3. Or use a service like Twilio, Nexmo, etc.
        
        // For now, we'll log it and simulate success
        Log::info("OTP for {$user->email}: {$otp}");
        
        // Example email sending (uncomment when you have mail configured):
        // Mail::to($user->email)->send(new OtpMail($otp));
        
        return true;
    }

    /**
     * Generate and send OTP to user
     */
    public function generateAndSendOtp(SystemUser $user): ?string
    {
        try {
            $otp = $user->generateOtp();
            $this->sendOtp($user, $otp);
            return $otp;
        } catch (\Exception $e) {
            Log::error('Failed to send OTP: ' . $e->getMessage());
            return null;
        }
    }
}