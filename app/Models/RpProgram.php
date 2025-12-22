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
        'rp_components_id',  // ← FIXED: was 'component_id'
        'external_id',
        'name',
        'code',
        'description',
    ];

   

    protected $dates = ['deleted_at'];

    /**
     * CORRECTED Relationships
     */
    public function component()
    {
        return $this->belongsTo(RpComponent::class, 'rp_components_id', 'rp_components_id');
    }

    public function units()
    {
        return $this->hasMany(RpUnit::class, 'rp_programs_id', 'rp_programs_id');
    }
}