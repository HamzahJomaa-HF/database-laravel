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
        'program_id',
        'employee_id',
        'description',
        'external_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($projectEmployee) {
            // Generate UUID for primary key
            if (empty($projectEmployee->project_employee_id)) {
                $projectEmployee->project_employee_id = (string) Str::uuid();
            }

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

    // Each ProjectEmployee belongs to a Program
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    // Each ProjectEmployee belongs to an Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
