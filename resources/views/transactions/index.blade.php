@extends('layouts.app')

@section('title', 'Transactions')
@section('page-title', 'Transactions')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Transactions</h2>
            <p class="page-subtitle">Historique de toutes les transactions</p>
        </div>
    </header>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total entrees</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_income'], 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total sorties</span>
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ number_format($stats['total_expense'], 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('transactions.index') }}" class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Compte</label>
                    <select name="account_id" class="input-modern">
                        <option value="">Tous les comptes</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type</label>
                    <select name="type" class="input-modern">
                        <option value="">Tous</option>
                        <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Entree</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Sortie</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut</label>
                    <select name="status" class="input-modern">
                        <option value="">Tous</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Complete</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary flex-1">Filtrer</button>
                    <a href="{{ route('transactions.index') }}" class="btn-secondary">Reset</a>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Compte</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Montant</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $transaction->account->name }}</td>
                        <td class="px-4 py-3">
                            @if($transaction->type === 'income')
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">Entree</span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-red-50 text-red-700 ring-red-600/20">Sortie</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $transaction->description ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-right {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', ' ') }} F
                        </td>
                        <td class="px-4 py-3">
                            <x-status-badge :status="$transaction->status" size="sm" />
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('transactions.show', $transaction) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="empty-state-icon">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucune transaction</h3>
                            <p class="text-xs text-slate-500">Les transactions apparaitront ici</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
