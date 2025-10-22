<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';
    protected $primaryKey = 'activity_id'; // match your DB

    protected $fillable = [
        'activity_title',
        'activity_type',
        'content_network',
        'start_date',
        'end_date',
        'parent_activity',
        'target_cop'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
