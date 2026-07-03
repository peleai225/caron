@extends('layouts.app')

@section('title', 'Etat des lieux')
@section('page-title', 'Etat des lieux')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">{{ $etatDesLieux->type === 'entree' ? 'Entree' : 'Sortie' }} — {{ $etatDesLieux->property->name ?? $etatDesLieux->property->address }}</h2>
            <p class="page-subtitle">Etat des lieux du {{ $etatDesLieux->date->format('d/m/Y') }}</p>
        </div>
        <div>
            @if($etatDesLieux->type === 'entree')
                <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Entree</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">Sortie</span>
            @endif
        </div>
    </header>

    <div class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Bien</p>
                    <p class="text-sm font-medium text-slate-900">{{ $etatDesLieux->property->name ?? $etatDesLieux->property->address }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Date</p>
                    <p class="text-sm font-medium text-slate-900">{{ $etatDesLieux->date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Locataire</p>
                    <p class="text-sm font-medium text-slate-900">{{ $etatDesLieux->contract?->tenant?->full_name ?? '—' }}</p>
                </div>
            </div>

            @if($etatDesLieux->observations)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-1">Observations</p>
                <p class="text-sm text-slate-700 whitespace-pre-wrap">{{ $etatDesLieux->observations }}</p>
            </div>
            @endif
        </div>
    </div>

    <a href="{{ route('etat-des-lieux.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-700 flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour a la liste
    </a>
</div>
@endsection
