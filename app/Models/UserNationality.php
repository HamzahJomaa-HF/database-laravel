<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserNationality extends Model
{
     use SoftDeletes;
    use HasFactory;

    protected $table = 'users_nationality'; // Pivot table
    public $timestamps = true; // Keep timestamps
    public $incrementing = false; // Not needed if no primary key
    protected $keyType = 'string'; // Not needed if no primary key

    protected $fillable = [
        'user_id',
        'nationality_id',
    ];

    /**
     * Relations
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id', 'nationality_id');
    }
}
