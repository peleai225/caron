<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Payment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'contract_id',
        'payment_schedule_id',
        'amount',
        'penalty_amount',
        'charges_amount',
        'depense_travaux',
        'commission_percent',
        'payment_date',
        'period',
        'payment_type',
        'payment_method',
        'reference',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'charges_amount' => 'decimal:2',
        'depense_travaux' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public static function paymentTypes(): array
    {
        return [
            'loyer' => 'Loyer',
            'charges_locatives' => 'Charges locatives',
            'factures' => 'Factures',
            'vente' => 'Vente',
            'commission' => 'Commission',
        ];
    }

    // Relations
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function paymentSchedule(): BelongsTo
    {
        return $this->belongsTo(PaymentSchedule::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    // Accessors
    public function getTotalAmountAttribute(): float
    {
        $charges = (float) ($this->charges_amount ?? 0);
        $travaux = (float) ($this->depense_travaux ?? 0);
        $penalty = (float) ($this->penalty_amount ?? 0);
        return $this->amount + $penalty + $charges + $travaux;
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['amount', 'status', 'payment_method'])
            ->logOnlyDirty();
    }
}
