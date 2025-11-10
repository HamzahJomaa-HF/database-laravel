<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Nationality extends Model
{
    use HasFactory;

    protected $table = 'nationality'; // Table name
    protected $primaryKey = 'id';
    public $incrementing = true; // Auto-increment ID
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'external_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($nationality) {
            // Generate a unique external ID if not provided
            if (empty($nationality->external_id)) {

                $year = now()->format('Y');
                $month = now()->format('m');

                // Get last nationality created in this year-month
                $lastNationality = \App\Models\Nationality::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Extract last sequence number safely
                $lastNumber = 0;
                if ($lastNationality && preg_match('/_(\d+)$/', $lastNationality->external_id, $matches)) {
                    $lastNumber = (int) $matches[1];
                }

                $nextNumber = $lastNumber + 1;

                $nationality->external_id = sprintf(
                    "NAT_%s_%s_%03d",
                    $year,
                    $month,
                    $nextNumber
                );
            }
        });
    }

    /**
     * Relation to Users
     */
    public function users()
    {
        return $this->hasMany(User::class, 'nationality_id', 'id');
    }
}
