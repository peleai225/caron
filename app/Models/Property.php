<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Property extends Model
{
    use LogsActivity;

    protected $fillable = [
        'agency_id',
        'owner_id',
        'parent_id',
        'type',
        'unit_type',
        'status',
        'address',
        'designation',
        'city',
        'neighborhood',
        'bedrooms',
        'bathrooms',
        'surface',
        'description',
        'monthly_rent',
        'is_active',
    ];

    protected $casts = [
        'surface' => 'decimal:2',
        'monthly_rent' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relations
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'parent_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Property::class, 'parent_id')->orderBy('designation');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('order');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function etatDesLieux(): HasMany
    {
        return $this->hasMany(EtatDesLieux::class, 'property_id')->orderByDesc('date');
    }

    public function primaryImage()
    {
        return $this->hasOne(PropertyImage::class)->where('is_primary', true);
    }

    // Types de biens (niveau principal)
    public static function types(): array
    {
        return [
            'maison' => 'Maison',
            'immeuble' => 'Immeuble',
            'boutique' => 'Boutique',
            'terrain' => 'Terrain',
        ];
    }

    // Types d'unités (dans un immeuble)
    public static function unitTypes(): array
    {
        return [
            'studio' => 'Studio',
            'deux_pieces' => '2 pièces',
            'trois_pieces' => '3 pièces',
            'quatre_pieces' => '4 pièces et +',
            'appartement' => 'Appartement',
            'etage' => 'Étage complet',
            'duplex' => 'Duplex',
            'chambre' => 'Chambre',
            'bureau' => 'Bureau / Local professionnel',
            'autre' => 'Autre',
        ];
    }

    public function isBuilding(): bool
    {
        return $this->type === 'immeuble' && !$this->parent_id;
    }

    public function isUnit(): bool
    {
        return (bool) $this->parent_id;
    }

    // Accessors
    public function getNameAttribute(): string
    {
        if ($this->designation && $this->parent_id) {
            $parent = $this->parent;
            return ($parent ? $parent->address . ' — ' : '') . $this->designation;
        }
        $parts = explode(',', $this->address ?? '');
        return trim($parts[0] ?? '') . ($this->neighborhood ? ' - ' . $this->neighborhood : '');
    }

    public function getFullAddressAttribute(): string
    {
        if ($this->parent_id && $this->parent) {
            return $this->parent->address . ($this->designation ? ', ' . $this->designation : '');
        }
        return $this->address ?? '';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'libre');
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'status', 'address', 'monthly_rent', 'is_active'])
            ->logOnlyDirty();
    }
}
