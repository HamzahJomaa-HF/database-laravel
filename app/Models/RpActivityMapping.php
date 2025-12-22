<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpActivityMapping extends Model
{
    use HasUuids;

    protected $table = 'rp_activity_mappings';
    protected $primaryKey = 'rp_activity_mappings_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'rp_activities_id',    // ← FIXED: was 'rp_activity_id'
        'activity_id',         // ← Already correct
        // REMOVE ALL OTHER FIELDS - they don't exist in database!
    ];

    

    /**
     * CORRECTED Relationships
     */
    public function rpActivity()
    {
        return $this->belongsTo(RpActivity::class, 'rp_activities_id', 'rp_activities_id');
    }

    public function mainActivity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');
    }

    
    
    
    public function scopeByRpActivity($query, $rpActivityId)
    {
        return $query->where('rp_activities_id', $rpActivityId);
    }

}