<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Program extends Model
{
    use HasFactory;

    protected $primaryKey = 'program_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'folder_name',
        'type',
        'program_type',
        'parent_program_id',
        'description',
        'time', 
        'start_date',   
        'end_date',     
        'external_id'
    ];

    protected static function boot()
{
    parent::boot();

    static::creating(function ($program) {
        // Generate UUID if not provided
        if (empty($program->program_id)) {
            $program->program_id = (string) \Illuminate\Support\Str::uuid();
        }

        // Generate a unique external ID if not provided
        if (empty($program->external_id)) {

            $year = now()->format('Y');
            $month = now()->format('m');

            // Get last program created in this year-month
            $lastProgram = \App\Models\Program::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->orderBy('created_at', 'desc')
                ->first();

            // Extract last sequence number safely
            $lastNumber = 0;
            if ($lastProgram && preg_match('/_(\d+)$/', $lastProgram->external_id, $matches)) {
                $lastNumber = (int) $matches[1];
            }

            $nextNumber = $lastNumber + 1;

            $program->external_id = sprintf(
                "PRG_%s_%s_%03d",
                $year,
                $month,
                $nextNumber
            );
        }

    });
}


    /**
     * Relation to ProjectCenters (if needed)
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'program_id', 'program_id');
    }
    public function parentProgram()
    {
        return $this->belongsTo(Program::class, 'parent_program_id', 'program_id');
    }

    public function subPrograms()
    {
        return $this->hasMany(Program::class, 'parent_program_id', 'program_id');
    }

}
