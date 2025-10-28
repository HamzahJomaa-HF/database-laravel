<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'start_date',
        'end_date',
        'program_id',
        'parent_project_id',
        'project_type',
        'project_group',
        'external_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            // Generate UUID for primary key
            if (empty($project->project_id)) {
                $project->project_id = (string) Str::uuid();
            }

             // Generate unique external_id like projects
        $year = now()->format('Y');
        $month = now()->format('m');
        $type = strtolower($activity->activity_type ?? 'general');
        $random = substr(Str::uuid(), 0, 4); // ensures uniqueness

        $activity->external_id = "act_{$year}_{$month}_{$type}_{$random}";
    });
    }

    /**
     * Relation to Program
     */
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    /**
     * Relation to parent Project
     */
    public function parent()
    {
        return $this->belongsTo(Project::class, 'parent_project_id', 'project_id');
    }

    /**
     * Relation to child Projects
     */
    public function children()
    {
        return $this->hasMany(Project::class, 'parent_project_id', 'project_id');
    }
}
