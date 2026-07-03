<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractTemplate extends Model
{
    protected $fillable = [
        'agency_id',
        'name',
        'country',
        'content',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relations
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'template_id');
    }
}
