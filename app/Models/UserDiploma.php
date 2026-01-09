<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDiploma extends Model
{
     use SoftDeletes;
    use HasFactory;

    protected $table = 'users_diploma'; // Pivot table
    public $timestamps = true; 
    public $incrementing = false; // Not needed if no primary key
    protected $keyType = 'string'; // Not needed if no primary key

    protected $fillable = [
        'user_id',
        'diploma_id',
    ];

    /**
     * Relations
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function diploma()
    {
        return $this->belongsTo(Diploma::class, 'diploma_id', 'diploma_id');
    }
}
