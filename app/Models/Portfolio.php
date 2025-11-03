<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Portfolio extends Model
{
    use HasFactory;

    protected $primaryKey = 'portfolio_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
        'type',
        'start_date',
        'end_date',
        'external_id'
    ];

    protected $dates = ['start_date', 'end_date'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($portfolio) {
            // Generate UUID for primary key
            if (empty($portfolio->portfolio_id)) {
                $portfolio->portfolio_id = (string) Str::uuid();
            }

            // Generate unique external_id
            if (empty($portfolio->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');
                $slugName = Str::slug($portfolio->name ?? 'unknown', '_');
                $random = Str::random(4);
                $portfolio->external_id = "port_{$year}_{$month}_{$slugName}_{$random}";
            }
        });
    }

    /**
     * Relation: A portfolio can have many activities (many-to-many)
     */
    public function activities()
    {
        return $this->belongsToMany(
            Activity::class,
            'portfolio_activities', // Pivot table
            'portfolio_id',
            'activity_id'
        );
    }

    /**
     * Attach/Detach helpers
     */
    public function attachActivity($activityId)
    {
        $this->activities()->syncWithoutDetaching([$activityId]);
    }

    public function detachActivity($activityId)
    {
        $this->activities()->detach($activityId);
    }
}
