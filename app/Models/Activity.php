<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory;

    protected $primaryKey = 'activity_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'activity_title',
        'activity_type',
        'content_network',
        'start_date',
        'end_date',
        'parent_activity',
        'target_cop',
        'external_id',
    ];

    protected $dates = ['start_date', 'end_date'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activity) {
            // Assign a UUID for the primary key if not set
            if (empty($activity->activity_id)) {
                $activity->activity_id = (string) Str::uuid();
            }

            // Assign a unique UUID for external_id if not set
            if (empty($activity->external_id)) {
                $activity->external_id = (string) Str::uuid();
            }
        });
    }

    /**
     * Self-relation for parent/child activities
     */
    public function parent()
    {
        return $this->belongsTo(Activity::class, 'parent_activity', 'activity_id');
    }

    public function children()
    {
        return $this->hasMany(Activity::class, 'parent_activity', 'activity_id');
    }

    /**
     * Optional relation for target_cop if needed
     */
    // public function targetGroup()
    // {
    //     return $this->belongsTo(TargetGroup::class, 'target_cop', 'id');
    // }
}
