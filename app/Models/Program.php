<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Program extends Model
{
     use SoftDeletes;
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
                $program->external_id = self::generateExternalId($program->program_type);
            }
        });
    }

    /**
     * Generate external_id based on program_type
     */
    public static function generateExternalId($programType = null)
    {
        // Map program types to prefixes
        $typePrefixes = [
            'Sub-Program' => 'SP',
            'Local Program' => 'LP',
            'Local Program/Network' => 'LP',
            'flagship' => 'FP',
            'Center' => 'CP',
            // Add more mappings as needed
        ];

        // Default prefix if type not found
        $prefix = 'PRG';
        
        if ($programType) {
            // Normalize program_type (lowercase, trim)
            $normalizedType = strtolower(trim($programType));
            
            if (isset($typePrefixes[$normalizedType])) {
                $prefix = $typePrefixes[$normalizedType];
            } elseif (isset($typePrefixes[$programType])) {
                $prefix = $typePrefixes[$programType];
            }
        }

        $year = now()->format('Y');
        $month = now()->format('m');

        // Get the MAXIMUM sequence number for this prefix-year-month combination
        $lastProgram = Program::where('external_id', 'like', "{$prefix}_{$year}_{$month}_%")
            ->orderByRaw('CAST(SUBSTRING(external_id FROM \'[0-9]+$\') AS INTEGER) DESC')
            ->first();

        // Extract last sequence number safely
        $lastNumber = 0;
        if ($lastProgram && $lastProgram->external_id && preg_match('/_(\d+)$/', $lastProgram->external_id, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $nextNumber = $lastNumber + 1;

        return sprintf(
            "%s_%s_%s_%03d",
            $prefix,
            $year,
            $month,
            $nextNumber
        );
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