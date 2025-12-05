<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpAction extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'rp_actions';
    protected $primaryKey = 'rp_actions_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'unit_id',
        'external_id',
        'name',
        'code',
        'description',
        'planned_start_date',
        'planned_end_date',
        'status',
        'allocated_budget',
        'is_active'
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'allocated_budget' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Relationships (OBLIGATORY for Eloquent)
     */
    public function unit()
    {
        return $this->belongsTo(RpUnit::class, 'unit_id', 'rp_units_id');
    }

    public function activities()
    {
        return $this->hasMany(RpActivity::class, 'action_id', 'rp_actions_id');
    }

    public function targetActions()
    {
        return $this->hasMany(RpTargetAction::class, 'action_id', 'rp_actions_id');
    }
}