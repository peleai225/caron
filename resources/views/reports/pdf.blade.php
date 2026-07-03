<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Financier</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1e293b;
            margin: 0;
            padding: 18px;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
            border-bottom: 3px solid #dc2626;
            padding-bottom: 16px;
        }
        .header h1 {
            margin: 0 0 6px 0;
            font-size: 22px;
            color: #0f172a;
        }
        .header .period {
            font-size: 13px;
            color: #64748b;
        }
        .stats-row {
            margin-bottom: 20px;
            overflow: hidden;
        }
        .stat-card {
            float: left;
            width: 16.3%;
            margin: 0 0.5% 10px 0.5%;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 6px;
            background: #f8fafc;
        }
        .stat-label {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .stat-value.negative { color: #dc2626; }
        .stat-value.positive { color: #16a34a; }
        .content-row {
            overflow: hidden;
            margin-bottom: 20px;
        }
        .content-col {
            float: left;
            width: 48%;
            margin: 0 1%;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 10px 0;
            padding-bottom: 6px;
            border-bottom: 2px solid #dc2626;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #dc2626;
            color: white;
            font-weight: 600;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .footer {
            clear: both;
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>RAPPORT FINANCIER</h1>
        <p class="period">Période : {{ \Carbon\Carbon::parse($data['period']['start'])->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($data['period']['end'])->format('d/m/Y') }}</p>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Revenus totaux</div>
            <div class="stat-value">{{ number_format($data['total_revenue'] ?? 0, 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Dépenses</div>
            <div class="stat-value">{{ number_format($data['total_expenses'] ?? 0, 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Impayés</div>
            <div class="stat-value negative">{{ number_format($data['overdue_amount'] ?? 0, 0, ',', ' ') }} FCFA</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Paiements reçus</div>
            <div class="stat-value">{{ $data['total_payments'] ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Taux recouvrement</div>
            <div class="stat-value">{{ number_format($data['recovery_rate'] ?? 0, 2) }}%</div>
        </div>
        @php $netProfit = ($data['total_revenue'] ?? 0) - ($data['total_expenses'] ?? 0); @endphp
        <div class="stat-card">
            <div class="stat-label">Bénéfice net</div>
            <div class="stat-value {{ $netProfit >= 0 ? 'positive' : 'negative' }}">{{ number_format($netProfit, 0, ',', ' ') }} FCFA</div>
        </div>
    </div>

    <div class="content-row">
        <div class="content-col">
            @if(!empty($data['by_property']))
            <h2 class="section-title">Revenus par bien</h2>
            <table>
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th style="text-align:right;">Revenus</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_property'] as $item)
                    <tr>
                        <td>{{ $item['property_address'] }}</td>
                        <td style="text-align:right;">{{ number_format($item['revenue'], 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <h2 class="section-title">Revenus par bien</h2>
            <p>Aucune donnée sur cette période.</p>
            @endif
        </div>
        <div class="content-col">
            @if(!empty($data['by_payment_method']))
            <h2 class="section-title">Revenus par méthode de paiement</h2>
            <table>
                <thead>
                    <tr>
                        <th>Méthode</th>
                        <th style="text-align:right;">Montant</th>
                        <th style="text-align:center;">Nb</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['by_payment_method'] as $item)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $item['payment_method'] ?? 'non_specifie')) }}</td>
                        <td style="text-align:right;">{{ number_format($item['revenue'], 0, ',', ' ') }} FCFA</td>
                        <td style="text-align:center;">{{ $item['count'] ?? 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <h2 class="section-title">Revenus par méthode de paiement</h2>
            <p>Aucune donnée sur cette période.</p>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>Rapport généré le {{ now()->format('d/m/Y à H:i') }} — Caron - Gestion Immobilière</p>
    </div>
</body>
</html>
