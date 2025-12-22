<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; 
class Activity extends Model
{
    use HasFactory;

    protected $primaryKey = 'activity_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
    'activity_title_en',
    'activity_title_ar',
    'activity_type',
    'folder_name',
    'content_network',
    'start_date',
    'end_date',
    'parent_activity',
    'target_cop',
    'external_id',
    'venue', 
    ];

    protected $dates = ['start_date', 'end_date'];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($activity) {
        if (empty($activity->activity_id)) {
            $activity->activity_id = (string) \Illuminate\Support\Str::uuid();
        }

        if (empty($activity->external_id)) {
            $year = now()->format('Y');
            $month = now()->format('m');

            // Get the last activity external_id for this month
            $lastActivity = Activity::where('external_id', 'like', "ACT_{$year}_{$month}_%")
                ->orderByRaw('CAST(SUBSTRING(external_id FROM \'[0-9]+$\') AS INTEGER) DESC')
                ->first();

            $lastNumber = 0;
            if ($lastActivity && $lastActivity->external_id && preg_match('/_(\d+)$/', $lastActivity->external_id, $matches)) {
                $lastNumber = (int) $matches[1];
            }

            $nextNumber = $lastNumber + 1;

            $activity->external_id = sprintf(
                "ACT_%s_%s_%03d",
                $year,
                $month,
                $nextNumber
            );
        }
    });
}

    /**
     * 🔗 Self-relation for parent/child activities
     */
    public function parent()
    {
        return $this->belongsTo(Activity::class, 'parent_activity', 'activity_id');
    }

    public function children()
    {
        return $this->hasMany(Activity::class, 'parent_activity', 'activity_id');
    }

    /**
     * 🔗 Many-to-Many: Activities belong to Portfolios
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

    public function attachPortfolio($portfolioId)
    {
        $this->portfolios()->syncWithoutDetaching([$portfolioId]);
    }

    public function detachPortfolio($portfolioId)
    {
        $this->portfolios()->detach($portfolioId);
    }
}
