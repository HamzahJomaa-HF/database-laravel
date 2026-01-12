<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use SoftDeletes, HasFactory, Notifiable;

    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'employee_id',
        'project_id',
        'role_id',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'employee_type',
        'start_date',
        'end_date',
        'external_id',
        'password',
        'is_active',
        'email_verified_at',
    ];

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

            if (empty($employee->is_active)) {
                $employee->is_active = true;
            }
        });
    }

    // =============== RELATIONSHIPS ===============

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function moduleAccess()
    {
        return $this->hasMany(ModuleAccess::class, 'employee_id', 'employee_id');
    }

    // =============== MODULE ACCESS METHODS ===============

    public function hasAccess($module, $requiredLevel = 'view', $resourceId = null, $resourceType = null)
    {
        // Validate the access level
        $validLevels = ['view', 'create', 'edit', 'delete', 'manage', 'full'];
        if (!in_array($requiredLevel, $validLevels)) {
            return false;
        }
        
        // Check if employee has "all" module with full access
        $allAccess = $this->moduleAccess()
            ->where('module', 'all')
            ->where('access_level', 'full')
            ->first();
        
        if ($allAccess) {
            return true;
        }
        
        // Build query for specific access
        $query = $this->moduleAccess()->where('module', $module);
        
        if ($resourceId && $resourceType) {
            $query->where('resource_id', $resourceId)
                  ->where('resource_type', $resourceType);
        } else {
            $query->whereNull('resource_id');
        }
        
        $access = $query->first();
        
        if (!$access) {
            return false;
        }
        
        // IMPORTANT: ModuleAccess model must have hasLevel() method
        // If not, you can implement the logic here:
        $levelHierarchy = ['view' => 1, 'create' => 2, 'edit' => 3, 'delete' => 4, 'manage' => 5, 'full' => 6];
        $userLevel = $levelHierarchy[$access->access_level] ?? 0;
        $requiredLevelValue = $levelHierarchy[$requiredLevel] ?? 0;
        
        return $userLevel >= $requiredLevelValue;
    }

    // Convenience methods for specific modules
    public function hasActivityAccess($activityId, $requiredLevel = 'view')
    {
        return $this->hasAccess('activities', $requiredLevel, $activityId, 'activity');
    }

    public function hasUserAccess($userId, $requiredLevel = 'view')
    {
        return $this->hasAccess('users', $requiredLevel, $userId, 'user');
    }

    public function hasProjectAccess($projectId, $requiredLevel = 'view')
    {
        return $this->hasAccess('projects', $requiredLevel, $projectId, 'project');
    }

    public function hasProgramAccess($programId, $requiredLevel = 'view')
    {
        return $this->hasAccess('programs', $requiredLevel, $programId, 'program');
    }

    public function hasModuleAccess($module, $requiredLevel = 'view')
    {
        return $this->hasAccess($module, $requiredLevel);
    }

    // =============== HELPER METHODS ===============

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isActive()
    {
        return $this->is_active && !$this->trashed();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function getAccessibleModules()
    {
        $modules = $this->moduleAccess()
            ->whereNull('resource_id')
            ->pluck('module')
            ->toArray();
        
        if (in_array('all', $modules)) {
            return ['activities', 'users', 'projects', 'programs', 'action_plans', 'surveys', 'reports'];
        }
        
        return array_unique($modules);
    }

    public function getAccessibleResources($module, $resourceType)
    {
        return $this->moduleAccess()
            ->where('module', $module)
            ->where('resource_type', $resourceType)
            ->whereNotNull('resource_id')
            ->get()
            ->map(function ($access) {
                return $access->resource;
            })
            ->filter();
    }

    public function scopeWithModuleAccess($query, $module, $level = 'view')
    {
        $levels = ['view', 'create', 'edit', 'delete', 'manage', 'full'];
        $requiredIndex = array_search($level, $levels);
        
        return $query->whereHas('moduleAccess', function($q) use ($module, $levels, $requiredIndex) {
            $q->where('module', $module);
            
            if ($requiredIndex !== false) {
                $applicableLevels = array_slice($levels, $requiredIndex);
                $q->whereIn('access_level', $applicableLevels);
            }
        });
    }
}