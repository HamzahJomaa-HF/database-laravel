<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ModuleAccess extends Model
{
    use SoftDeletes;

    protected $table = 'module_access';
    protected $primaryKey = 'access_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'module',
        'access_level',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->access_id)) {
                $model->access_id = (string) Str::uuid();
            }
        });
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_module_access', 'access_id', 'role_id')
            ->withPivot('roles_module_access_id')
            ->withTimestamps();
    }
}