<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CredentialsEmployeeController extends Controller
{
    /**
     * Generate a new random password for the employee and email it to them.
     */
    public function resetPassword(Employee $employee)
    {
        $newPassword = Str::password(12);

        if ($employee->credentials) {
            $employee->credentials->update([
                'password_hash' => Hash::make($newPassword),
            ]);
        } else {
            $employee->credentials()->create([
                'password_hash' => Hash::make($newPassword),
                'is_active' => true,
            ]);
        }

        if ($employee->email) {
            try {
                Mail::raw(
                    "Hello {$employee->first_name},\n\nYour password has been reset. Your new password is: {$newPassword}\n\nPlease log in and change it as soon as possible.",
                    function ($message) use ($employee) {
                        $message->to($employee->email)
                            ->subject('Your Password Has Been Reset');
                    }
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send password reset email: ' . $e->getMessage());
            }
        }

        return redirect()->back()
            ->with('success', "Password reset successfully. New password: {$newPassword}");
    }
}
