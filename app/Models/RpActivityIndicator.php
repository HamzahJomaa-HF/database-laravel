<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes; 

class RpActivityIndicator extends Model
{
     use SoftDeletes;
    use HasUuids;

    protected $table = 'rp_activity_indicators';
    protected $primaryKey = 'rp_activity_indicators_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'rp_activities_id',    // ← FIXED: was 'activity_id'
        'rp_indicators_id',    // ← FIXED: was 'indicator_id'
        'notes',
    ];

    

    /**
     * CORRECTED Relationships
     */
    public function activity()
    {
        return $this->belongsTo(RpActivity::class, 'rp_activities_id', 'rp_activities_id');
    }

    public function indicator()
    {
        return $this->belongsTo(RpIndicator::class, 'rp_indicators_id', 'rp_indicators_id');
    }

    /**
     * CORRECTED Scopes
     */
    public function scopeByActivity($query, $activityId)
    {
        return $query->where('rp_activities_id', $activityId);
    }

    public function scopeByIndicator($query, $indicatorId)
    {
        return $query->where('rp_indicators_id', $indicatorId);
    }

   
}