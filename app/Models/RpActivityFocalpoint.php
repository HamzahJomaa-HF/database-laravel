<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes; 

class RpActivityFocalpoint extends Model
{
     use SoftDeletes;
    use HasUuids;

    protected $table = 'rp_activity_focalpoints';
    protected $primaryKey = 'rp_activity_focalpoints_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'rp_activities_id',    
        'rp_focalpoints_id',  
        'role',
        'end_date',
       
    ];

    protected $casts = [
        'end_date' => 'date',
    ];

    /**
     * CORRECTED Relationships
     */
    public function activity()
    {
        return $this->belongsTo(RpActivity::class, 'rp_activities_id', 'rp_activities_id');
    }

    public function focalpoint()
    {
        return $this->belongsTo(RpFocalpoint::class, 'rp_focalpoints_id', 'rp_focalpoints_id');
    }

    /**
     * CORRECTED Scopes
     */
    public function scopeByActivity($query, $activityId)
    {
        return $query->where('rp_activities_id', $activityId);
    }

    public function scopeByFocalPoint($query, $focalpointId)
    {
        return $query->where('rp_focalpoints_id', $focalpointId);
    }

   
}