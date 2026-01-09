<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleAccess extends Model
{
    protected $table = 'module_access'; 
    protected $primaryKey = 'access_id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'employee_id', 
        'module', 
        'resource_id', 
        'resource_type',
        'access_level'
    ];
    
    protected $casts = [
        'access_level' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relationship with employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
    
    // Polymorphic relationship to access resource (activity, user, project, etc.)
    public function resource()
    {
        return $this->morphTo();
    }
    
    // Scope for module access
    public function scopeForModule($query, $module)
    {
        return $query->where('module', $module);
    }
    
    // Scope for resource access
    public function scopeForResource($query, $resourceId, $resourceType)
    {
        return $query->where('resource_id', $resourceId)
                     ->where('resource_type', $resourceType);
    }
    
    // Check if access level is sufficient
    public function hasLevel($requiredLevel)
    {
        $hierarchy = ['none', 'view', 'create', 'edit', 'delete', 'manage', 'full'];
        $currentIndex = array_search($this->access_level, $hierarchy);
        $requiredIndex = array_search($requiredLevel, $hierarchy);
        
        return $currentIndex !== false && $requiredIndex !== false && $currentIndex >= $requiredIndex;
    }
}