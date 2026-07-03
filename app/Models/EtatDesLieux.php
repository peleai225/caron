<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtatDesLieux extends Model
{
    protected $table = 'etat_des_lieux';

    protected $fillable = [
        'property_id',
        'contract_id',
        'type',
        'date',
        'observations',
        'pdf_path',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public static function types(): array
    {
        return [
            'entree' => 'Entrée',
            'sortie' => 'Sortie',
        ];
    }
}
