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
    
            if (empty($cop->cop_id)) {
                $cop->cop_id = (string) Str::uuid();
            }

        
            $year = now()->format('Y');
            $month = now()->format('m');
            $slugName = Str::slug($cop->cop_name ?? 'unknown', '_');
             $cop->external_id = "cop_{$year}_{$month}_{$slugName}";
             $cop->external_id = "cop_{$year}_{$month}_{$slugName}";

        });
    }

    
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }
}
