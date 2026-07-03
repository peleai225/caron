<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class PaymentSchedule extends Model
{
    protected $fillable = [
        'contract_id',
        'due_date',
        'amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    // Relations
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'overdue')
              ->orWhere(function($sub) {
                  $sub->where('status', 'pending')
                      ->where('due_date', '<', now());
              });
        });
    }

    // Methods
    public function isOverdue(): bool
    {
        return $this->due_date < now() && $this->status === 'pending';
    }

    public function daysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return max(0, now()->diffInDays($this->due_date));
    }
}
