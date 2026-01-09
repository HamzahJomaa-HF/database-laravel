<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = 'permission_id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = ['name', 'group', 'description'];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    // Relationship with roles
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id')
                    ->withTimestamps();
    }
    
    // Relationship with employees
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_permissions', 'permission_id', 'employee_id')
                    ->withPivot('is_granted')
                    ->withTimestamps();
    }
    
    // Helper method to check if permission grants module access
    public function isModuleAccessPermission()
    {
        return str_starts_with($this->name, 'access-');
    }
    
    // Get module name from permission (for access-* permissions)
    public function getModuleAttribute()
    {
        if (str_starts_with($this->name, 'access-')) {
            return substr($this->name, 7); // Remove 'access-' prefix
        }
        return null;
    }
}