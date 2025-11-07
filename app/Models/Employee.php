<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'project_id',
        'role_id',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'employee_type',
        'start_date',
        'end_date',
        'external_id', // added external_id
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            // Generate UUID for primary key
            if (empty($employee->employee_id)) {
                $employee->employee_id = (string) Str::uuid();
            }

            // Generate sequential external_id: EMP_{YYYY}_{MM}_{sequence}
            if (empty($employee->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                // Get last employee created in this year-month
                $lastEmployee = Employee::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($lastEmployee && preg_match('/_(\d+)$/', $lastEmployee->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $employee->external_id = sprintf(
                    "EMP_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
            }
        });
    }

    /**
     * Relation to Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    /**
     * Relation to Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
}
