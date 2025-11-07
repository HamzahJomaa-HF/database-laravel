<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory;

    protected $primaryKey = 'activity_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'activity_title',
        'activity_type',
        'folder_name',
        'content_network',
        'start_date',
        'end_date',
        'parent_activity',
        'target_cop',
        'external_id',
    ];

    protected $dates = ['start_date', 'end_date'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activity) {
            // ✅ Generate UUID for primary key
            if (empty($activity->activity_id)) {
                $activity->activity_id = (string) Str::uuid();
            }

            // ✅ Generate a unique external ID like the Program model
            if (empty($activity->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                // Get the last created activity for this year-month
                $lastActivity = self::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Extract last sequence number
                $lastNumber = 0;
                if ($lastActivity && preg_match('/_(\d+)$/', $lastActivity->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $activity->external_id = sprintf(
                    "ACT_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
            }
        });
    }

    /**
     * 🔗 Self-relation for parent/child activities
     */
    public function parent()
    {
        return $this->belongsTo(Activity::class, 'parent_activity', 'activity_id');
    }

    public function children()
    {
        return $this->hasMany(Activity::class, 'parent_activity', 'activity_id');
    }

    /**
     * 🔗 Many-to-Many: Activities belong to Portfolios
     */
    public function portfolios()
    {
        return $this->belongsToMany(
            Portfolio::class,
            'portfolio_activities',
            'activity_id',
            'portfolio_id'
        );
    }

    public function attachPortfolio($portfolioId)
    {
        $this->portfolios()->syncWithoutDetaching([$portfolioId]);
    }

    public function detachPortfolio($portfolioId)
    {
        $this->portfolios()->detach($portfolioId);
    }
}
