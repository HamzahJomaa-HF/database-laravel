<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class User extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';  // UUID primary key
    public $incrementing = false;       // Not auto-incrementing
    protected $keyType = 'string';      // UUID is a string

    protected $fillable = [
        'identification_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'nationality',
        'dob',
        'register_number',
        'phone_number',
        'marital_status',
        'current_situation',
        'passport_number',
    ];

    /**
     * Boot method to automatically generate UUID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->user_id)) {
                $user->user_id = (string) Str::uuid();
            }
        });
    }

    /**
     * Example relation: a user can have multiple sessions
     */
    public function sessions()
    {
        return $this->hasMany(Session::class, 'user_id', 'user_id');
    }

    /**
     * Example relation: user responses to surveys
     */
    public function responses()
    {
        return $this->hasMany(Response::class, 'user_id', 'user_id');
    }

    /**
     * Example relation: user diplomas
     */
    public function diplomas()
    {
        return $this->hasMany(UserDiploma::class, 'user_id', 'user_id');
    }
}
