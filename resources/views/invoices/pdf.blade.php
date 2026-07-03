<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1e293b; line-height: 1.5; }
        .container { padding: 40px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; border-bottom: 3px solid #dc2626; padding-bottom: 20px; }
        .company-info { }
        .company-name { font-size: 22px; font-weight: bold; color: #dc2626; margin-bottom: 5px; }
        .company-details { font-size: 10px; color: #64748b; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 28px; color: #dc2626; font-weight: bold; }
        .invoice-number { font-size: 14px; color: #64748b; margin-top: 5px; }
        .invoice-meta { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .meta-block { width: 48%; }
        .meta-block h3 { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #dc2626; margin-bottom: 8px; font-weight: bold; }
        .meta-block p { font-size: 11px; color: #475569; margin-bottom: 3px; }
        .meta-block .name { font-size: 14px; font-weight: bold; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        thead th { background: #dc2626; color: white; padding: 10px 15px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        .text-right { text-align: right; }
        .totals { width: 300px; margin-left: auto; }
        .totals table { margin-bottom: 0; }
        .totals td { padding: 8px 15px; }
        .totals .total-row { background: #dc2626; color: white; font-weight: bold; font-size: 14px; }
        .totals .total-row td { padding: 12px 15px; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 10px; color: #94a3b8; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-paid { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-overdue { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <table style="width:100%; margin-bottom: 30px; border-bottom: 3px solid #dc2626; padding-bottom: 15px;">
            <tr>
                <td style="border: none; padding: 0;">
                    <div class="company-name">
                        @if($invoice->contract && $invoice->contract->agency)
                            {{ $invoice->contract->agency->name ?? 'Caron Immobilier' }}
                        @else
                            Caron Immobilier
                        @endif
                    </div>
                    <div class="company-details">
                        @if($invoice->contract && $invoice->contract->agency)
                            {{ $invoice->contract->agency->address ?? '' }}<br>
                            {{ $invoice->contract->agency->phone ?? '' }}<br>
                            {{ $invoice->contract->agency->email ?? '' }}
                        @endif
                    </div>
                </td>
                <td style="border: none; padding: 0; text-align: right;">
                    <h1 style="font-size: 28px; color: #dc2626; font-weight: bold; margin: 0;">FACTURE</h1>
                    <p style="font-size: 14px; color: #64748b; margin-top: 5px;">{{ $invoice->invoice_number }}</p>
                    <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                </td>
            </tr>
        </table>

        <!-- Meta Info -->
        <table style="width: 100%; margin-bottom: 30px;">
            <tr>
                <td style="width: 50%; vertical-align: top; border: none; padding: 0 10px 0 0;">
                    @if($invoice->invoice_type === 'commission')
                        <h3 style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #dc2626; margin-bottom: 8px; font-weight: bold;">Propriétaire</h3>
                        @if($invoice->contract && $invoice->contract->owner)
                            <p class="name" style="font-size: 14px; font-weight: bold; color: #0f172a;">{{ $invoice->contract->owner->name }}</p>
                            <p style="font-size: 11px; color: #475569;">{{ $invoice->contract->owner->phone ?? '' }}</p>
                            <p style="font-size: 11px; color: #475569;">{{ $invoice->contract->owner->email ?? '' }}</p>
                        @endif
                    @else
                        <h3 style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #dc2626; margin-bottom: 8px; font-weight: bold;">Facturé à</h3>
                        @if($invoice->contract && $invoice->contract->tenant)
                            <p class="name" style="font-size: 14px; font-weight: bold; color: #0f172a;">{{ $invoice->contract->tenant->first_name }} {{ $invoice->contract->tenant->last_name }}</p>
                            <p style="font-size: 11px; color: #475569;">{{ $invoice->contract->tenant->phone ?? '' }}</p>
                            <p style="font-size: 11px; color: #475569;">{{ $invoice->contract->tenant->email ?? '' }}</p>
                        @endif
                    @endif
                </td>
                <td style="width: 50%; vertical-align: top; border: none; padding: 0 0 0 10px;">
                    <h3 style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #dc2626; margin-bottom: 8px; font-weight: bold;">Détails</h3>
                    <p style="font-size: 11px; color: #475569;">Date d'émission : {{ $invoice->issue_date->format('d/m/Y') }}</p>
                    <p style="font-size: 11px; color: #475569;">Date d'échéance : {{ $invoice->due_date->format('d/m/Y') }}</p>
                    @if($invoice->contract && $invoice->contract->property)
                        <p style="font-size: 11px; color: #475569;">Bien : {{ $invoice->contract->property->address }}</p>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Période</th>
                    <th class="text-right">Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @if($invoice->invoice_type === 'commission')
                            {{ $invoice->description ?? 'Commission de gestion' }}
                        @else
                            Loyer mensuel
                        @endif
                    </td>
                    <td>
                        @if($invoice->payment)
                            {{ \Carbon\Carbon::parse($invoice->payment->period)->translatedFormat('F Y') }}
                        @elseif($invoice->contract)
                            {{ $invoice->contract->start_date->format('d/m/Y') }} - {{ $invoice->contract->end_date->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</td>
                </tr>
                @if($invoice->tax_amount > 0)
                <tr>
                    <td>TVA</td>
                    <td>-</td>
                    <td class="text-right">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endif
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td><strong>Sous-total HT</strong></td>
                    <td class="text-right">{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</td>
                </tr>
                @if($invoice->tax_amount > 0)
                <tr>
                    <td>TVA</td>
                    <td class="text-right">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>TOTAL TTC</td>
                    <td class="text-right">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Merci pour votre confiance.</p>
            <p style="margin-top: 5px;">
                @if($invoice->contract && $invoice->contract->agency)
                    {{ $invoice->contract->agency->name ?? 'Caron' }} - Gestion Immobilière
                @else
                    Caron - Gestion Immobilière
                @endif
            </p>
        </div>
    </div>
</body>
</html>
