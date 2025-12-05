<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpActivityFocalpoint extends Model
{
    use HasUuids;

    protected $table = 'rp_activity_focalpoints';
    protected $primaryKey = 'rp_activity_focalpoints_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'activity_id',
        'focalpoint_id',
        'role',
        'responsibilities',
        'assigned_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'end_date' => 'date'
    ];

    /**
     * Relationships (MUST match controller usage)
     */
    public function activity()
    {
        return $this->belongsTo(RpActivity::class, 'activity_id', 'rp_activities_id');
    }

    public function focalpoint()
    {
        return $this->belongsTo(RpFocalpoint::class, 'focalpoint_id', 'rp_focalpoints_id');
    }

    /**
     * Scopes (Optional but useful for your controller queries)
     */
    public function scopeByActivity($query, $activityId)
    {
        return $query->where('activity_id', $activityId);
    }

    public function scopeByFocalPoint($query, $focalpointId)
    {
        return $query->where('focalpoint_id', $focalpointId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}