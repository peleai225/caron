<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    protected $fillable = [
        'payment_id',
        'receipt_number',
        'amount',
        'issue_date',
        'pdf_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issue_date' => 'date',
    ];

    // Relations
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
