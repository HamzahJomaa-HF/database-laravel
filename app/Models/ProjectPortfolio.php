<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPortfolio extends Model
{
    protected $table = 'project_portfolios';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'project_id',
        'portfolio_id',
        'order',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'order' => 'integer'
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class, 'portfolio_id');
    }
}