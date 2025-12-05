<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpIndicator extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'rp_indicators';
    protected $primaryKey = 'rp_indicators_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'external_id',
        'indicator_code',
        'name',
        'description',
        'indicator_type',
        'unit_of_measure',
        'baseline_value',
        'baseline_date',
        'target_value',
        'target_date',
        'data_source',
        'frequency',
        'calculation_method',
        'is_active'
    ];

    protected $casts = [
        'baseline_value' => 'decimal:2',
        'target_value' => 'decimal:2',
        'baseline_date' => 'date',
        'target_date' => 'date',
        'is_active' => 'boolean'
    ];

    /**
     * Relationships (MUST match controller usage)
     */
    public function activityAssignments()
    {
        return $this->hasMany(RpActivityIndicator::class, 'indicator_id', 'rp_indicators_id');
    }
}