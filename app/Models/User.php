<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
     use SoftDeletes;
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

   protected $fillable = [
    // Required Fields
    'prefix',
    'is_high_profile',
    'scope',
    'default_cop_id',
    'first_name',
    'last_name',
    'gender',
    'position_1',
    'organization_1',
    'organization_type_1',
    'status_1',
    'address',
    'phone_number',
    
    // Optional Fields
    'sector',
    'middle_name',
    'dob',
    'office_phone',
    'extension_number',
    'home_phone',
    'email',
    
    // Optional Secondary Position Fields
    'position_2',
    'organization_2',
    'organization_type_2',
    'status_2',
    
    // Other fields
    'identification_id',
    'mother_name',
    'register_number',
    'marital_status',
    'employment_status',
    'passport_number',
    'register_place',
    'type', // ← ADD THIS (exists in DB)
];
    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
        'is_high_profile' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constants for ENUM values
    public const SCOPE_INTERNATIONAL = 'International';
    public const SCOPE_REGIONAL = 'Regional';
    public const SCOPE_NATIONAL = 'National';
    public const SCOPE_LOCAL = 'Local';

    public const ORG_TYPE_PUBLIC = 'Public Sector';
    public const ORG_TYPE_PRIVATE = 'Private Sector';
    public const ORG_TYPE_ACADEMIA = 'Academia';
    public const ORG_TYPE_UN = 'UN';
    public const ORG_TYPE_INGOS = 'INGOs';
    public const ORG_TYPE_CIVIL_SOCIETY = 'Civil Society';
    public const ORG_TYPE_NGOS = 'NGOs';
    public const ORG_TYPE_ACTIVIST = 'Activist';

    public const GENDER_MALE = 'Male';
    public const GENDER_FEMALE = 'Female';
    
    public const TYPE_STAKEHOLDER = 'Stakeholder';
    public const TYPE_BENEFICIARY = 'Beneficiary';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->user_id)) {
                $user->user_id = (string) Str::uuid();
            }

            // Set default for high profile if not provided
            if (!isset($user->is_high_profile)) {
                $user->is_high_profile = false;
            }

            // Set default type if not provided
            if (empty($user->type)) {
                $user->type = self::TYPE_STAKEHOLDER;
            }
            
            // REMOVED: mobile_phone to phone_number sync logic
        });

        static::updating(function ($user) {
            // REMOVED: mobile_phone to phone_number sync logic
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
                    ->withTimestamps();
    }
    public function defaultCop()
{
    return $this->belongsTo(Cop::class, 'default_cop_id', 'cop_id');
}
public function nationalities()
{
    return $this->belongsToMany(Nationality::class, 'users_nationality', 'user_id', 'nationality_id');
}
}