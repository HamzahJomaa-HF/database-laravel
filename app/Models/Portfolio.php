<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Portfolio extends Model
{
     use SoftDeletes;
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

            // Generate sequential external_id: PORT_{YYYY}_{MM}_{sequence}
            if (empty($portfolio->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                // Get last portfolio created in this year-month
                $lastPortfolio = Portfolio::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($lastPortfolio && preg_match('/_(\d+)$/', $lastPortfolio->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $portfolio->external_id = sprintf(
                    "PORT_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
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

    // ⭐⭐⭐ MISSING: Projects Relationship ⭐⭐⭐
    /**
     * Relation to Projects (many-to-many through project_portfolios)
     */
    public function projects()
    {
        return $this->belongsToMany(
            Project::class,
            'project_portfolios', // Pivot table
            'portfolio_id',
            'project_id'
        )->withPivot('order', 'metadata') // Include pivot data
         ->withTimestamps();
    }

    /**
     * Attach/Detach helpers for activities
     */
    public function attachActivity($activityId)
    {
        $this->activities()->syncWithoutDetaching([$activityId]);
    }

    public function detachActivity($activityId)
    {
        $this->activities()->detach($activityId);
    }

    // ⭐⭐⭐ MISSING: Helper Methods for Projects ⭐⭐⭐
    
    /**
     * Attach project to portfolio
     */
    public function attachProject($projectId, $order = 0, $metadata = null)
    {
        return $this->projects()->attach($projectId, [
            'order' => $order,
            'metadata' => $metadata
        ]);
    }

    /**
     * Detach project from portfolio
     */
    public function detachProject($projectId)
    {
        return $this->projects()->detach($projectId);
    }

    /**
     * Sync portfolio projects (replace all)
     */
    public function syncProjects($projectIds)
    {
        return $this->projects()->sync($projectIds);
    }

    /**
     * Get projects ordered by pivot order
     */
    public function orderedProjects()
    {
        return $this->projects()->orderByPivot('order', 'asc');
    }
}