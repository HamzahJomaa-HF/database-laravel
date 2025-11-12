<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diploma extends Model
{
    use HasFactory;
    protected $table = 'diploma';

    protected $primaryKey = 'diploma_id';  
    public $incrementing = false;    // UUID primary key
    protected $keyType = 'string';     // UUID is a string

    protected $fillable = [
        'diploma_id',
        'diploma_name',
        'institution',
        'year',
        'external_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($diploma) {

            // Generate a unique external ID if not provided
            if (empty($diploma->external_id)) {

                $yearNow = now()->format('Y');
                $monthNow = now()->format('m');

                // Get last diploma created in this year-month
                $lastDiploma = \App\Models\Diploma::whereYear('created_at', $yearNow)
                    ->whereMonth('created_at', $monthNow)
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Extract last sequence number safely
                $lastNumber = 0;
                if ($lastDiploma && preg_match('/_(\d+)$/', $lastDiploma->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $diploma->external_id = sprintf(
                    "DIP_%s_%s_%03d",
                    $yearNow,
                    $monthNow,
                    $nextNumber
                );
            }
        });
    }

    /**
     * Relation: A diploma can belong to many users (through UserDiploma)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_diploma', 'diploma_id', 'user_id')
                    ->withTimestamps();
    }
}
