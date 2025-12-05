<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpActivity extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'rp_activities';
    protected $primaryKey = 'rp_activities_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'action_id',
        'external_id',
        'external_type',
        'name',
        'code',
        'description',
        'activity_title_en',
        'activity_title_ar',
        'folder_name',
        'content_network',
        'activity_type',
        'reporting_period_start',
        'reporting_period_end',
        'status',
        'achievements',
        'challenges',
        'next_steps',
        'allocated_budget',
        'spent_budget',
        'is_active',
        'needs_sync'
    ];

    protected $casts = [
        'reporting_period_start' => 'date',
        'reporting_period_end' => 'date',
        'allocated_budget' => 'decimal:2',
        'spent_budget' => 'decimal:2',
        'is_active' => 'boolean',
        'needs_sync' => 'boolean'
    ];

    /**
     * Relationships (MUST match controller usage)
     */
    public function action()
    {
        return $this->belongsTo(RpAction::class, 'action_id', 'rp_actions_id');
    }

    // Pivot tables for many-to-many relationships
    public function indicators()
    {
        return $this->belongsToMany(RpIndicator::class, 'rp_activity_indicators', 'activity_id', 'indicator_id')
            ->withTimestamps();
    }

    public function focalpoints()
    {
        return $this->belongsToMany(RpFocalpoint::class, 'rp_activity_focalpoints', 'activity_id', 'focalpoint_id')
            ->withTimestamps();
    }

    public function mappedActivities()
    {
        return $this->belongsToMany(Activity::class, 'rp_activity_mappings', 'rp_activity_id', 'main_activity_id')
            ->withTimestamps();
    }
}