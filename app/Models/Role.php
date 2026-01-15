<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles'; // Make sure this matches your actual table name

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'role_id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_name', 
        'description'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the employees associated with this role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    
    public function employees()
    {
        return $this->hasMany(Employee::class, 'role_id', 'role_id');
    }
     // Add this relationship method
    // app/Models/Role.php

public function moduleAccesses()
{
    return $this->belongsToMany(
        ModuleAccess::class,
        'roles_module_access',
        'role_id',      // Foreign key on roles_module_access table
        'access_id',    // Foreign key on roles_module_access table
        'role_id',      // Local key on roles table
        'access_id'     // Local key on module_access table
    )->withTimestamps();
}
}
