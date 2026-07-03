<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Contract extends Model
{
    use LogsActivity;

    protected $fillable = [
        'agency_id',
        'tenant_id',
        'property_id',
        'owner_id',
        'contract_number',
        'type_contrat',
        'rent_amount',
        'deposit',
        'start_date',
        'end_date',
        'payment_frequency',
        'payment_day',
        'status',
        'template_id',
        'notes',
        'signed_at',
        'pdf_path',
    ];

    protected $casts = [
        'rent_amount' => 'decimal:2',
        'deposit' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'signed_at' => 'datetime',
    ];

    // Relations
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'template_id');
    }

    public static function typesContrat(): array
    {
        return \App\Models\Litige::typesContrat();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiring($query, int $days = 30)
    {
        return $query->where('status', 'active')
            ->whereBetween('end_date', [now(), now()->addDays($days)]);
    }

    // Methods
    public function isExpired(): bool
    {
        return $this->end_date < now() && $this->status === 'active';
    }

    public function daysUntilExpiry(): int
    {
        return max(0, now()->diffInDays($this->end_date, false));
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'rent_amount', 'start_date', 'end_date'])
            ->logOnlyDirty();
    }
}
