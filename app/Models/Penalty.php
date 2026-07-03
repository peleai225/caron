<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penalty extends Model
{
    protected $fillable = [
        'payment_schedule_id',
        'payment_id',
        'amount',
        'rate',
        'days_late',
        'calculated_at',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'calculated_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relations
    public function paymentSchedule(): BelongsTo
    {
        return $this->belongsTo(PaymentSchedule::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    // Accessors
    public function getStatusAttribute(): string
    {
        return $this->paid_at ? 'paid' : 'unpaid';
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->whereNotNull('paid_at');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereNull('paid_at');
    }
}
