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
        'description',
        'external_id', // optional, like in Activity
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($program) {
            // Generate UUID for primary key
            if (empty($program->program_id)) {
                $program->program_id = (string) Str::uuid();
            }

            // Optional: Generate external ID: prog_{YYYY}_{MM}_{slug(name)}
            $year = now()->format('Y');
            $month = now()->format('m');
            $slugName = Str::slug($program->name ?? 'unknown', '_');

            $program->external_id = "prog_{$year}_{$month}_{$slugName}";
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
