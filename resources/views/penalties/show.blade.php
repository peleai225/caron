@extends('layouts.app')

@section('title', 'Details de la penalite')
@section('page-title', 'Details de la penalite')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Penalite</h2>
            <p class="page-subtitle">{{ $penalty->paymentSchedule->contract->contract_number ?? 'N/A' }} — {{ $penalty->paymentSchedule->contract->tenant->first_name ?? '' }} {{ $penalty->paymentSchedule->contract->tenant->last_name ?? '' }}</p>
        </div>
    </header>

    <div class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Contrat</p>
                    <p class="text-sm font-medium text-slate-900">{{ $penalty->paymentSchedule->contract->contract_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Locataire</p>
                    <p class="text-sm font-medium text-slate-900">{{ $penalty->paymentSchedule->contract->tenant->first_name ?? '' }} {{ $penalty->paymentSchedule->contract->tenant->last_name ?? '' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Montant</p>
                    <p class="text-lg font-bold text-red-600">{{ number_format($penalty->amount, 0, ',', ' ') }} FCFA</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Statut</p>
                    @if($penalty->status === 'paid')
                        <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Payee</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20">Impayee</span>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Date de creation</p>
                    <p class="text-sm font-medium text-slate-900">{{ $penalty->created_at->format('d/m/Y a H:i') }}</p>
                </div>
                @if($penalty->paid_at)
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Date de paiement</p>
                    <p class="text-sm font-medium text-slate-900">{{ $penalty->paid_at->format('d/m/Y a H:i') }}</p>
                </div>
                @endif
            </div>

            @if($penalty->description)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-1">Description</p>
                <p class="text-sm text-slate-700">{{ $penalty->description }}</p>
            </div>
            @endif
        </div>

        @if($penalty->status === 'unpaid')
        <div class="px-5 py-4 border-t border-slate-100 flex items-center gap-3">
            <form action="{{ route('penalties.mark-as-paid', $penalty) }}" method="POST" class="inline">
                @csrf
                @method('PUT')
                <button type="submit" class="btn-primary">Marquer comme payee</button>
            </form>
        </div>
        @endif
    </div>

    <a href="{{ route('penalties.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-700 flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour a la liste
    </a>
</div>
@endsection
