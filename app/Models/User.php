<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        // Required Fields
        'prefix',
        'is_high_profile',
        'scope',
        'default_cop_id', // Foreign key to cops table
        'first_name',
        'last_name',
        'gender',
        'position_1',
        'organization_1',
        'organization_type_1',
        'status_1',
        'address',
        'phone_number', // CHANGED: from mobile_phone to phone_number
        
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
        
        // Existing fields for backward compatibility
        'identification_id',
        'mother_name',
        'register_number',
        // REMOVED: 'phone_number', (duplicate)
        'marital_status',
        'employment_status',
        'passport_number',
        'register_place',
        'type',
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
    public const GENDER_OTHER = 'Other';

    public const TYPE_STAKEHOLDER = 'Stakeholder';
    public const TYPE_EMPLOYEE = 'Employee';
    public const TYPE_ADMIN = 'Admin';
    public const TYPE_CUSTOMER = 'Customer';
    public const TYPE_PARTNER = 'Partner';
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

    public function nationalities()
    {
        return $this->belongsToMany(Nationality::class, 'users_nationality', 'user_id', 'nationality_id')
                    ->withTimestamps();
    }

    public function defaultCop()
    {
        return $this->belongsTo(Cop::class, 'default_cop_id', 'cop_id');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        $parts = [];
        if ($this->prefix) {
            $parts[] = $this->prefix;
        }
        if ($this->first_name) {
            $parts[] = $this->first_name;
        }
        if ($this->middle_name) {
            $parts[] = $this->middle_name;
        }
        if ($this->last_name) {
            $parts[] = $this->last_name;
        }
        
        return implode(' ', $parts);
    }

    // CHANGED: Renamed from getFormattedMobilePhoneAttribute
    public function getFormattedPhoneNumberAttribute()
    {
        if (!$this->phone_number) {
            return null;
        }
        
        // Format Lebanese phone numbers
        $phone = preg_replace('/[^\d+]/', '', $this->phone_number);
        
        if (preg_match('/^\+961(\d{8})$/', $phone, $matches)) {
            return '+961 ' . substr($matches[1], 0, 2) . ' ' . substr($matches[1], 2, 3) . ' ' . substr($matches[1], 5, 3);
        }
        
        return $this->phone_number;
    }

    public function getFormattedOfficePhoneAttribute()
    {
        if (!$this->office_phone) {
            return null;
        }
        
        $phone = preg_replace('/[^\d]/', '', $this->office_phone);
        if (strlen($phone) === 8) {
            return '01 ' . substr($phone, 0, 3) . ' ' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 2);
        }
        
        return $this->office_phone;
    }

    public function getAgeAttribute()
    {
        if (!$this->dob) {
            return null;
        }
        
        return $this->dob->age;
    }

    // Accessor for backward compatibility (if you still need community_of_practice)
    public function getCommunityOfPracticeAttribute()
    {
        return $this->defaultCop ? $this->defaultCop->cop_name : null;
    }

    // Scopes for filtering
    public function scopeHighProfile($query)
    {
        return $query->where('is_high_profile', true);
    }

    public function scopeByScope($query, $scope)
    {
        return $query->where('scope', $scope);
    }

    public function scopeByDefaultCop($query, $copId)
    {
        return $query->where('default_cop_id', $copId);
    }

    public function scopeByOrganizationType($query, $type)
    {
        return $query->where(function($q) use ($type) {
            $q->where('organization_type_1', $type)
              ->orWhere('organization_type_2', $type);
        });
    }

    public function scopeBySector($query, $sector)
    {
        return $query->where('sector', $sector);
    }

    public function scopeSearchByName($query, $name)
    {
        return $query->where(function ($q) use ($name) {
            $q->where('first_name', 'ilike', "%$name%")
              ->orWhere('middle_name', 'ilike', "%$name%")
              ->orWhere('last_name', 'ilike', "%$name%")
              ->orWhere('mother_name', 'ilike', "%$name%");
        });
    }

    public function scopeSearchByOrganization($query, $organization)
    {
        return $query->where(function ($q) use ($organization) {
            $q->where('organization_1', 'ilike', "%$organization%")
              ->orWhere('organization_2', 'ilike', "%$organization%");
        });
    }

    public function scopeSearchByPosition($query, $position)
    {
        return $query->where(function ($q) use ($position) {
            $q->where('position_1', 'ilike', "%$position%")
              ->orWhere('position_2', 'ilike', "%$position%");
        });
    }

    // UPDATED: Removed mobile_phone from search
    public function scopeSearchByPhone($query, $phone)
    {
        return $query->where(function ($q) use ($phone) {
            $q->where('phone_number', 'ilike', "%$phone%") // CHANGED: from mobile_phone to phone_number
              ->orWhere('office_phone', 'ilike', "%$phone%")
              ->orWhere('home_phone', 'ilike', "%$phone%");
            // REMOVED: orWhere('phone_number', 'ilike', "%$phone%")
        });
    }

    // Helper methods
    public function hasSecondaryPosition()
    {
        return !empty($this->position_2) || !empty($this->organization_2);
    }

    public function getPrimaryOrganization()
    {
        return [
            'position' => $this->position_1,
            'organization' => $this->organization_1,
            'type' => $this->organization_type_1,
            'status' => $this->status_1
        ];
    }

    public function getSecondaryOrganization()
    {
        if (!$this->hasSecondaryPosition()) {
            return null;
        }
        
        return [
            'position' => $this->position_2,
            'organization' => $this->organization_2,
            'type' => $this->organization_type_2,
            'status' => $this->status_2
        ];
    }

    public function getAllOrganizations()
    {
        $organizations = [$this->getPrimaryOrganization()];
        
        if ($secondary = $this->getSecondaryOrganization()) {
            $organizations[] = $secondary;
        }
        
        return $organizations;
    }

    public function isInternational()
    {
        return $this->scope === self::SCOPE_INTERNATIONAL;
    }

    public function isRegional()
    {
        return $this->scope === self::SCOPE_REGIONAL;
    }

    public function isNational()
    {
        return $this->scope === self::SCOPE_NATIONAL;
    }

    public function isLocal()
    {
        return $this->scope === self::SCOPE_LOCAL;
    }

    public function isHighProfile()
    {
        return $this->is_high_profile === true;
    }

    // Static methods for dropdowns
    public static function getScopeOptions()
    {
        return [
            self::SCOPE_INTERNATIONAL => 'International',
            self::SCOPE_REGIONAL => 'Regional',
            self::SCOPE_NATIONAL => 'National',
            self::SCOPE_LOCAL => 'Local',
        ];
    }

    public static function getGenderOptions()
    {
        return [
            self::GENDER_MALE => 'Male',
            self::GENDER_FEMALE => 'Female',
            self::GENDER_OTHER => 'Other',
        ];
    }

    public static function getOrganizationTypeOptions()
    {
        return [
            self::ORG_TYPE_PUBLIC => 'Public Sector',
            self::ORG_TYPE_PRIVATE => 'Private Sector',
            self::ORG_TYPE_ACADEMIA => 'Academia',
            self::ORG_TYPE_UN => 'UN',
            self::ORG_TYPE_INGOS => 'INGOs',
            self::ORG_TYPE_CIVIL_SOCIETY => 'Civil Society',
            self::ORG_TYPE_NGOS => 'NGOs',
            self::ORG_TYPE_ACTIVIST => 'Activist',
        ];
    }

    public static function getUserTypeOptions()
    {
        return [
            self::TYPE_STAKEHOLDER => 'Stakeholder',
            self::TYPE_EMPLOYEE => 'Employee',
            self::TYPE_ADMIN => 'Admin',
            self::TYPE_CUSTOMER => 'Customer',
            self::TYPE_PARTNER => 'Partner',
            self::TYPE_BENEFICIARY => 'Beneficiary',
        ];
    }

    // Authentication methods override
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    public function getAuthIdentifier()
    {
        return $this->user_id;
    }

    public function getAuthPassword()
    {
        return $this->password ?? null;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}