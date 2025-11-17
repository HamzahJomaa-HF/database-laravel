<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
        'type',
        'external_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->activity_user_id)) {
                $model->activity_user_id = (string) Str::uuid();
            }
            
            // Generate external_id in AU_YYYY_MM_001 format if not provided
            if (empty($model->external_id)) {
                $model->external_id = self::generateSequentialId();
            }
        });
    }

    /**
     * Generate sequential external_id in AU_YYYY_MM_001 format
     */
    protected static function generateSequentialId()
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        
        // Find the highest sequence number for this year and month
        $lastRecord = static::where('external_id', 'like', "AU_{$year}_{$month}_%")
            ->orderBy('external_id', 'desc')
            ->first();
        
        if ($lastRecord) {
            // Extract the number part and increment
            $parts = explode('_', $lastRecord->external_id);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return sprintf("AU_%s_%s_%03d", $year, $month, $nextNumber);
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