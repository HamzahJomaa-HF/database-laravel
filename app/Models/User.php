<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'dob',
        'phone_number',
        'user_role',
    ];

    protected $casts = [
        'dob' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relation with roles (if you have a Role model)
    // public function role() {
    //     return $this->belongsTo(Role::class, 'user_role', 'role_id');
    // }

    // Example relation with sessions
    public function sessions() {
        return $this->hasMany(Session::class);
    }
}
