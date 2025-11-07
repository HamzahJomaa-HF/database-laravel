<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Question extends Model
{
    use HasFactory;

    protected $primaryKey = 'question_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'survey_id',
        'external_id',
        'question_type',
        'question_name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($question) {
            // Generate UUID for primary key
            if (empty($question->question_id)) {
                $question->question_id = (string) Str::uuid();
            }

            // Generate sequential external_id: QUES_{YYYY}_{MM}_{sequence}
            if (empty($question->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                // Get last Question created in this year-month
                $last = Question::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($last && preg_match('/_(\d+)$/', $last->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $question->external_id = sprintf(
                    "QUES_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
            }
        });
    }

    /**
     * Relation to Survey (optional)
     */
    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id', 'survey_id');
    }
}
