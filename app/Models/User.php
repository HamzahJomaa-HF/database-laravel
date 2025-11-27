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
        'email',
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
            if (empty($user->user_id)) {
                $user->user_id = (string) Str::uuid();
            }

            if (empty($user->type)) {
                $user->type = 'Stakeholder';
            }

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
        return $this->belongsToMany(Diploma::class, 'users_diploma', 'user_id', 'diploma_id')
                    ->withPivot('created_at', 'updated_at')
                    ->withTimestamps();
    }

    public function nationalities()
    {
        return $this->belongsToMany(Nationality::class, 'users_nationality', 'user_id', 'nationality_id')
                    ->withPivot('created_at', 'updated_at')
                    ->withTimestamps();
    }
}