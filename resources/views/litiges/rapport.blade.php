@extends('layouts.app')

@section('title', 'Rapport de situation juridique')
@section('page-title', 'Rapport de situation juridique')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Rapport de situation juridique</h2>
            <p class="page-subtitle">Analyse de vos situations juridiques</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('litiges.export.excel', request()->query()) }}" class="btn-secondary">Excel</a>
            <a href="{{ route('litiges.export.word', request()->query()) }}" class="btn-secondary">Word</a>
            <a href="{{ route('litiges.export.pdf', request()->query()) }}" class="btn-primary">PDF</a>
        </div>
    </header>

    <div class="card-panel">
        <form method="GET" action="{{ route('litiges.rapport') }}">
            <div class="card-panel-body">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date de début</label>
                        <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date de fin</label>
                        <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Bailleurs concernés</label>
                        <select name="owner_id" class="input-modern">
                            <option value="">Tous</option>
                            @foreach($owners as $o)
                                <option value="{{ $o->id }}" {{ request('owner_id') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="btn-primary">Générer</button>
                        <a href="{{ route('litiges.rapport') }}" class="btn-secondary">Réinitialiser</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card-panel">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Personnes / ID</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Type contrat</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Bailleur</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Nature</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Suivi</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Coûts</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Pertes</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($litiges as $l)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-5 py-3">
                            <p class="text-xs font-medium text-slate-900">{{ $l->personnes_concernées ?? ($l->tenant ? $l->tenant->first_name . ' ' . $l->tenant->last_name : '—') }}</p>
                            <p class="text-[11px] text-slate-500">{{ $l->lieu_intervention ?? '' }}</p>
                        </td>
                        <td class="px-5 py-3 text-xs text-slate-600">{{ \App\Models\Litige::typesContrat()[$l->type_contrat] ?? $l->type_contrat ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs text-slate-600">{{ $l->owner?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs text-slate-600">{{ \App\Models\Litige::naturesLitige()[$l->nature_litige] ?? $l->nature_litige ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs text-slate-600 max-w-[200px] truncate">{{ Str::limit($l->suivi_commentaires, 60) }}</td>
                        <td class="px-5 py-3 text-xs text-slate-600">
                            @if($l->couts_engages && count($l->couts_engages) > 0)
                                {{ number_format(array_sum($l->couts_engages), 0, ',', ' ') }} FCFA
                            @else — @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-slate-600">
                            @if($l->pertes_financieres && count($l->pertes_financieres) > 0)
                                {{ number_format(array_sum($l->pertes_financieres), 0, ',', ' ') }} FCFA
                            @else — @endif
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('litiges.show', $l) }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Voir</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-xs text-slate-500">Aucun litige pour les critères sélectionnés.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('litiges.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-700">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour aux litiges
    </a>
</div>
@endsection
