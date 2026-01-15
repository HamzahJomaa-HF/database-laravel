<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne; // Add this import

class Employee extends Authenticatable
{
    use SoftDeletes, HasFactory, Notifiable;

    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'employee_id',
        'project_id',
        'role_id',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'employee_type',
        'start_date',
        'end_date',
        'external_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->employee_id)) {
                $employee->employee_id = (string) Str::uuid();
            }

            if (empty($employee->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

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

    // =============== RELATIONSHIPS ===============

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    // Fix the return type - use HasOne from Illuminate\Database\Eloquent\Relations\HasOne
    public function credentials(): HasOne
    {
        return $this->hasOne(CredentialsEmployee::class, 'employee_id', 'employee_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}