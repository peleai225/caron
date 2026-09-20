<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dépenses</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000000;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin: 0 0 4px 0;
        }
        .subtitle {
            text-align: center;
            font-size: 10px;
            color: #555555;
            margin-bottom: 20px;
        }
        .separator {
            border: none;
            border-top: 3px solid #dc2626;
            margin: 0 0 20px 0;
        }
        .total-block {
            background: #f9f9f9;
            border: 1px solid #dddddd;
            padding: 10px 16px;
            margin-bottom: 16px;
            display: inline-block;
        }
        .total-block .label {
            font-size: 9px;
            color: #555;
            text-transform: uppercase;
        }
        .total-block .value {
            font-size: 16px;
            font-weight: bold;
            color: #dc2626;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead th {
            background-color: #dc2626;
            color: #ffffff;
            padding: 7px 8px;
            font-size: 9px;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        tbody td {
            padding: 6px 8px;
            border-bottom: 1px solid #eeeeee;
            font-size: 10px;
            color: #222222;
        }
        tbody tr:nth-child(even) td {
            background-color: #fafafa;
        }
        .text-right { text-align: right; }
        tfoot td {
            padding: 7px 8px;
            font-weight: bold;
            font-size: 11px;
            border-top: 2px solid #dc2626;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #cccccc;
            padding-top: 8px;
            text-align: center;
            font-size: 8px;
            color: #888888;
        }
    </style>
</head>
<body>
    <h1>LISTE DES DÉPENSES</h1>
    <p class="subtitle">
        @if($filters['start_date'] || $filters['end_date'])
            Période : {{ $filters['start_date'] ? \Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') : '...' }}
            &mdash;
            {{ $filters['end_date'] ? \Carbon\Carbon::parse($filters['end_date'])->format('d/m/Y') : '...' }}
        @else
            Toutes les dépenses
        @endif
        @if($filters['type'])
            &bull; Type : {{ $filters['type'] }}
        @endif
    </p>
    <hr class="separator">

    <div class="total-block">
        <div class="label">Total des dépenses</div>
        <div class="value">{{ number_format($total, 0, ',', ' ') }} FCFA</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:12%">Date</th>
                <th style="width:18%">Type</th>
                <th style="width:22%">Bien</th>
                <th style="width:33%">Description</th>
                <th style="width:15%" class="text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
            <tr>
                <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                <td>{{ \App\Models\Expense::expenseTypes()[$expense->type] ?? $expense->type }}</td>
                <td>{{ $expense->property?->address ?? '—' }}</td>
                <td>{{ $expense->description }}</td>
                <td class="text-right">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:20px;color:#888;">Aucune dépense sur cette période.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">TOTAL</td>
                <td class="text-right">{{ number_format($total, 0, ',', ' ') }} FCFA</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Généré le {{ now()->format('d/m/Y à H:i') }} &mdash; Caron - Gestion Immobilière
    </div>
</body>
</html>
