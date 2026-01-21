<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes; 

class ProjectEmployee extends Model
{
    use SoftDeletes;
    use HasFactory, HasUuids;

    protected $primaryKey = 'project_employee_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'project_employee_id',
        'project_id',
        'employee_id',
        'description',
        'external_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($projectEmployee) {
            // REMOVE THE MANUAL UUID GENERATION - HasUuids trait handles it automatically
            // The HasUuids trait will set the UUID for you, so this code is redundant
            
            // Generate sequential external_id: PROJEMP_{YYYY}_{MM}_{sequence}
            if (empty($projectEmployee->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                // Get last ProjectEmployee created in this year-month
                $last = ProjectEmployee::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($last && preg_match('/_(\d+)$/', $last->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $projectEmployee->external_id = sprintf(
                    "PROJEMP_%s_%s_%03d",
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

    // Each ProjectEmployee belongs to a Project
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    // Each ProjectEmployee belongs to an Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}