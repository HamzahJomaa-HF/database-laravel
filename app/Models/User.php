<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'email',      // Add email field if you have it
        'password',   // Add password field if you have it
    ];

    protected $hidden = [
        'password', // Hide password if you have it
        'remember_token', // Add if using remember me
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

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'user_id'; // Tell Laravel to use user_id as the identifier
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        // If you don't have a password column, you might need to handle this differently
        return $this->password ?? null;
    }

    /**
     * Get the "remember me" token value.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the "remember me" token value.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}