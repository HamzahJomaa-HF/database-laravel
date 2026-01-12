<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = 'role_id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = ['role_name', 'description'];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    
    
    public function employees()
    {
        return $this->hasMany(Employee::class, 'role_id', 'role_id');
    }
}