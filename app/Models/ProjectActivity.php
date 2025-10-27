<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectActivity extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_activity_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'project_center_id',
        'activity_id',
        'external_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($projectActivity) {
            // Generate UUID for primary key
            if (empty($projectActivity->project_activity_id)) {
                $projectActivity->project_activity_id = (string) Str::uuid();
            }

            // Generate external_id in the format: projact_{YYYY}_{MM}_{short_uuid}
            $year = now()->format('Y');
            $month = now()->format('m');
            $shortUuid = substr((string) Str::uuid(), 0, 8);

            $projectActivity->external_id = "projact_{$year}_{$month}_{$shortUuid}";
        });
    }

    /**
     * Relationships
     */

    // A ProjectActivity belongs to a ProjectCenter
    public function projectCenter()
    {
        return $this->belongsTo(ProjectCenter::class, 'project_center_id', 'project_center_id');
    }

    // A ProjectActivity belongs to an Activity
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');
    }
}
