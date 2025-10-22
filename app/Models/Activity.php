<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory;

    protected $primaryKey = 'activity_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'activity_title',
        'activity_type',
        'content_network',
        'start_date',
        'end_date',
    ];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($activity) {
        // Generate UUID for primary key
        if (empty($activity->activity_id)) {
            $activity->activity_id = (string) Str::uuid();
        }

        // Ensure start_date is a Carbon instance
        $startDate = $activity->start_date
            ? \Carbon\Carbon::parse($activity->start_date)
            : now(); // fallback if null

        // Get year and month from start_date
        $year = $startDate->format('Y');
        $month = $startDate->format('m');

        // Sanitize type (e.g. "Training Session" → "training_session")
        $type = Str::slug($activity->activity_type ?? 'unknown', '_');

        // Generate external ID
        $activity->external_id = "act_{$year}_{$month}_{$type}";
    });
}

}
