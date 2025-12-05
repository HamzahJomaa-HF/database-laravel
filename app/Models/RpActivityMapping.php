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
        'rp_activity_id',
        'main_activity_id',
        'external_activity_id',
        'external_activity_code',
        'external_type',
        'external_system_name',
        'mapping_type',
        'mapping_percentage',
        'sync_direction',
        'mapping_notes',
        'sync_rules',
        'mapped_date',
        'valid_from',
        'valid_to',
        'is_active',
        'sync_pending',
        'sync_status',
        'last_sync_at',
        'last_sync_result'
    ];

    protected $casts = [
        'mapping_percentage' => 'decimal:2',
        'mapped_date' => 'date',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_active' => 'boolean',
        'sync_pending' => 'boolean',
        'last_sync_at' => 'datetime',
        'sync_rules' => 'array'
    ];

    /**
     * Relationships (MUST match controller usage)
     */
    public function rpActivity()
    {
        return $this->belongsTo(RpActivity::class, 'rp_activity_id', 'rp_activities_id');
    }

    public function mainActivity()
    {
        return $this->belongsTo(Activity::class, 'main_activity_id', 'activity_id');
    }

    /**
     * Scopes (Optional but useful for your controller queries)
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePendingSync($query)
    {
        return $query->where('sync_pending', true);
    }

    public function scopeByRpActivity($query, $rpActivityId)
    {
        return $query->where('rp_activity_id', $rpActivityId);
    }

    public function scopeByExternalType($query, $externalType)
    {
        return $query->where('external_type', $externalType);
    }
}