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
        'name',
        'description',
        'indicator_type',
        'target_value',
        'data_source',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
    ];

    protected $dates = ['deleted_at'];

    /**
     * CORRECTED Relationships
     */
    public function activityAssignments()
    {
        return $this->hasMany(RpActivityIndicator::class, 'rp_indicators_id', 'rp_indicators_id');
    }
}