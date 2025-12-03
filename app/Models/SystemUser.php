<?php
// app/Models/SystemUser.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class SystemUser extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $table = 'system_users';
    
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'is_active',
        'otp',
        'otp_expires_at',
        'otp_enabled',
        'auth_method'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'otp_enabled' => 'boolean',
        'otp_expires_at' => 'datetime',
    ];

    /**
     * Check if user has admin role
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if OTP is enabled for this user
     */
    public function isOtpEnabled(): bool
    {
        return $this->otp_enabled;
    }

    /**
     * Check if OTP is valid and not expired
     */
    public function isOtpValid(string $otp): bool
    {
        return $this->otp === $otp && 
               $this->otp_expires_at && 
               $this->otp_expires_at->isFuture();
    }

    /**
     * Generate and save OTP
     */
    public function generateOtp(): string
    {
        $otp = Str::random(6); // 6-digit OTP
        $this->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10), // OTP valid for 10 minutes
        ]);
        
        return $otp;
    }

    /**
     * Clear OTP after successful login
     */
    public function clearOtp(): void
    {
        $this->update([
            'otp' => null,
            'otp_expires_at' => null,
        ]);
    }

    /**
     * Get preferred authentication method
     */
    public function getAuthMethod(): string
    {
        return $this->auth_method;
    }

    /**
     * Toggle OTP authentication
     */
    public function toggleOtp(bool $enabled): void
    {
        $this->update([
            'otp_enabled' => $enabled,
            'auth_method' => $enabled ? 'otp' : 'password',
        ]);
    }
}