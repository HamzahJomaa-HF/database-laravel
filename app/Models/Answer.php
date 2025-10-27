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

            // Optional: Generate external ID: ans_{YYYY}_{MM}_{random6}
            if (empty($answer->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');
                $randomSuffix = substr(Str::uuid(), 0, 6);
                $answer->external_id = "ans_{$year}_{$month}_{$randomSuffix}";
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
