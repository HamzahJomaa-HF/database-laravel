<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpUnit extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'rp_units';
    protected $primaryKey = 'rp_units_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'program_id',
        'external_id',
        'name',
        'code',
        'unit_type',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Relationships (MUST match controller usage)
     */
    public function program()
    {
        return $this->belongsTo(RpProgram::class, 'program_id', 'rp_programs_id');
    }

    public function actions()
    {
        return $this->hasMany(RpAction::class, 'unit_id', 'rp_units_id');
    }
}