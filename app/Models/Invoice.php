<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Payment;

class Invoice extends Model
{
    protected $fillable = [
        'contract_id',
        'payment_id',
        'invoice_number',
        'invoice_type',
        'amount',
        'tax_amount',
        'issue_date',
        'due_date',
        'description',
        'status',
        'pdf_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    // Relations
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    // Accessors
    public function getTotalAmountAttribute(): float
    {
        return $this->amount + $this->tax_amount;
    }
}
