@extends('layouts.app')

@section('title', 'Litiges')
@section('page-title', 'Litiges')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Litiges</h2>
            <p class="page-subtitle">Liste des litiges et suivi juridique</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('litiges.rapport') }}" class="btn-secondary">Rapport juridique</a>
            <a href="{{ route('litiges.create') }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nouveau litige
            </a>
        </div>
    </header>

    <!-- Filters -->
    <form method="GET" class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bailleur</label>
                    <select name="owner_id" class="input-modern">
                        <option value="">Tous</option>
                        @foreach($owners as $o)
                            <option value="{{ $o->id }}" {{ request('owner_id') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nature</label>
                    <select name="nature_litige" class="input-modern">
                        <option value="">Toutes</option>
                        @foreach(\App\Models\Litige::naturesLitige() as $k => $v)
                            <option value="{{ $k }}" {{ request('nature_litige') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut</label>
                    <select name="statut" class="input-modern">
                        <option value="">Tous</option>
                        <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="regle" {{ request('statut') == 'regle' ? 'selected' : '' }}>Regle</option>
                        <option value="cloture" {{ request('statut') == 'cloture' ? 'selected' : '' }}>Cloture</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Date debut</label>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="input-modern">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary">Filtrer</button>
                    <a href="{{ route('litiges.index') }}" class="btn-secondary">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="card-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Personnes / Lieu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Type contrat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Bailleur</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nature</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($litiges as $litige)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-slate-900">{{ $litige->personnes_concernées ?? $litige->tenant?->first_name . ' ' . $litige->tenant?->last_name ?? '—' }}</p>
                            @if($litige->lieu_intervention)
                                <p class="text-xs text-slate-500">{{ $litige->lieu_intervention }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ \App\Models\Litige::typesContrat()[$litige->type_contrat] ?? $litige->type_contrat ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $litige->owner?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ \App\Models\Litige::naturesLitige()[$litige->nature_litige] ?? $litige->nature_litige ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($litige->statut === 'en_cours')
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20">En cours</span>
                            @elseif($litige->statut === 'regle')
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">Regle</span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-slate-50 text-slate-600 ring-slate-500/20">Cloture</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                @if($litige->contract_id)
                                    <a href="{{ route('contracts.show', $litige->contract_id) }}" class="px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded transition-colors">Contrat</a>
                                @endif
                                <a href="{{ route('litiges.show', $litige) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                                <form id="form-delete-litige-{{ $litige->id }}" action="{{ route('litiges.destroy', $litige) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" data-confirm="Supprimer ce litige ?" data-confirm-form="form-delete-litige-{{ $litige->id }}" class="px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 rounded transition-colors">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="empty-state-icon">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucun litige enregistre</h3>
                            <p class="text-xs text-slate-500 mb-4">Les litiges apparaitront ici</p>
                            <a href="{{ route('litiges.create') }}" class="btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Nouveau litige
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($litiges->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $litiges->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
