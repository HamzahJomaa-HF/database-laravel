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
                $program->program_id = (string) Str::uuid();
            }

            // Generate a unique external ID if not provided
            if (empty($program->external_id)) {

                $year = now()->format('Y');
                $month = now()->format('m');

                // FIX: Get the MAXIMUM sequence number, not just the last one
                $lastProgram = Program::where('external_id', 'like', "PRG_{$year}_{$month}_%")
                    ->orderByRaw('CAST(SUBSTRING(external_id FROM \'[0-9]+$\') AS INTEGER) DESC')
                    ->first();

                // Extract last sequence number safely
                $lastNumber = 0;
                if ($lastProgram && $lastProgram->external_id && preg_match('/_(\d+)$/', $lastProgram->external_id, $matches)) {
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
     * Relation to Project(if needed)
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