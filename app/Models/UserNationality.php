<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNationality extends Model
{
    use HasFactory;

    protected $table = 'users_nationality'; // Table name
    protected $primaryKey = 'id';
    public $incrementing = true; // Auto-increment ID
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'nationality_id',
    ];

    /**
     * Boot method to handle any automatic logic on creating
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($userNationality) {
            // Optional: You can add logic here if needed for external_id or other fields
        });
    }

    /**
     * Relations
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id', 'id');
    }
}
