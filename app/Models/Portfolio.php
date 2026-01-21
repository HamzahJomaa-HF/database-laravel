<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Portfolio extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $primaryKey = 'portfolio_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'description',
        'type',
        'external_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($portfolio) {
            if (empty($portfolio->portfolio_id)) {
                $portfolio->portfolio_id = (string) Str::uuid();
            }

            if (empty($portfolio->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                $lastPortfolio = Portfolio::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($lastPortfolio && preg_match('/_(\d+)$/', $lastPortfolio->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $portfolio->external_id = sprintf(
                    "PORT_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
            }
        });
    }

    public function cops()
    {
        return $this->belongsToMany(
            Cop::class,
            'cops_portfolios',
            'portfolio_id',
            'cop_id'
        )->withTimestamps();
    }

    public function activities()
    {
        return $this->belongsToMany(
            Activity::class,
            'portfolio_activities',
            'portfolio_id',
            'activity_id'
        );
    }

    public function projects()
    {
        return $this->belongsToMany(
            Project::class,
            'project_portfolios',
            'portfolio_id',
            'project_id'
        )->withPivot('order', 'metadata')
         ->withTimestamps();
    }

    
    public function attachCop($copId)
    {
        $this->cops()->syncWithoutDetaching([$copId]);
    }

    
}