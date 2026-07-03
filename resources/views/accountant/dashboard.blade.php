@extends('layouts.app')

@section('title', 'Dashboard Comptable')
@section('page-title', 'Dashboard Comptable')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Dashboard Comptable</h2>
            <p class="page-subtitle">Gestion financière — {{ now()->format('F Y') }}</p>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500 mb-1">Revenus du Mois</p>
            <p class="text-xl font-bold text-emerald-600">{{ number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') }} FCFA</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Reçus</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500 mb-1">Dépenses du Mois</p>
            <p class="text-xl font-bold text-red-600">{{ number_format($stats['total_expenses'] ?? 0, 0, ',', ' ') }} FCFA</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Engagées</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500 mb-1">Bénéfice Net</p>
            <p class="text-xl font-bold {{ ($stats['net_profit'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($stats['net_profit'] ?? 0, 0, ',', ' ') }} FCFA</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Revenus - Dépenses</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500 mb-1">Paiements en Attente</p>
            <p class="text-xl font-bold text-amber-600">{{ number_format($stats['pending_payments'] ?? 0, 0, ',', ' ') }} FCFA</p>
            <p class="text-[11px] text-slate-400 mt-0.5">À recevoir</p>
        </div>
    </div>

    <div class="card-panel">
        <div class="border-b border-slate-100">
            <nav class="flex -mb-px" id="dashboard-tabs" role="tablist">
                <button class="tab-button active px-5 py-3 text-xs font-semibold text-primary-700 border-b-2 border-primary-600 transition-all" data-tab="overview">Vue d'ensemble</button>
                <button class="tab-button px-5 py-3 text-xs font-semibold text-slate-500 border-b-2 border-transparent hover:text-primary-700 hover:border-primary-600 transition-all" data-tab="transactions">Transactions</button>
                <button class="tab-button px-5 py-3 text-xs font-semibold text-slate-500 border-b-2 border-transparent hover:text-primary-700 hover:border-primary-600 transition-all" data-tab="expenses">Dépenses</button>
                <button class="tab-button px-5 py-3 text-xs font-semibold text-slate-500 border-b-2 border-transparent hover:text-primary-700 hover:border-primary-600 transition-all" data-tab="accounts">Comptes</button>
                <button class="tab-button px-5 py-3 text-xs font-semibold text-slate-500 border-b-2 border-transparent hover:text-primary-700 hover:border-primary-600 transition-all" data-tab="invoices">Factures</button>
            </nav>
        </div>

        <div class="card-panel-body">
            <div id="tab-overview" class="tab-content active">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-3">Transactions Récentes</h3>
                        @if($recentTransactions->count() > 0)
                        <div class="space-y-2">
                            @foreach($recentTransactions as $transaction)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                                <div>
                                    <p class="text-xs font-medium text-slate-900">{{ $transaction->description ?? 'Transaction' }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $transaction->account->name ?? 'N/A' }} — {{ $transaction->transaction_date->format('d/m/Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', ' ') }} FCFA
                                    </p>
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded ring-1 ring-inset {{ $transaction->type === 'income' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-red-50 text-red-700 ring-red-600/20' }}">
                                        {{ $transaction->type === 'income' ? 'Entrée' : 'Sortie' }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <div class="empty-state-icon mx-auto mb-2"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg></div>
                            <p class="text-xs text-slate-500">Aucune transaction récente</p>
                        </div>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-3">Dépenses par Catégorie</h3>
                        @if($expensesByCategory->count() > 0)
                        <div class="space-y-2">
                            @foreach($expensesByCategory as $expense)
                            <div class="p-3 bg-slate-50 rounded-lg">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs font-medium text-slate-900">{{ ucfirst($expense->category ?? 'Autre') }}</span>
                                    <span class="text-xs font-bold text-red-600">{{ number_format($expense->total, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-1.5">
                                    <div class="bg-primary-500 h-1.5 rounded-full" style="width: {{ min(100, ($expense->total / max($expensesByCategory->sum('total'), 1)) * 100) }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <p class="text-xs text-slate-500">Aucune dépense ce mois</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div id="tab-transactions" class="tab-content hidden">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-slate-900">Toutes les Transactions</h3>
                    <a href="{{ route('transactions.index') }}" class="btn-primary text-xs">Voir toutes</a>
                </div>
                @if($recentTransactions->count() > 0)
                <div class="overflow-x-auto rounded-lg border border-slate-100">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Description</th>
                                <th class="px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Compte</th>
                                <th class="px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-wider text-right">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($recentTransactions as $transaction)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-4 py-2.5 text-xs text-slate-900">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-2.5 text-xs text-slate-900">{{ $transaction->description ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5 text-xs text-slate-500">{{ $transaction->account->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded ring-1 ring-inset {{ $transaction->type === 'income' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-red-50 text-red-700 ring-red-600/20' }}">
                                        {{ $transaction->type === 'income' ? 'Entrée' : 'Sortie' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-xs text-right font-semibold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center py-8 text-xs text-slate-500">Aucune transaction</p>
                @endif
            </div>

            <div id="tab-expenses" class="tab-content hidden">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-slate-900">Dépenses</h3>
                    <a href="{{ route('expenses.index') }}" class="btn-primary text-xs">Gérer les dépenses</a>
                </div>
                @if($expensesByCategory->count() > 0)
                <div class="space-y-2">
                    @foreach($expensesByCategory as $expense)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                        <div>
                            <p class="text-xs font-medium text-slate-900">{{ ucfirst($expense->category ?? 'Autre') }}</p>
                            <p class="text-[11px] text-slate-500">Total des dépenses</p>
                        </div>
                        <p class="text-sm font-bold text-red-600">{{ number_format($expense->total, 0, ',', ' ') }} FCFA</p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center py-8 text-xs text-slate-500">Aucune dépense</p>
                @endif
            </div>

            <div id="tab-accounts" class="tab-content hidden">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-slate-900">Comptes</h3>
                    <a href="{{ route('accounts.index') }}" class="btn-primary text-xs">Gérer les comptes</a>
                </div>
                @if($accounts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($accounts as $account)
                    <div class="p-4 bg-slate-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-xs font-semibold text-slate-900">{{ $account->name }}</h4>
                            <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded ring-1 ring-inset {{ $account->type === 'bank' ? 'bg-primary-50 text-primary-700 ring-primary-600/20' : 'bg-purple-50 text-purple-700 ring-purple-600/20' }}">
                                {{ ucfirst($account->type ?? 'N/A') }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-1">{{ $account->bank_name ?? 'N/A' }}</p>
                        <p class="text-sm font-bold text-slate-900">{{ number_format($account->balance ?? 0, 0, ',', ' ') }} FCFA</p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center py-8 text-xs text-slate-500">Aucun compte</p>
                @endif
            </div>

            <div id="tab-invoices" class="tab-content hidden">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-slate-900">Factures Récentes</h3>
                    <a href="{{ route('invoices.index') }}" class="btn-primary text-xs">Voir toutes</a>
                </div>
                @if($recentInvoices->count() > 0)
                <div class="space-y-2">
                    @foreach($recentInvoices as $invoice)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                        <div>
                            <p class="text-xs font-medium text-slate-900">Facture #{{ $invoice->invoice_number ?? 'N/A' }}</p>
                            <p class="text-[11px] text-slate-500">{{ $invoice->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-900">{{ number_format($invoice->total_amount ?? 0, 0, ',', ' ') }} FCFA</p>
                            <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded ring-1 ring-inset {{ $invoice->status === 'paid' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }}">
                                {{ ucfirst($invoice->status ?? 'pending') }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center py-8 text-xs text-slate-500">Aucune facture</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'text-primary-700', 'border-primary-600');
                btn.classList.add('text-slate-500', 'border-transparent');
            });
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.classList.add('hidden');
            });
            this.classList.add('active', 'text-primary-700', 'border-primary-600');
            this.classList.remove('text-slate-500', 'border-transparent');
            document.getElementById(`tab-${targetTab}`).classList.add('active');
            document.getElementById(`tab-${targetTab}`).classList.remove('hidden');
        });
    });
});
</script>
@endsection
