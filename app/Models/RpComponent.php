<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpComponent extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'rp_components';
    protected $primaryKey = 'rp_components_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'external_id',
        'name',
        'code',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Relationships (Optional but likely needed)
     */
    public function programs()
    {
        return $this->hasMany(RpProgram::class, 'component_id', 'rp_components_id');
    }
}