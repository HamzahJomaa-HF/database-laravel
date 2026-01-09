<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Role extends Model
{
     use SoftDeletes;
    use HasFactory;

    protected $primaryKey = 'role_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'role_name',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            // Generate UUID for primary key
            if (empty($role->role_id)) {
                $role->role_id = (string) Str::uuid();
            }

            // Optional: You could also create a slug or code for the role
            // $role->external_id = "role_" . Str::slug($role->role_name ?? 'unknown', '_');
        });
    }

    /**
     * Example: If you want to relate Role to Users
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
