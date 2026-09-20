<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'property_id',
        'type',
        'amount',
        'description',
        'expense_date',
        'receipt_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public static function expenseTypes(): array
    {
        return [
            'salaire' => 'Salaire',
            'facture_cie' => 'Facture CIE',
            'facture_sodeci' => 'Facture SODECI',
            'facture_ouvrier' => 'Facture ouvrier',
            'facture_internet' => 'Facture internet',
            'impot' => 'Impôt',
            'transport' => 'Transport',
            'nourriture' => 'Nourriture',
            'carburant' => 'Carburant',
            'devis' => 'Devis',
            'commission' => 'Commission',
            'entretien' => 'Entretien',
            'piscine' => 'Piscine',
            'jardinier' => 'Jardinier',
            'maintenance' => 'Maintenance',
            'tax' => 'Taxe',
            'insurance' => 'Assurance',
            'utilities' => 'Services publics',
            'other' => 'Autre',
        ];
    }

    // Relations
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
