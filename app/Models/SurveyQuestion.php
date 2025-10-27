<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $primaryKey = 'survey_question_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'survey_id',
        'question_id',
        'question_order',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($surveyQuestion) {
            // Generate UUID for primary key
            if (empty($surveyQuestion->survey_question_id)) {
                $surveyQuestion->survey_question_id = (string) Str::uuid();
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
     * Relation to Question
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'question_id');
    }
}
