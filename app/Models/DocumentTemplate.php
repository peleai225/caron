<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DocumentTemplate extends Model
{
    protected $fillable = [
        'agency_id',
        'name',
        'slug',
        'category',
        'country',
        'description',
        'file_path',
        'variables',
        'is_system',
        'is_default',
        'is_active',
        'version',
        'previous_version_id',
        'usage_count',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'variables' => 'array',
        'version' => 'integer',
        'usage_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
    }

    // Relations
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function previousVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'previous_version_id');
    }

    public function nextVersions(): HasMany
    {
        return $this->hasMany(DocumentTemplate::class, 'previous_version_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    // Helper methods
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function getFullPathAttribute(): string
    {
        return storage_path('app/public/' . $this->file_path);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function canBeEdited(): bool
    {
        return !$this->is_system;
    }

    public function getAvailableVariables(): array
    {
        return $this->variables ?? [];
    }
}

