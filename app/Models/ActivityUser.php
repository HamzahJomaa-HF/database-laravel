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

            // Generate sequential external ID like Program model: au_{YYYY}_{MM}_{sequence}
            if (empty($activityUser->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                // Get last activity user created in this year-month
                $lastActivityUser = ActivityUser::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Extract last sequence number safely
                $lastNumber = 0;
                if ($lastActivityUser && preg_match('/_(\d+)$/', $lastActivityUser->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $activityUser->external_id = sprintf(
                    "AU_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
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
