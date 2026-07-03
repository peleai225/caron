<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrat Locatif - {{ $contract->contract_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 200px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
        }
        .signature-box {
            text-align: center;
            width: 250px;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CONTRAT DE LOCATION</h1>
        <p>Numéro: {{ $contract->contract_number }}</p>
    </div>

    <div class="section">
        <div class="section-title">PARTIES AU CONTRAT</div>
        <div class="info-row">
            <span class="info-label">Locataire:</span>
            {{ $contract->tenant->full_name }}
        </div>
        <div class="info-row">
            <span class="info-label">Téléphone:</span>
            {{ $contract->tenant->phone }}
        </div>
        <div class="info-row">
            <span class="info-label">Bien loué:</span>
            {{ $contract->property->address }}, {{ $contract->property->city }}
        </div>
        @if($contract->owner)
        <div class="info-row">
            <span class="info-label">Propriétaire:</span>
            {{ $contract->owner->name }}
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">CONDITIONS DU CONTRAT</div>
        <div class="info-row">
            <span class="info-label">Montant du loyer:</span>
            {{ number_format($contract->rent_amount, 0, ',', ' ') }} FCFA
        </div>
        <div class="info-row">
            <span class="info-label">Caution:</span>
            {{ number_format($contract->deposit, 0, ',', ' ') }} FCFA
        </div>
        <div class="info-row">
            <span class="info-label">Date de début:</span>
            {{ $contract->start_date->format('d/m/Y') }}
        </div>
        <div class="info-row">
            <span class="info-label">Date de fin:</span>
            {{ $contract->end_date->format('d/m/Y') }}
        </div>
        <div class="info-row">
            <span class="info-label">Fréquence de paiement:</span>
            @if($contract->payment_frequency === 'monthly') Mensuel
            @elseif($contract->payment_frequency === 'quarterly') Trimestriel
            @else Annuel
            @endif
        </div>
        <div class="info-row">
            <span class="info-label">Jour de paiement:</span>
            Le {{ $contract->payment_day }} de chaque mois
        </div>
    </div>

    @if($content)
    <div class="section">
        <div class="section-title">CLauses du contrat</div>
        <div>{!! nl2br(e($content)) !!}</div>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>Le Locataire</strong></p>
            <p>{{ $contract->tenant->full_name }}</p>
            <p style="margin-top: 30px;">_________________________</p>
        </div>
        <div class="signature-box">
            <p><strong>Le Propriétaire / Représentant</strong></p>
            @if($contract->owner)
            <p>{{ $contract->owner->name }}</p>
            @endif
            <p style="margin-top: 30px;">_________________________</p>
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>Contrat généré le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>Document généré par Caron - Gestion Immobilière</p>
    </div>
</body>
</html>

