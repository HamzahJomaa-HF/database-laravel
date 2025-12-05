<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpProgram extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'rp_programs';
    protected $primaryKey = 'rp_programs_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'component_id',
        'external_id',
        'name',
        'code',
        'description',
        'start_date',
        'end_date',
        'budget',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Relationships (MUST match controller usage)
     */
    public function component()
    {
        return $this->belongsTo(RpComponent::class, 'component_id', 'rp_components_id');
    }

    public function units()
    {
        return $this->hasMany(RpUnit::class, 'program_id', 'rp_programs_id');
    }
}