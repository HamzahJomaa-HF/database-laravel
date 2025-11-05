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
        'name',
        'folder_name',
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
        // Generate UUID for primary key if not set
        if (empty($project->project_id)) {
            $project->project_id = (string) Str::uuid();
        }


        // Generate a unique external ID if not provided
        if (empty($project->external_id)) {

            $year = now()->format('Y');
            $month = now()->format('m');

            // Get last project created in this year-month
            $lastProject = \App\Models\Project::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->orderBy('created_at', 'desc')
                ->first();

            // Extract last sequence number safely
            $lastNumber = 0;
            if ($lastProject && preg_match('/_(\d+)$/', $lastProject->external_id, $matches)) {
                $lastNumber = (int) $matches[1];
            }

            $nextNumber = $lastNumber + 1;

            $project->external_id = sprintf(
                "PRJ_%s_%s_%03d",
                $year,
                $month,
                $nextNumber
            );
        }
        

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
