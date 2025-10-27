<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProjectEmployee extends Model
{
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
