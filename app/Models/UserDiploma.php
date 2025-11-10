<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDiploma extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Bigint primary key
    public $incrementing = true;   // Auto-incrementing
    protected $keyType = 'int';    // Bigint is an integer

    protected $fillable = [
        'user_id',
        'diploma_id',
    ];

    /**
     * Relation: a UserDiploma belongs to a User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relation: a UserDiploma belongs to a Diploma
     */
    public function diploma()
    {
        return $this->belongsTo(Diploma::class, 'diploma_id', 'id');
    }
}
