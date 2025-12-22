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
        'code',
        'description',
        
    ];

    

    protected $dates = ['deleted_at'];

    /**
     * FIXED Relationships
     */
    public function programs()
    {
        return $this->hasMany(RpProgram::class, 'rp_components_id', 'rp_components_id');
                                          
}}