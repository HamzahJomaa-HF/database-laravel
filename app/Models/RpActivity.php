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
        'rp_actions_id',      // ← FIXED: was 'action_id'
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
        // REMOVE all date/budget casts - those fields don't exist!
    ];

    protected $dates = ['deleted_at'];

    /**
     * CORRECTED Relationships
     */
    public function action()
    {
        return $this->belongsTo(RpAction::class, 'rp_actions_id', 'rp_actions_id');
    }

    // CORRECT pivot relationships based on your schema:
    public function indicators()
    {
        return $this->belongsToMany(
            RpIndicator::class, 
            'rp_activity_indicators', 
            'rp_activities_id',  // Foreign key in pivot table
            'rp_indicators_id'   // Related key in pivot table
        )->withPivot('notes');
    }

    public function focalpoints()
    {
        return $this->belongsToMany(
            RpFocalpoint::class, 
            'rp_activity_focalpoints', 
            'rp_activities_id',  // Foreign key in pivot table  
            'rp_focalpoints_id'  // Related key in pivot table
        )->withPivot(['role', 'end_date']);
    }

    public function mappedActivities()
    {
        return $this->belongsToMany(
            Activity::class,
            'rp_activity_mappings',
            'rp_activities_id',  // Foreign key in pivot table
            'activity_id'        // Related key in pivot table
        );
    }
}