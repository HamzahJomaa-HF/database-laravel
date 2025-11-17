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
        'project_id',
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

            // Generate sequential external_id: PROJACT_{YYYY}_{MM}_{sequence}
            if (empty($projectActivity->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                // Get last ProjectActivity created in this year-month
                $last = ProjectActivity::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($last && preg_match('/_(\d+)$/', $last->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $projectActivity->external_id = sprintf(
                    "PROJACT_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
            }
        });
    }

    /**
     * Relationships
     */

    // A ProjectActivity belongs to a ProjectCenter
    public function projectCenter()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    // A ProjectActivity belongs to an Activity
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');
    }
}
