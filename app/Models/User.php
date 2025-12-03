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
        // Required Fields (X)
        'prefix',
        'is_high_profile',
        'scope',
        'community_of_practice',
        'first_name',
        'last_name', // Using last_name instead of family_name
        'gender',
        'position_1',
        'organization_1',
        'organization_type_1',
        'status_1',
        'address',
        'mobile_phone',
        
        // Optional Fields
        'sector',
        'middle_name', // Using middle_name instead of father_name
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
        'phone_number',
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

    // Add these for scope ENUM values
    public const SCOPE_INTERNATIONAL = 'International';
    public const SCOPE_REGIONAL = 'Regional';
    public const SCOPE_NATIONAL = 'National';
    public const SCOPE_LOCAL = 'Local';

    // Organization types
    public const ORG_TYPE_PUBLIC = 'Public Sector';
    public const ORG_TYPE_PRIVATE = 'Private Sector';
    public const ORG_TYPE_ACADEMIA = 'Academia';
    public const ORG_TYPE_UN = 'UN';
    public const ORG_TYPE_INGOS = 'INGOs';
    public const ORG_TYPE_CIVIL_SOCIETY = 'Civil Society';
    public const ORG_TYPE_NGOS = 'NGOs';
    public const ORG_TYPE_ACTIVIST = 'Activist';

    // Gender options
    public const GENDER_MALE = 'Male';
    public const GENDER_FEMALE = 'Female';
    public const GENDER_OTHER = 'Other';

    // User types (for backward compatibility)
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

            // Validate required fields
            $requiredFields = [
                'scope' => 'Scope',
                'community_of_practice' => 'Community of Practice',
                'first_name' => 'First Name',
                'last_name' => 'Last Name', // Changed from family_name
                'gender' => 'Gender',
                'position_1' => 'Position 1',
                'organization_1' => 'Organization 1',
                'organization_type_1' => 'Type of Organization 1',
                'status_1' => 'Status 1',
                'address' => 'Address',
                'mobile_phone' => 'Mobile Phone',
            ];

            $errors = [];
            foreach ($requiredFields as $field => $fieldName) {
                if (empty($user->$field)) {
                    $errors[$field] = "{$fieldName} is required.";
                }
            }

            // Validate ENUM fields
            if ($user->scope && !in_array($user->scope, [
                self::SCOPE_INTERNATIONAL,
                self::SCOPE_REGIONAL,
                self::SCOPE_NATIONAL,
                self::SCOPE_LOCAL
            ])) {
                $errors['scope'] = 'Invalid scope value.';
            }

            if ($user->gender && !in_array($user->gender, [
                self::GENDER_MALE,
                self::GENDER_FEMALE,
                self::GENDER_OTHER
            ])) {
                $errors['gender'] = 'Invalid gender value.';
            }

            if ($user->organization_type_1 && !in_array($user->organization_type_1, [
                self::ORG_TYPE_PUBLIC,
                self::ORG_TYPE_PRIVATE,
                self::ORG_TYPE_ACADEMIA,
                self::ORG_TYPE_UN,
                self::ORG_TYPE_INGOS,
                self::ORG_TYPE_CIVIL_SOCIETY,
                self::ORG_TYPE_NGOS,
                self::ORG_TYPE_ACTIVIST
            ])) {
                $errors['organization_type_1'] = 'Invalid organization type 1 value.';
            }

            if ($user->organization_type_2 && !in_array($user->organization_type_2, [
                self::ORG_TYPE_PUBLIC,
                self::ORG_TYPE_PRIVATE,
                self::ORG_TYPE_ACADEMIA,
                self::ORG_TYPE_UN,
                self::ORG_TYPE_INGOS,
                self::ORG_TYPE_CIVIL_SOCIETY,
                self::ORG_TYPE_NGOS,
                self::ORG_TYPE_ACTIVIST
            ])) {
                $errors['organization_type_2'] = 'Invalid organization type 2 value.';
            }

            if (!empty($errors)) {
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
            }

            // Copy mobile_phone to phone_number for backward compatibility if phone_number is empty
            if (empty($user->phone_number) && !empty($user->mobile_phone)) {
                $user->phone_number = $user->mobile_phone;
            }
        });

        static::updating(function ($user) {
            // Sync mobile_phone with phone_number for backward compatibility
            if ($user->isDirty('mobile_phone')) {
                $user->phone_number = $user->mobile_phone;
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

    public function getFormattedMobilePhoneAttribute()
    {
        if (!$this->mobile_phone) {
            return null;
        }
        
        // Format Lebanese phone numbers
        $phone = preg_replace('/[^\d+]/', '', $this->mobile_phone);
        
        if (preg_match('/^\+961(\d{8})$/', $phone, $matches)) {
            return '+961 ' . substr($matches[1], 0, 2) . ' ' . substr($matches[1], 2, 3) . ' ' . substr($matches[1], 5, 3);
        }
        
        return $this->mobile_phone;
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

    // Scopes for filtering
    public function scopeHighProfile($query)
    {
        return $query->where('is_high_profile', true);
    }

    public function scopeByScope($query, $scope)
    {
        return $query->where('scope', $scope);
    }

    public function scopeByCommunity($query, $community)
    {
        return $query->where('community_of_practice', $community);
    }

    public function scopeByOrganizationType($query, $type)
    {
        return $query->where('organization_type_1', $type)
                    ->orWhere('organization_type_2', $type);
    }

    public function scopeBySector($query, $sector)
    {
        return $query->where('sector', $sector);
    }

    public function scopeSearchByName($query, $name)
    {
        return $query->where(function ($q) use ($name) {
            $q->where('first_name', 'ilike', "%$name%")
              ->orWhere('middle_name', 'ilike', "%$name%") // Changed from father_name
              ->orWhere('last_name', 'ilike', "%$name%") // Changed from family_name
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

    public function scopeSearchByPhone($query, $phone)
    {
        return $query->where(function ($q) use ($phone) {
            $q->where('mobile_phone', 'ilike', "%$phone%")
              ->orWhere('office_phone', 'ilike', "%$phone%")
              ->orWhere('home_phone', 'ilike', "%$phone%")
              ->orWhere('phone_number', 'ilike', "%$phone%");
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
        // If you have a password field
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