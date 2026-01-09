<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
     use SoftDeletes;
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

            // Generate sequential external_id: SURV_{YYYY}_{MM}_{sequence}
            if (empty($survey->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                // Get last Survey created in this year-month
                $last = Survey::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($last && preg_match('/_(\d+)$/', $last->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $survey->external_id = sprintf(
                    "SURV_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
            }
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
