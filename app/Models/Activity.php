<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $primaryKey = 'activity_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'activities';

    protected $fillable = [
        'external_id',
        'folder_name',
        'activity_title_en',
        'activity_title_ar',
        'activity_type',
        'content_network',
        'start_date',
        'end_date',
        'parent_activity',
        'target_cop',
        'operational_support',
        'venue',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'operational_support' => 'array', // json <-> array
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activity) {
            if (empty($activity->activity_id)) {
                $activity->activity_id = (string) \Illuminate\Support\Str::uuid();
            }

            // auto-generate external_id if not provided
            if (empty($activity->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                $lastActivity = self::where('external_id', 'like', "ACT_{$year}_{$month}_%")
                    ->orderByRaw("CAST(SUBSTRING(external_id FROM '[0-9]+$') AS INTEGER) DESC")
                    ->first();

                $lastNumber = 0;
                if ($lastActivity && $lastActivity->external_id && preg_match('/_(\d+)$/', $lastActivity->external_id, $m)) {
                    $lastNumber = (int) $m[1];
                }

                $activity->external_id = sprintf("ACT_%s_%s_%03d", $year, $month, $lastNumber + 1);
            }
        });
    }

    /**
     * Self relation
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_activity', 'activity_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_activity', 'activity_id');
    }

    /**
     * Many-to-many portfolios
     */
    public function portfolios()
    {
        return $this->belongsToMany(
            Portfolio::class,
            'portfolio_activities',
            'activity_id',
            'portfolio_id'
        );
    }
}
