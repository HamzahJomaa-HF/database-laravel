<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Hash;

class Employee extends Authenticatable
{
    use SoftDeletes, HasFactory, Notifiable;

    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'employee_id',
        'role_id',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'employee_type',
        'start_date',
        'end_date',
        'external_id',
    ];

    // ✅ CRITICAL: Override to get password from credentials table
    public function getAuthPassword()
    {
        return $this->credentials ? $this->credentials->password_hash : null;
    }

    // ✅ Check if employee account is active
    public function isActive(): bool
    {
        return $this->credentials && $this->credentials->is_active;
    }

    // ✅ SIMPLIFIED: Get accessible modules
    public function getAccessibleModules()
    {
        if (!$this->role_id) {
            return collect();
        }
        
        return $this->role->moduleAccesses;
    }
     public function hasFullAccess(): bool
    {
       if (!$this->role_id) {
        return false;
    }
    
    // Option 1: Check if role name contains 'admin' (case-insensitive)
    if ($this->role && stripos($this->role->role_name, 'admin') !== false) {
        return true;
    }
    
    // Option 2: Check if the role has 'full' access on at least 3 major modules
    $majorModules = ['Users', 'Projects', 'Programs', 'Activities', 'Reports', 'Dashboard'];
    $fullAccessCount = 0;
    
    foreach ($majorModules as $module) {
        if ($this->hasPermission($module, 'full')) {
            $fullAccessCount++;
        }
    }
    
      // If user has 'full' access on 4 or more major modules, consider it full access
      return $fullAccessCount >= 4;
}
    // ✅ SIMPLIFIED: Check if employee has specific permission
   public function hasPermission($module, $accessLevel = null): bool
{
    if (!$this->role_id) {
        return false;
    }
    
    // Try to find module in both cases
    $moduleAccess = $this->role->moduleAccesses
        ->where('module', $module)
        ->first();
    
    // If not found, try with capitalized version
    if (!$moduleAccess) {
        $moduleAccess = $this->role->moduleAccesses
            ->where('module', ucfirst($module))
            ->first();
    }
    
    // If still not found, try with lowercase version
    if (!$moduleAccess) {
        $moduleAccess = $this->role->moduleAccesses
            ->where('module', strtolower($module))
            ->first();
    }
    
    if (!$moduleAccess) {
        return false;
    }
    
    // If no specific access level required, just module access is enough
    if (!$accessLevel) {
        return true;
    }
    
    // Check specific access level
    return $accessLevel === '*' || $moduleAccess->access_level === $accessLevel;
}
    // ✅ Check if employee can access module (alias for hasPermission)
    public function canAccessModule($module): bool
    {
        return $this->hasPermission($module);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->employee_id)) {
                $employee->employee_id = (string) Str::uuid();
            }

            if (empty($employee->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                $lastEmployee = Employee::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($lastEmployee && preg_match('/_(\d+)$/', $lastEmployee->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $employee->external_id = sprintf(
                    "EMP_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
            }
        });
        
    }

    // =============== CORRECTED RELATIONSHIPS ===============

    public function projectEmployees(): HasMany
    {
        return $this->hasMany(ProjectEmployee::class, 'employee_id', 'employee_id');
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            Project::class,
            'project_employees',
            'employee_id',
            'project_id',
            'employee_id',
            'project_id'
        );
    }

    // ✅ CORRECTED: Use BelongsTo relationship
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
    
    // ✅ REMOVED: Complex hasManyThrough (not needed)
    // The roleModuleAccesses() method was overly complex

    // ✅ CORRECTED: Single credential relationship
    public function credentials(): HasOne
    {
        return $this->hasOne(CredentialsEmployee::class, 'employee_id', 'employee_id');
    }

    // ✅ Helper method to check role
    public function hasRole($roleName): bool
    {
        return $this->role && $this->role->role_name === $roleName;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}