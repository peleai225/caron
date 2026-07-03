<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport de situation juridique - Litiges</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; line-height: 1.4; }
        .container { padding: 30px; }
        h1 { font-size: 18px; color: #dc2626; margin-bottom: 20px; border-bottom: 2px solid #dc2626; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px 10px; border: 1px solid #e2e8f0; text-align: left; }
        th { background: #dc2626; color: white; font-size: 10px; text-transform: uppercase; }
        tr:nth-child(even) { background: #f8fafc; }
        .text-right { text-align: right; }
        .small { font-size: 9px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Rapport de situation juridique - Litiges immobiliers</h1>
    <p class="small">Généré le {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Réf.</th>
                <th>Personnes / Lieu</th>
                <th>Type contrat</th>
                <th>Bailleur</th>
                <th>Nature litige</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Statut</th>
                <th>Suivi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($litiges as $l)
                <tr>
                    <td>{{ $l->reference ?? '—' }}</td>
                    <td>{{ $l->personnes_concernées ?? ($l->tenant ? $l->tenant->first_name . ' ' . $l->tenant->last_name : '—') }}<br><span class="small">{{ $l->lieu_intervention ?? '' }}</span></td>
                    <td>{{ \App\Models\Litige::typesContrat()[$l->type_contrat] ?? $l->type_contrat ?? '—' }}</td>
                    <td>{{ $l->owner?->name ?? '—' }}</td>
                    <td>{{ \App\Models\Litige::naturesLitige()[$l->nature_litige] ?? $l->nature_litige ?? '—' }}</td>
                    <td>{{ $l->date_debut?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $l->date_fin?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $l->statut }}</td>
                    <td class="small">{{ Str::limit($l->suivi_commentaires, 80) }}</td>
                </tr>
            @empty
                <tr><td colspan="9">Aucun litige pour la période sélectionnée.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
