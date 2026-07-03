@extends('layouts.app')

@section('title', 'Détails de la transaction')
@section('page-title', 'Détails de la transaction')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Détails de la transaction</h2>
            <p class="page-subtitle">Informations complètes</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn-secondary">Retour</a>
    </header>

    <div class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Date</p>
                    <p class="text-sm font-medium text-slate-900">{{ $transaction->transaction_date->format('d/m/Y') }}</p>
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Compte</p>
                    <p class="text-sm font-medium text-slate-900">
                        <a href="{{ route('accounts.show', $transaction->account) }}" class="text-primary-600 hover:text-primary-700">
                            {{ $transaction->account->name }}
                        </a>
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Type</p>
                    <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md ring-1 ring-inset {{ $transaction->type === 'income' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-red-50 text-red-700 ring-red-600/20' }}">
                        {{ $transaction->type === 'income' ? 'Entrée' : 'Sortie' }}
                    </span>
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Montant</p>
                    <p class="text-lg font-bold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                        {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', ' ') }} FCFA
                    </p>
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Statut</p>
                    <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md ring-1 ring-inset {{ $transaction->status === 'completed' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </div>

                @if($transaction->reference)
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Référence</p>
                    <p class="text-sm font-medium text-slate-900">{{ $transaction->reference }}</p>
                </div>
                @endif

                @if($transaction->description)
                <div class="md:col-span-2">
                    <p class="text-xs text-slate-500 mb-0.5">Description</p>
                    <p class="text-sm text-slate-700">{{ $transaction->description }}</p>
                </div>
                @endif

                @if($transaction->payment)
                <div class="md:col-span-2">
                    <p class="text-xs text-slate-500 mb-0.5">Paiement associé</p>
                    <a href="{{ route('rents.show', $transaction->payment) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                        Paiement #{{ $transaction->payment->id }} - {{ number_format($transaction->payment->amount, 0, ',', ' ') }} FCFA
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
