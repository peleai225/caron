<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantDocument extends Model
{
    protected $fillable = [
        'tenant_id',
        'type',
        'name',
        'path',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    // Relations
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
