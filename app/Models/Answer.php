<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Answer extends Model
{
    use HasFactory;

    protected $primaryKey = 'answer_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'survey_question_id',
        'question_id',
        'response_id',
        'answer_value',
        'external_id', // optional but useful for reference
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($answer) {
            // Generate UUID for primary key
            if (empty($answer->answer_id)) {
                $answer->answer_id = (string) Str::uuid();
            }

            // Sequential external ID: ANS_{YYYY}_{MM}_{sequence}
            if (empty($answer->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');

                // Get last answer created in this year-month
                $lastAnswer = Answer::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastNumber = 0;
                if ($lastAnswer && preg_match('/_(\d+)$/', $lastAnswer->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $answer->external_id = sprintf(
                    "ANS_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
            }
        });
    }

    /**
     * Relations (if needed)
     */
    public function surveyQuestion()
    {
        return $this->belongsTo(SurveyQuestion::class, 'survey_question_id', 'survey_question_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'question_id');
    }

    public function response()
    {
        return $this->belongsTo(Response::class, 'response_id', 'response_id');
    }
}
