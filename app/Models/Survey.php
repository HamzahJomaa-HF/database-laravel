<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Survey extends Model
{
    use HasFactory;

    protected $primaryKey = 'survey_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'description',
        'link',
        'is_active',
        'external_id',
        'activity_id', // foreign key
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($survey) {
            // Generate UUID for primary key
            if (empty($survey->survey_id)) {
                $survey->survey_id = (string) Str::uuid();
            }

            // Generate external ID: surv_{YYYY}_{MM}_{slug(description)}_{4 random chars}
            $year = now()->format('Y');
            $month = now()->format('m');
            $slugDesc = Str::slug(substr($survey->description ?? 'survey', 0, 20), '_'); // limit to 20 chars
            $randomSuffix = substr(Str::uuid(), 0, 4);

            $survey->external_id = "surv_{$year}_{$month}_{$slugDesc}_{$randomSuffix}";
        });
    }

    /**
     * Relation to Activity
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');
    }
}
