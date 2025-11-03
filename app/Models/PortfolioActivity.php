<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioActivity extends Model
{
    use HasFactory;

    protected $table = 'portfolio_activities';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'portfolio_id',
        'activity_id',
    ];

    /**
     * Relationships
     */
    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class, 'portfolio_id', 'portfolio_id');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');
    }
}
