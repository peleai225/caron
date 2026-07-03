@extends('layouts.app')

@section('title', 'Details du litige')
@section('page-title', 'Details du litige')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">{{ $litige->reference ?? 'Litige #' . $litige->id }}</h2>
            <p class="page-subtitle">Details de l'affaire</p>
        </div>
        <div class="flex items-center gap-2">
            @if($litige->contract_id)
                <a href="{{ route('contracts.show', $litige->contract) }}" class="btn-secondary">Contrat</a>
            @endif
            <a href="{{ route('litiges.edit', $litige) }}" class="btn-primary">Modifier</a>
            <form id="form-delete-litige-{{ $litige->id }}" action="{{ route('litiges.destroy', $litige) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" data-confirm="Supprimer ce litige ?" data-confirm-form="form-delete-litige-{{ $litige->id }}" class="btn-danger">Supprimer</button>
            </form>
        </div>
    </header>

    <div class="card-panel">
        <div class="card-panel-body space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Personnes concernees</p>
                    <p class="text-sm font-medium text-slate-900">{{ $litige->personnes_concernées ?? ($litige->tenant ? $litige->tenant->first_name . ' ' . $litige->tenant->last_name : '—') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Lieu d'intervention</p>
                    <p class="text-sm font-medium text-slate-900">{{ $litige->lieu_intervention ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Type de contrat</p>
                    <p class="text-sm font-medium text-slate-900">{{ \App\Models\Litige::typesContrat()[$litige->type_contrat] ?? $litige->type_contrat ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Bailleur</p>
                    <p class="text-sm font-medium text-slate-900">{{ $litige->owner?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Nature du litige</p>
                    <p class="text-sm font-medium text-slate-900">{{ \App\Models\Litige::naturesLitige()[$litige->nature_litige] ?? $litige->nature_litige ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Statut</p>
                    @if($litige->statut === 'en_cours')
                        <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">En cours</span>
                    @elseif($litige->statut === 'regle')
                        <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Regle</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-slate-50 text-slate-600 ring-1 ring-inset ring-slate-500/20">Cloture</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Dates</p>
                    <p class="text-sm font-medium text-slate-900">{{ $litige->date_debut?->format('d/m/Y') ?? '—' }} — {{ $litige->date_fin?->format('d/m/Y') ?? '—' }}</p>
                </div>
            </div>

            @if($litige->description)
            <div class="pt-3 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-1">Description</p>
                <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $litige->description }}</p>
            </div>
            @endif

            @if($litige->couts_engages && count($litige->couts_engages) > 0)
            <div class="pt-3 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-1.5">Couts engages</p>
                <div class="space-y-1">
                    @foreach($litige->couts_engages as $lib => $montant)
                        @if($montant > 0)
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-600">{{ str_replace('_', ' ', ucfirst($lib)) }}</span>
                                <span class="font-medium text-slate-900">{{ number_format($montant, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            @if($litige->pertes_financieres && count($litige->pertes_financieres) > 0)
            <div class="pt-3 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-1.5">Pertes financieres</p>
                <div class="space-y-1">
                    @foreach($litige->pertes_financieres as $lib => $montant)
                        @if($montant > 0)
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-600">{{ str_replace('_', ' ', ucfirst($lib)) }}</span>
                                <span class="font-medium text-red-600">{{ number_format($montant, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            @if($litige->suivi_commentaires)
            <div class="pt-3 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-1">Suivi / Commentaires</p>
                <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $litige->suivi_commentaires }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
