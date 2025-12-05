<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpTargetAction extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'rp_target_actions';
    protected $primaryKey = 'rp_target_actions_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'action_id',
        'external_id',
        'target_name',
        'description',
        'target_value',
        'unit_of_measure',
        'target_date',
        'status',
        'achieved_value',
        'achieved_date'
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'achieved_value' => 'decimal:2',
        'target_date' => 'date',
        'achieved_date' => 'date'
    ];

    /**
     * Relationships (MUST match controller usage)
     */
    public function action()
    {
        return $this->belongsTo(RpAction::class, 'action_id', 'rp_actions_id');
    }
}