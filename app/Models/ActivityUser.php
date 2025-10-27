<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityUser extends Model
{
    use HasFactory;

    protected $primaryKey = 'activity_user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'activity_id',
        'cop_id',
        'is_lead',
        'invited',
        'attended',
        'external_id', // optional external identifier
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activityUser) {
            // Generate UUID for primary key
            if (empty($activityUser->activity_user_id)) {
                $activityUser->activity_user_id = (string) Str::uuid();
            }

            // Optional: Generate external ID: au_{YYYY}_{MM}_{random}
            if (empty($activityUser->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');
                $random = Str::random(6);
                $activityUser->external_id = "au_{$year}_{$month}_{$random}";
            }
        });
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');
    }

    public function cop()
    {
        return $this->belongsTo(Cop::class, 'cop_id', 'cop_id');
    }
}
