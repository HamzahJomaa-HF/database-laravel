<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ActivityFinancial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activity_financials';
    protected $primaryKey = 'activity_financial_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'activity_financial_id',
        'activity_id',
        'user_id',
        'cop_id',
        'financial_type',
        'amount',
        'payment_status',
        'tx_date',
        'financial_data',
        'external_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'financial_data' => 'array',
        'tx_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->activity_financial_id)) {
                $model->activity_financial_id = (string) Str::uuid();
            }
            if (empty($model->external_id)) {
                $model->external_id = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function cop()
    {
        return $this->belongsTo(Cop::class, 'cop_id', 'cop_id');
    }

    // Scopes
    public function scopeOmt($query)
    {
        return $query->where('financial_type', 'omt');
    }

    public function scopeMedical($query)
    {
        return $query->where('financial_type', 'medical');
    }

    public function scopeEducation($query)
    {
        return $query->where('financial_type', 'education');
    }

    // Helper methods
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }
}