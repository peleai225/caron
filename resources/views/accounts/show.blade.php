@extends('layouts.app')

@section('title', 'Details du compte')
@section('page-title', 'Details du compte')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">{{ $account->name }}</h2>
            <p class="page-subtitle">Details et transactions du compte</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('accounts.edit', $account) }}" class="btn-primary">Modifier</a>
            <a href="{{ route('accounts.index') }}" class="btn-secondary">Retour</a>
        </div>
    </header>

    <!-- Balance Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500">Solde actuel</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($account->balance, 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500">Type</p>
            <p class="text-sm font-semibold text-slate-900 mt-1">
                @if($account->type === 'cash') Especes
                @elseif($account->type === 'bank') Banque
                @else Mobile Money
                @endif
            </p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500">Statut</p>
            <div class="mt-1">
                @if($account->is_active)
                    <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Actif</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-slate-50 text-slate-600 ring-1 ring-inset ring-slate-500/20">Inactif</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Account Details -->
    @if($account->account_number || $account->bank_name)
    <div class="card-panel">
        <div class="card-panel-header">Informations</div>
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($account->account_number)
                <div>
                    <p class="text-xs text-slate-500">Numero de compte</p>
                    <p class="text-sm font-medium text-slate-900">{{ $account->account_number }}</p>
                </div>
                @endif
                @if($account->bank_name)
                <div>
                    <p class="text-xs text-slate-500">Banque</p>
                    <p class="text-sm font-medium text-slate-900">{{ $account->bank_name }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Transactions -->
    <div class="card-panel">
        <div class="card-panel-header">Transactions recentes</div>
        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="px-4 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-2.5 text-right font-semibold text-slate-600 uppercase tracking-wider">Montant</th>
                            <th class="px-4 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($transactions as $transaction)
                        <tr class="hover:bg-slate-25">
                            <td class="px-4 py-2.5 text-slate-700">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5">
                                <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md {{ $transaction->type === 'income' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20' : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20' }}">
                                    {{ $transaction->type === 'income' ? 'Entree' : 'Sortie' }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-slate-700">{{ $transaction->description ?? 'N/A' }}</td>
                            <td class="px-4 py-2.5 text-right font-semibold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-4 py-2.5"><x-status-badge :status="$transaction->status" /></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="card-panel-body text-center py-8">
                <p class="text-xs text-slate-400">Aucune transaction enregistree</p>
            </div>
        @endif
    </div>
</div>
@endsection
