<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectCenter extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_center_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'start_date',
        'end_date',
        'program_id',
        'parent_project_center_id',
        'project_type',
        'project_group',
        'external_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($projectCenter) {
            // Generate UUID for primary key
            if (empty($projectCenter->project_center_id)) {
                $projectCenter->project_center_id = (string) Str::uuid();
            }

            // Generate external ID: pc_{YYYY}_{MM}_{project_type or general}_{random4}
            $year = now()->format('Y');
            $month = now()->format('m');
            $type = strtolower($projectCenter->project_type ?? 'general');
            $random = substr(Str::uuid(), 0, 4);

            $projectCenter->external_id = "pc_{$year}_{$month}_{$type}_{$random}";
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
     * Relation to parent ProjectCenter
     */
    public function parent()
    {
        return $this->belongsTo(ProjectCenter::class, 'parent_project_center_id', 'project_center_id');
    }

    /**
     * Relation to child ProjectCenters
     */
    public function children()
    {
        return $this->hasMany(ProjectCenter::class, 'parent_project_center_id', 'project_center_id');
    }
}
