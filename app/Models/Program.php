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
        'type',
        'program_type',
        'description',
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
            $slugName = \Illuminate\Support\Str::slug($program->name ?? 'unknown', '_');
            $random = \Illuminate\Support\Str::random(4); // ensures uniqueness
            $program->external_id = "prog_{$year}_{$month}_{$slugName}_{$random}";
        }
    });
}


    /**
     * Relation to ProjectCenters (if needed)
     */
    public function projectCenters()
    {
        return $this->hasMany(ProjectCenter::class, 'program_id', 'program_id');
    }
}
