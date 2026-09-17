<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
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
        'original_name',
        'register_number',
        'marital_status',
        'employment_status',
        'passport_number',
        'register_place',
        'type', // ← ADD THIS (exists in DB)
        
        // NEW: person_id and istimara_id
        'person_id',
        'istimara_id',
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

            if (!empty($user->phone_number)) {
                $user->phone_number = self::formatPhoneNumber($user->phone_number);
            }

            // REMOVED: mobile_phone to phone_number sync logic
        });

        static::updating(function ($user) {
            // Phone numbers are only normalized on creation, not on edits —
            // an existing phone_number is left exactly as the user typed it.

            // REMOVED: mobile_phone to phone_number sync logic
        });
    }

    /**
     * Applied only when a user is created (see static::creating() above), not
     * on later edits. Lebanese local numbers starting with a trunk 0 followed
     * by 1, 3, 7 or 9 (e.g. 03098741) are stored in international form: the
     * leading 0 is dropped and the 961 country code is prefixed (e.g.
     * 9613098741). Mobile numbers already given without the trunk zero
     * (70/71/76/78/79/81...) just get the 961 country code prefixed as-is
     * (e.g. 81968927 -> 96181968927). Any other number is left exactly as
     * entered.
     */
    public static function formatPhoneNumber(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if ($digits === '') {
            return $phone;
        }

        if ($digits[0] === '0' && isset($digits[1]) && in_array($digits[1], ['1', '3', '7', '9'], true)) {
            return '961' . substr($digits, 1);
        }

        foreach (['70', '71', '76', '78', '79', '81'] as $mobilePrefix) {
            if (str_starts_with($digits, $mobilePrefix)) {
                return '961' . $digits;
            }
        }

        return $phone;
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