<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RpFocalpoint extends Model
{
    use SoftDeletes, HasUuids;

    protected $table = 'rp_focalpoints';
    protected $primaryKey = 'rp_focalpoints_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'external_id',
        'focalpoint_code',
        'name',
        'position',
        'department',
        'email',
        'phone',
        'responsibility_level',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Relationships (MUST match controller usage)
     */
    public function activityAssignments()
    {
        return $this->hasMany(RpActivityFocalpoint::class, 'focalpoint_id', 'rp_focalpoints_id');
    }
}