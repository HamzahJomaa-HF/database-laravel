<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'type',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Generate UUID if not already set
            if (empty($user->user_id)) {
                $user->user_id = (string) Str::uuid();
            }

            // Set default type to Stakeholder if not provided
            if (empty($user->type)) {
                $user->type = 'Stakeholder';
            }

            // REMOVE the complex validation logic below - only require first_name and last_name
            if (empty($user->first_name) || empty($user->last_name)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'first_name' => 'First name is required.',
                    'last_name' => 'Last name is required.'
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