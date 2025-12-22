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
        'rp_programs_id',  // ← FIXED: was 'program_id'
        'external_id',
        'name',
        'code',
        'unit_type',
        'description',
    ];

    

    protected $dates = ['deleted_at'];

    /**
     * CORRECTED Relationships
     */
    public function program()
    {
        return $this->belongsTo(RpProgram::class, 'rp_programs_id', 'rp_programs_id');
    }

    public function actions()
    {
        return $this->hasMany(RpAction::class, 'rp_units_id', 'rp_units_id');
    }
}