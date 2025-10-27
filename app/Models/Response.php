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

            // Optional: Generate external ID like "resp_{YYYY}_{MM}_{slug(user_id)}"
            $year = now()->format('Y');
            $month = now()->format('m');
            $slugUser = Str::slug($response->user_id ?? 'unknown', '_');

            if (empty($response->external_id)) {
                $response->external_id = "resp_{$year}_{$month}_{$slugUser}";
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
