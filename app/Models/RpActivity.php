<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpActivity extends Model
{
     use SoftDeletes;
    use SoftDeletes, HasUuids;

    protected $table = 'rp_activities';
    protected $primaryKey = 'rp_activities_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'rp_actions_id',
        'external_id',
        'external_type',
        'name',
        'code',
        'description',
        'activity_type',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Relationships
     */
    public function action()
    {
        return $this->belongsTo(RpAction::class, 'rp_actions_id', 'rp_actions_id');
    }

    // Add this method to get component through action
    public function component()
    {
        return $this->action ? $this->action->component : null;
    }

    // Keep pivot relationships if needed
    public function indicators()
    {
        return $this->belongsToMany(
            RpIndicator::class, 
            'rp_activity_indicators', 
            'rp_activities_id',
            'rp_indicators_id'
        )->withPivot('notes');
    }

    public function focalpoints()
    {
        return $this->belongsToMany(
            RpFocalpoint::class, 
            'rp_activity_focalpoints', 
            'rp_activities_id',  
            'rp_focalpoints_id'
        )->withPivot(['role', 'end_date']);
    }

    public function mappedActivities()
    {
        return $this->belongsToMany(
            Activity::class,
            'rp_activity_mappings',
            'rp_activities_id',
            'activity_id'
        );
    }
}