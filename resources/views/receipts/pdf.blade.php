<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quittance {{ $receipt->receipt_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; }
        .page { padding: 32px 36px; }

        /* En-tête agence */
        .header { display: table; width: 100%; margin-bottom: 24px; border-bottom: 3px solid #1d4ed8; padding-bottom: 16px; }
        .header-left  { display: table-cell; vertical-align: middle; width: 60%; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .agency-name  { font-size: 18px; font-weight: bold; color: #1d4ed8; margin: 0 0 3px; }
        .agency-sub   { font-size: 9px; color: #64748b; }
        .receipt-badge { display: inline-block; background: #1d4ed8; color: #fff; font-size: 13px; font-weight: bold; padding: 6px 16px; border-radius: 4px; letter-spacing: 1px; }
        .receipt-num  { font-size: 9px; color: #64748b; margin-top: 4px; text-align: right; }

        /* Corps */
        .section-title { font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; margin: 0 0 8px; }
        .two-col { display: table; width: 100%; margin-bottom: 16px; }
        .col { display: table-cell; width: 50%; vertical-align: top; padding-right: 12px; }
        .col:last-child { padding-right: 0; padding-left: 12px; }
        .info-block { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px 12px; }
        .info-row { margin-bottom: 4px; font-size: 10px; }
        .info-label { color: #64748b; }
        .info-value { font-weight: bold; color: #0f172a; }

        /* Montants */
        .amounts { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 4px; padding: 14px 16px; margin: 16px 0; }
        .amount-row { display: table; width: 100%; padding: 3px 0; font-size: 10px; }
        .amount-row .label { display: table-cell; color: #475569; }
        .amount-row .value { display: table-cell; text-align: right; font-weight: bold; color: #1e293b; }
        .amount-total { border-top: 2px solid #1d4ed8; margin-top: 8px; padding-top: 8px; }
        .amount-total .label { font-size: 11px; font-weight: bold; color: #1d4ed8; }
        .amount-total .value { font-size: 14px; font-weight: bold; color: #1d4ed8; }

        /* Attestation */
        .attestation { border: 1px solid #e2e8f0; border-left: 4px solid #1d4ed8; padding: 10px 14px; margin: 16px 0; font-size: 10px; color: #334155; line-height: 1.6; }

        /* Footer */
        .footer { margin-top: 28px; border-top: 1px solid #e2e8f0; padding-top: 12px; display: table; width: 100%; }
        .footer-left { display: table-cell; width: 50%; vertical-align: top; font-size: 9px; color: #64748b; }
        .footer-right { display: table-cell; width: 50%; text-align: right; vertical-align: top; }
        .signature-box { border: 1px solid #cbd5e1; display: inline-block; padding: 8px 24px; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
<div class="page">

    {{-- En-tête --}}
    <div class="header">
        <div class="header-left">
            <p class="agency-name">{{ $receipt->payment->contract->agency->name ?? 'Caron Immobilier' }}</p>
            <p class="agency-sub">{{ $receipt->payment->contract->agency->address ?? '' }}{{ ($receipt->payment->contract->agency->city ?? '') ? ' — ' . $receipt->payment->contract->agency->city : '' }}</p>
            <p class="agency-sub">{{ $receipt->payment->contract->agency->phone ?? '' }}{{ ($receipt->payment->contract->agency->email ?? '') ? ' · ' . $receipt->payment->contract->agency->email : '' }}</p>
        </div>
        <div class="header-right">
            <div class="receipt-badge">QUITTANCE DE LOYER</div>
            <p class="receipt-num">N° {{ $receipt->receipt_number }}</p>
        </div>
    </div>

    {{-- Locataire / Propriétaire --}}
    <div class="two-col">
        <div class="col">
            <p class="section-title">Locataire</p>
            <div class="info-block">
                <div class="info-row"><span class="info-label">Nom : </span><span class="info-value">{{ $receipt->payment->contract->tenant?->full_name ?? '—' }}</span></div>
                @if($receipt->payment->contract->tenant?->phone)
                <div class="info-row"><span class="info-label">Tél : </span><span class="info-value">{{ $receipt->payment->contract->tenant->phone }}</span></div>
                @endif
                @if($receipt->payment->contract->tenant?->email)
                <div class="info-row"><span class="info-label">Email : </span><span class="info-value">{{ $receipt->payment->contract->tenant->email }}</span></div>
                @endif
            </div>
        </div>
        <div class="col">
            <p class="section-title">Propriétaire / Bailleur</p>
            <div class="info-block">
                @if($receipt->payment->contract->owner)
                <div class="info-row"><span class="info-label">Nom : </span><span class="info-value">{{ $receipt->payment->contract->owner->name }}</span></div>
                @if($receipt->payment->contract->owner->phone)
                <div class="info-row"><span class="info-label">Tél : </span><span class="info-value">{{ $receipt->payment->contract->owner->phone }}</span></div>
                @endif
                @else
                <div class="info-row"><span class="info-label">Bailleur : </span><span class="info-value">{{ $receipt->payment->contract->agency->name ?? 'Agence' }}</span></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bien + Contrat --}}
    <div class="two-col">
        <div class="col">
            <p class="section-title">Bien loué</p>
            <div class="info-block">
                <div class="info-row"><span class="info-value">{{ $receipt->payment->contract->property?->full_address ?? '—' }}</span></div>
                @if($receipt->payment->contract->property?->designation)
                <div class="info-row"><span class="info-label">Désignation : </span><span class="info-value">{{ $receipt->payment->contract->property->designation }}</span></div>
                @endif
                <div class="info-row"><span class="info-label">Ville : </span><span class="info-value">{{ $receipt->payment->contract->property?->city ?? '—' }}</span></div>
            </div>
        </div>
        <div class="col">
            <p class="section-title">Détails du paiement</p>
            <div class="info-block">
                <div class="info-row"><span class="info-label">Contrat : </span><span class="info-value">{{ $receipt->payment->contract->contract_number }}</span></div>
                <div class="info-row"><span class="info-label">Période : </span><span class="info-value">{{ \Carbon\Carbon::parse($receipt->payment->period . '-01')->isoFormat('MMMM YYYY') }}</span></div>
                <div class="info-row"><span class="info-label">Date de paiement : </span><span class="info-value">{{ $receipt->payment->payment_date->format('d/m/Y') }}</span></div>
                <div class="info-row"><span class="info-label">Méthode : </span><span class="info-value">{{ ucfirst(str_replace('_', ' ', $receipt->payment->payment_method)) }}</span></div>
                @if($receipt->payment->reference)
                <div class="info-row"><span class="info-label">Référence : </span><span class="info-value">{{ $receipt->payment->reference }}</span></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Montants --}}
    <div class="amounts">
        <div class="amount-row">
            <span class="label">Loyer mensuel</span>
            <span class="value">{{ number_format($receipt->payment->amount, 0, ',', ' ') }} FCFA</span>
        </div>
        @if(($receipt->payment->charges_amount ?? 0) > 0)
        <div class="amount-row">
            <span class="label">Charges</span>
            <span class="value">{{ number_format($receipt->payment->charges_amount, 0, ',', ' ') }} FCFA</span>
        </div>
        @endif
        @if(($receipt->payment->penalty_amount ?? 0) > 0)
        <div class="amount-row">
            <span class="label">Pénalités de retard</span>
            <span class="value">{{ number_format($receipt->payment->penalty_amount, 0, ',', ' ') }} FCFA</span>
        </div>
        @endif
        @if(($receipt->payment->commission_percent ?? 0) > 0)
        <div class="amount-row">
            <span class="label">Commission agence ({{ $receipt->payment->commission_percent }}%)</span>
            <span class="value">{{ number_format($receipt->payment->total_amount * $receipt->payment->commission_percent / 100, 0, ',', ' ') }} FCFA</span>
        </div>
        @endif
        <div class="amount-row amount-total">
            <span class="label">MONTANT TOTAL PAYÉ</span>
            <span class="value">{{ number_format($receipt->amount, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    {{-- Attestation --}}
    <div class="attestation">
        Je soussigné(e), <strong>{{ $receipt->payment->contract->agency->name ?? 'Caron Immobilier' }}</strong>, reconnais avoir reçu de
        <strong>{{ $receipt->payment->contract->tenant?->full_name ?? '—' }}</strong>
        la somme de <strong>{{ number_format($receipt->amount, 0, ',', ' ') }} FCFA</strong>
        en règlement du loyer du bien situé au <strong>{{ $receipt->payment->contract->property?->full_address ?? '—' }}</strong>
        pour la période de <strong>{{ \Carbon\Carbon::parse($receipt->payment->period . '-01')->isoFormat('MMMM YYYY') }}</strong>.
        <br>Quittance délivrée le {{ $receipt->issue_date->format('d/m/Y') }} à {{ $receipt->payment->contract->agency->city ?? 'Abidjan' }}.
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-left">
            <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
            <p>{{ $receipt->payment->contract->agency->name ?? 'Caron Immobilier' }} — Gestion Immobilière</p>
        </div>
        <div class="footer-right">
            <div class="signature-box">Cachet et signature</div>
        </div>
    </div>

</div>
</body>
</html>
