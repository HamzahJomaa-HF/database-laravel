<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cop extends Model
{
    use HasFactory;

    protected $primaryKey = 'cop_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'program_id',
        'cop_name',
        'description',
        'external_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cop) {
            // Generate UUID for primary key
            if (empty($cop->cop_id)) {
                $cop->cop_id = (string) Str::uuid();
            }

            // Sequential external ID: COP_{YYYY}_{MM}_{sequence}
            if (empty($cop->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                // Get last COP created in this year-month
                $lastCop = Cop::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($lastCop && preg_match('/_(\d+)$/', $lastCop->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $cop->external_id = sprintf(
                    "COP_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
            }
        });
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }
}
