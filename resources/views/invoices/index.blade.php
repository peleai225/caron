@extends('layouts.app')

@section('title', 'Factures')
@section('page-title', 'Factures')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Factures</h2>
            <p class="page-subtitle">Gestion des factures</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle facture
        </a>
    </header>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">En attente</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_pending'], 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Payees</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_paid'], 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">En retard</span>
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ number_format($stats['total_overdue'], 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
    </div>

    <!-- Table -->
    <div class="card-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">N Facture</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Contrat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Emission</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Echeance</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Montant</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('contracts.show', $invoice->contract) }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">{{ $invoice->contract->contract_number }}</a>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $invoice->issue_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $invoice->due_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-slate-900 text-right">{{ number_format($invoice->total_amount, 0, ',', ' ') }} F</td>
                        <td class="px-4 py-3"><x-status-badge :status="$invoice->status" size="sm" /></td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('invoices.show', $invoice) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                                <a href="{{ route('invoices.download', $invoice) }}" class="px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded transition-colors">PDF</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="empty-state-icon">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucune facture</h3>
                            <p class="text-xs text-slate-500">Les factures apparaitront ici</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
