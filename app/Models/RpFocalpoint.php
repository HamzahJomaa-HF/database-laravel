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
        'name',
        'type',         
        'employee_id',  
       
    ];

   

    protected $dates = ['deleted_at'];

    /**
     * CORRECTED Relationships
     */
    public function activityAssignments()
    {
        return $this->hasMany(RpActivityFocalpoint::class, 'rp_focalpoints_id', 'rp_focalpoints_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}