<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class User extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';  
    public $incrementing = false;       
    protected $keyType = 'string';      

    protected $fillable = [
        'identification_id',
        'first_name',
        'middle_name',
        'mother_name',
        'last_name',
        'gender',
        'dob',
        'register_number',
        'phone_number',
        'marital_status',
        'employment_status',
        'passport_number',
        'register_place',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Generate UUID if not already set
            if (empty($user->user_id)) {
                $user->user_id = (string) Str::uuid();
            }

            // Check the creation logic
            $condition1 = !empty($user->identification_id);
            $condition2 = !empty($user->passport_number);
            $condition3 = (
                !empty($user->dob) &&
                !empty($user->phone_number) &&
                !empty($user->first_name) &&
                !empty($user->middle_name) &&
                !empty($user->last_name)
            );
            $condition4 = (
                !empty($user->register_number) &&
                !empty($user->register_place) &&
                !empty($user->first_name) &&
                !empty($user->middle_name) &&
                !empty($user->last_name) &&
                !empty($user->dob)
            );

            if (!($condition1 || $condition2 || $condition3 || $condition4)) {
                throw ValidationException::withMessages([
                    'user' => 'User creation requires either identification_id, passport_number, 
                    (dob, phone_number, first_name, middle_name, last_name), 
                    or (register_number, register_place, first_name, middle_name, last_name, dob).'
                ]);
            }
        });
    }

    // Relationships
    public function sessions()
    {
        return $this->hasMany(Session::class, 'user_id', 'user_id');
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'user_id', 'user_id');
    }

    public function diplomas()
    {
        return $this->belongsToMany(Diploma::class, 'users_diploma', 'user_id', 'diploma_id');
    }
}
