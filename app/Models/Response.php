<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Response extends Model
{
    use HasFactory;

    protected $primaryKey = 'response_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'survey_id',
        'user_id',
        'submitted_at',
        'external_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($response) {
            // Generate UUID for primary key
            if (empty($response->response_id)) {
                $response->response_id = (string) Str::uuid();
            }

            // Generate sequential external_id: RESP_{YYYY}_{MM}_{sequence}
            if (empty($response->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                // Get last Response created in this year-month
                $last = Response::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($last && preg_match('/_(\d+)$/', $last->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $response->external_id = sprintf(
                    "RESP_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
            }

            // Default submitted_at to now if not provided
            if (empty($response->submitted_at)) {
                $response->submitted_at = now();
            }
        });
    }

    /**
     * Relation to Survey
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id', 'survey_id');
    }

    /**
     * Relation to User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
