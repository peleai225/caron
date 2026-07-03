<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quittance de loyer - {{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 40px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1e40af;
        }
        .info-section {
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-label {
            font-weight: bold;
            color: #666;
        }
        .info-value {
            color: #333;
        }
        .amount-section {
            background-color: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
        }
        .amount-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .total-amount {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            border-top: 2px solid #1e40af;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>QUITTANCE DE LOYER</h1>
        <p>N° {{ $receipt->receipt_number }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Date d'émission:</span>
            <span class="info-value">{{ $receipt->issue_date->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Locataire:</span>
            <span class="info-value">{{ $receipt->payment->contract->tenant->first_name }} {{ $receipt->payment->contract->tenant->last_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Bien immobilier:</span>
            <span class="info-value">{{ $receipt->payment->contract->property->address }}, {{ $receipt->payment->contract->property->city }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Contrat:</span>
            <span class="info-value">{{ $receipt->payment->contract->contract_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Période:</span>
            <span class="info-value">{{ $receipt->payment->period }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date de paiement:</span>
            <span class="info-value">{{ $receipt->payment->payment_date->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Méthode de paiement:</span>
            <span class="info-value">{{ ucfirst(str_replace('_', ' ', $receipt->payment->payment_method)) }}</span>
        </div>
        @if($receipt->payment->reference)
        <div class="info-row">
            <span class="info-label">Référence:</span>
            <span class="info-value">{{ $receipt->payment->reference }}</span>
        </div>
        @endif
    </div>

    <div class="amount-section">
        <div class="amount-row">
            <span>Montant du loyer:</span>
            <span>{{ number_format($receipt->payment->amount, 0, ',', ' ') }} FCFA</span>
        </div>
        @if($receipt->payment->penalty_amount > 0)
        <div class="amount-row">
            <span>Pénalités de retard:</span>
            <span>{{ number_format($receipt->payment->penalty_amount, 0, ',', ' ') }} FCFA</span>
        </div>
        @endif
        <div class="amount-row total-amount">
            <span>MONTANT TOTAL PAYÉ:</span>
            <span>{{ number_format($receipt->amount, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    <div class="footer">
        <p>Cette quittance atteste que le montant ci-dessus a été reçu en paiement du loyer.</p>
        <p>Fait à {{ $receipt->payment->contract->agency->city ?? 'Abidjan' }}, le {{ $receipt->issue_date->format('d/m/Y') }}</p>
        <p style="margin-top: 30px;">
            <strong>{{ $receipt->payment->contract->agency->name ?? 'Agence Immobilière' }}</strong><br>
            {{ $receipt->payment->contract->agency->address ?? '' }}
        </p>
    </div>
</body>
</html>
