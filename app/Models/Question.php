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

            // Auto-generate external_id if not provided
            if (empty($question->external_id)) {
                $year = now()->format('Y');
                $month = now()->format('m');
                $slugName = Str::slug($question->question_name ?? 'question', '_');
                $question->external_id = "ques_{$year}_{$month}_{$slugName}";
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
