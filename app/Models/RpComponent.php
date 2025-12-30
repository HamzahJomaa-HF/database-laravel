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
        'name', // Add this - it's in your schema!
        'external_id',
        'code',
        'description',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Relationships
     */
    public function programs()
    {
        return $this->hasMany(RpProgram::class, 'rp_components_id', 'rp_components_id');
    }

    // Add this relationship - RpComponent has many RpAction
    public function actions()
    {
        return $this->hasMany(RpAction::class, 'rp_component_id', 'rp_components_id');
    }

    // Add this to get activities through actions
    public function activities()
    {
        return $this->hasManyThrough(
            RpActivity::class,
            RpAction::class,
            'rp_component_id', // Foreign key on actions table
            'rp_actions_id',   // Foreign key on activities table
            'rp_components_id', // Local key on components table
            'rp_actions_id'     // Local key on actions table
        );
    }
}