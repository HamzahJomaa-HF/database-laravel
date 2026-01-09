<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes; 

class ActionPlan extends Model
{
     use SoftDeletes;
    protected $primaryKey = 'action_plan_id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'action_plan_id',
        'title',
        'start_date',
        'end_date',
        'external_id',
        'rp_components_id',
        'excel_path',
        'excel_filename',
        'excel_metadata',
        'excel_uploaded_at',
        'excel_processed_at',
        
    ];
    
    protected $casts = [
        'excel_metadata' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'excel_uploaded_at' => 'datetime',
        'excel_processed_at' => 'datetime',
    ];

    /**
     * Boot method to generate external ID on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($actionPlan) {
            // Generate external ID if not provided
            if (empty($actionPlan->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                // Get last action plan created in this year-month
                $lastActionPlan = ActionPlan::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($lastActionPlan && $lastActionPlan->external_id) {
                    if (preg_match('/_(\d+)$/', $lastActionPlan->external_id, $matches)) {
                        $lastNumber = (int) $matches[1];
                    }
                }

                $nextNumber = $lastNumber + 1;

                $actionPlan->external_id = sprintf(
                    "AP_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
            }
        });
    }
    
    public function component()
    {
        return $this->belongsTo(RpComponent::class, 'rp_components_id', 'rp_components_id');
    }
    
    // Accessor for Excel file URL
    public function getExcelUrlAttribute()
    {
        return $this->excel_path ? Storage::url($this->excel_path) : null;
    }
}