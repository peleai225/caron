<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Financier</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000000;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            font-size: 20px;
            margin: 0 0 5px 0;
            color: #000000;
        }
        .period {
            text-align: center;
            font-size: 12px;
            color: #333333;
            margin-bottom: 15px;
        }
        .separator {
            border: none;
            border-top: 3px solid #dc2626;
            margin: 10px 0 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .stats-table td {
            border: 1px solid #999999;
            padding: 10px;
            text-align: center;
            width: 16.6%;
            vertical-align: top;
        }
        .stats-table .label {
            font-size: 8px;
            color: #333333;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .stats-table .value {
            font-size: 13px;
            font-weight: bold;
            color: #000000;
        }
        .stats-table .value-red {
            font-size: 13px;
            font-weight: bold;
            color: #dc2626;
        }
        .stats-table .value-green {
            font-size: 13px;
            font-weight: bold;
            color: #16a34a;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #000000;
            margin: 20px 0 8px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #dc2626;
        }
        .data-table th {
            background-color: #dc2626;
            color: #ffffff;
            padding: 8px;
            font-size: 10px;
            text-align: left;
            border: 1px solid #dc2626;
        }
        .data-table td {
            padding: 7px 8px;
            border: 1px solid #cccccc;
            font-size: 10px;
            color: #000000;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f5f5f5;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #999999;
            text-align: center;
            font-size: 9px;
            color: #666666;
        }
        .two-columns {
            width: 100%;
        }
        .two-columns td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
    </style>
</head>
<body>
    <h1>RAPPORT FINANCIER</h1>
    <p class="period">Periode : {{ \Carbon\Carbon::parse($data['period']['start'])->format('d/m/Y') }} &mdash; {{ \Carbon\Carbon::parse($data['period']['end'])->format('d/m/Y') }}</p>
    <hr class="separator">

    @php $netProfit = ($data['total_revenue'] ?? 0) - ($data['total_expenses'] ?? 0); @endphp

    <table class="stats-table">
        <tr>
            <td>
                <div class="label">Revenus totaux</div>
                <div class="value">{{ number_format($data['total_revenue'] ?? 0, 0, ',', ' ') }} FCFA</div>
            </td>
            <td>
                <div class="label">Depenses</div>
                <div class="value">{{ number_format($data['total_expenses'] ?? 0, 0, ',', ' ') }} FCFA</div>
            </td>
            <td>
                <div class="label">Impayes</div>
                <div class="value-red">{{ number_format($data['overdue_amount'] ?? 0, 0, ',', ' ') }} FCFA</div>
            </td>
            <td>
                <div class="label">Paiements recus</div>
                <div class="value">{{ $data['total_payments'] ?? 0 }}</div>
            </td>
            <td>
                <div class="label">Taux recouvrement</div>
                <div class="value">{{ number_format($data['recovery_rate'] ?? 0, 2) }}%</div>
            </td>
            <td>
                <div class="label">Benefice net</div>
                <div class="{{ $netProfit >= 0 ? 'value-green' : 'value-red' }}">{{ number_format($netProfit, 0, ',', ' ') }} FCFA</div>
            </td>
        </tr>
    </table>

    <table class="two-columns">
        <tr>
            <td>
                <p class="section-title">Revenus par bien</p>
                @if(!empty($data['by_property']))
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Bien</th>
                            <th class="text-right">Revenus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['by_property'] as $item)
                        <tr>
                            <td>{{ $item['property_address'] }}</td>
                            <td class="text-right">{{ number_format($item['revenue'], 0, ',', ' ') }} FCFA</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p>Aucune donnee sur cette periode.</p>
                @endif
            </td>
            <td>
                <p class="section-title">Revenus par methode de paiement</p>
                @if(!empty($data['by_payment_method']))
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Methode</th>
                            <th class="text-right">Montant</th>
                            <th class="text-center">Nb</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['by_payment_method'] as $item)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $item['payment_method'] ?? 'non specifie')) }}</td>
                            <td class="text-right">{{ number_format($item['revenue'], 0, ',', ' ') }} FCFA</td>
                            <td class="text-center">{{ $item['count'] ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p>Aucune donnee sur cette periode.</p>
                @endif
            </td>
        </tr>
    </table>

    <div class="footer">
        Rapport genere le {{ now()->format('d/m/Y a H:i') }} &mdash; Caron - Gestion Immobiliere
    </div>
</body>
</html>
