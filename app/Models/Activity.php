<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes; 


/**
 * @OA\Schema(
 *   schema="Activity",
 *   type="object",
 *   required={"activity_id", "activity_title_en", "activity_type"},
 *
 *   @OA\Property(property="activity_id", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
 *   @OA\Property(property="external_id", type="string", example="EXT-12345"),
 *   @OA\Property(property="folder_name", type="string", example="Workshop_Folder"),
 *   @OA\Property(property="activity_title_en", type="string", example="English Workshop Title"),
 *   @OA\Property(property="activity_title_ar", type="string", example="عنوان الورشة"),
 *   @OA\Property(property="activity_type", type="string", example="Workshop"),
 *   @OA\Property(property="content_network", type="string", example="Online"),
 *   @OA\Property(property="start_date", type="string", format="date", example="2025-10-01"),
 *   @OA\Property(property="end_date", type="string", format="date", example="2025-10-05"),
 *   @OA\Property(
 *      property="operational_support",
 *      type="object",
 *      @OA\Property(property="logistics", type="boolean", example=false),
 *      @OA\Property(property="public_relations", type="boolean", example=false),
 *      @OA\Property(property="media", type="boolean", example=false),
 *      @OA\Property(property="data", type="boolean", example=false)
 *   ),
 *   @OA\Property(property="venue", type="string", example="Conference Hall A")
 * )
 */

class Activity extends Model
{
     use SoftDeletes;
    use HasFactory;

    protected $primaryKey = 'activity_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'activities';

    protected $fillable = [
        'external_id',
        'folder_name',
        'activity_title_en',
        'activity_title_ar',
        'activity_type',
        'content_network',
        'experts',
        'start_date',
        'end_date',
        'parent_activity',
        'target_cop',
        'operational_support',
        'venue',
       'maximum_capacity',
        
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'operational_support' => 'array', // json <-> array
         'projects' => 'array',
        'rp_activities' => 'array', 
        'focal_points' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activity) {
            if (empty($activity->activity_id)) {
                $activity->activity_id = (string) \Illuminate\Support\Str::uuid();
            }

            // auto-generate external_id if not provided
            if (empty($activity->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                $lastActivity = self::where('external_id', 'like', "ACT_{$year}_{$month}_%")
                    ->orderByRaw("CAST(SUBSTRING(external_id FROM '[0-9]+$') AS INTEGER) DESC")
                    ->first();

                $lastNumber = 0;
                if ($lastActivity && $lastActivity->external_id && preg_match('/_(\d+)$/', $lastActivity->external_id, $m)) {
                    $lastNumber = (int) $m[1];
                }

                $activity->external_id = sprintf("ACT_%s_%s_%03d", $year, $month, $lastNumber + 1);
            }
        });
    }

    /**
     * Self relation
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_activity', 'activity_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_activity', 'activity_id');
    }

    /**
     * Many-to-many portfolios
     */
    public function portfolios()
    {
        return $this->belongsToMany(
            Portfolio::class,
            'portfolio_activities',
            'activity_id',
            'portfolio_id'
        );
    }
     /**
     * ============================================
     * NEW RELATIONSHIPS FOR PIVOT TABLES
     * ============================================
     */

    /**
     * Relationship with RpActivityMappings (for reporting activities)
     */
    public function rpActivityMappings()
    {
        return $this->hasMany(RpActivityMapping::class, 'activity_id', 'activity_id');
    }

    /**
     * Relationship with ProjectActivities (for project assignments)
     */
    public function projectActivities()
    {
        return $this->hasMany(ProjectActivity::class, 'activity_id', 'activity_id');
    }

    /**
     * Many-to-Many relationship with RpActivities through RpActivityMappings
     */
    public function reportingActivities()
    {
        return $this->belongsToMany(
            RpActivity::class,
            'rp_activity_mappings',
            'activity_id',
            'rp_activities_id',
            'activity_id',
            'rp_activities_id'
        )->withTimestamps();
    }

    /**
     * Many-to-Many relationship with Projects through ProjectActivities
     */
    public function assignedProjects()
    {
        return $this->belongsToMany(
            Project::class,
            'project_activities',
            'activity_id',
            'project_id',
            'activity_id',
            'project_id'
        )->withTimestamps();
    }

    /**
     * Relationship with ActivityFocalPoints (for focal points)
     * Note: You need to create the ActivityFocalPoint model if it doesn't exist
     */
    public function activityFocalPoints()
    {
        return $this->hasMany(ActivityFocalPoint::class, 'activity_id', 'activity_id');
    }

    /**
     * Many-to-Many relationship with focal points through ActivityFocalPoints
     */
    public function focalPoints()
    {
        return $this->belongsToMany(
            RpFocalpoint::class,
            'activity_focal_points',
            'activity_id',
            'rp_focalpoints_id',
            'activity_id',
            'rp_focalpoints_id'
        )->withTimestamps();
    }

    /**
     * Scopes the query to the activities visible (read-only) to the given
     * employee: everything for an employee who can view all Activities
     * records (super admin, a role granted 'full' access to Activities, or
     * a role granted the 'view_all' read-only override), otherwise only
     * activities they're a focal point on.
     */
    public function scopeVisibleTo($query, ?Employee $employee)
    {
        if ($employee && !$employee->canViewAllFor('Activities')) {
            $query->whereHas('focalPoints', function ($q) use ($employee) {
                $q->where('employee_id', $employee->employee_id);
            });
        }

        return $query;
    }

    /**
     * Whether the given employee may view this specific activity (used for
     * the show action). Includes the 'view_all' read-only override.
     */
    public function isVisibleTo(Employee $employee): bool
    {
        if ($employee->canViewAllFor('Activities')) {
            return true;
        }

        return $this->focalPoints()->where('employee_id', $employee->employee_id)->exists();
    }

    /**
     * Whether the given employee may modify (edit/delete) this specific
     * activity. Deliberately narrower than isVisibleTo() — holding the
     * read-only 'view_all' override does NOT grant write access outside an
     * employee's own focal-point assignments; only 'full' (or super admin)
     * does.
     */
    public function isEditableBy(Employee $employee): bool
    {
        if ($employee->canSeeAllFor('Activities')) {
            return true;
        }

        return $this->focalPoints()->where('employee_id', $employee->employee_id)->exists();
    }
}

