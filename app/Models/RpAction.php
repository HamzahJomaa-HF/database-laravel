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
        'rp_units_id',           
        'rp_component_id', // ADD THIS - it's in your schema!
        'external_id',
        'name',
        'code',
        'objectives',           
        'planned_start_date',
        'planned_end_date',
        'targets_beneficiaries', 
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Relationships
     */
    public function unit()
    {
        return $this->belongsTo(RpUnit::class, 'rp_units_id', 'rp_units_id');
    }

    public function component()
    {
        return $this->belongsTo(RpComponent::class, 'rp_component_id', 'rp_components_id');
    }

    public function activities()
    {
        return $this->hasMany(RpActivity::class, 'rp_actions_id', 'rp_actions_id');
    }
}