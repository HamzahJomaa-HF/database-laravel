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
        'folder_name',
        'content_network',
        'start_date',
        'end_date',
        'parent_activity',
        'target_cop',
        'external_id',
    ];

    protected $dates = ['start_date', 'end_date'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activity) {
            // Generate UUID for primary key
            if (empty($activity->activity_id)) {
                $activity->activity_id = (string) Str::uuid();
            }

            // Generate unique external_id like projects
        $year = now()->format('Y');
        $month = now()->format('m');
        $type = strtolower($activity->activity_type ?? 'general');
        $random = substr(Str::uuid(), 0, 4); // ensures uniqueness

        $activity->external_id = "act_{$year}_{$month}_{$type}_{$random}";
    });
    }

    /**
     * Self-relation for parent/child activities
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
     * Optional relation for target_cop if needed
     */
    // public function targetGroup()
    // {
    //     return $this->belongsTo(TargetGroup::class, 'target_cop', 'id');
    // }
    /**
 * Portfolios this activity belongs to
 */
public function portfolios()
{
    return $this->belongsToMany(
        Portfolio::class,          // Related model
        'portfolio_activities',    // Pivot table
        'activity_id',             // FK on pivot pointing to this model
        'portfolio_id'             // FK on pivot pointing to Portfolio
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
