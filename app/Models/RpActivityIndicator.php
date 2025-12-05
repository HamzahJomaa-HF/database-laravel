<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpActivityIndicator extends Model
{
    use HasUuids;

    protected $table = 'rp_activity_indicators';
    protected $primaryKey = 'rp_activity_indicators_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'activity_id',
        'indicator_id',
        'target_value',
        'achieved_value',
        'achieved_date',
        'notes',
        'status'
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'achieved_value' => 'decimal:2',
        'achieved_date' => 'date'
    ];

    /**
     * Relationships (MUST match controller usage)
     */
    public function activity()
    {
        return $this->belongsTo(RpActivity::class, 'activity_id', 'rp_activities_id');
    }

    public function indicator()
    {
        return $this->belongsTo(RpIndicator::class, 'indicator_id', 'rp_indicators_id');
    }

    /**
     * Scopes (Optional but useful for your controller queries)
     */
    public function scopeByActivity($query, $activityId)
    {
        return $query->where('activity_id', $activityId);
    }

    public function scopeByIndicator($query, $indicatorId)
    {
        return $query->where('indicator_id', $indicatorId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}