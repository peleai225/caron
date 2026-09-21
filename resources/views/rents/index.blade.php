@extends('layouts.app')

@section('title', 'Loyers & Paiements')
@section('page-title', 'Loyers & Paiements')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Loyers et paiements</h2>
            <p class="page-subtitle">Suivez les paiements et les impayes</p>
        </div>
        <button type="button" data-modal-open="modal-rent-payment" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Encaisser un paiement
        </button>
    </header>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total ce mois</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['monthly_total'] ?? 0, 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Impayes</span>
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ number_format($stats['overdue_amount'] ?? 0, 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">En attente</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $stats['pending_count'] ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Paiement(s)</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-panel">
        <div class="card-panel-body">
            <form method="GET" action="{{ route('rents.index') }}" id="rents-filter-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                {{-- Ligne 1 --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bailleur</label>
                    <select name="owner_id" class="rent-filter input-modern searchable-select">
                        <option value="">Tous</option>
                        @foreach($owners as $o)
                            <option value="{{ $o->id }}" {{ request('owner_id') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Résidence</label>
                    <select name="property_id" class="rent-filter input-modern searchable-select">
                        <option value="">Toutes</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" {{ request('property_id') == $p->id ? 'selected' : '' }}>{{ $p->full_address }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Locataire</label>
                    <select name="tenant_id" class="rent-filter input-modern searchable-select">
                        <option value="">Tous</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Période</label>
                    <input type="month" name="period" value="{{ request('period') }}" class="rent-filter input-modern">
                </div>
                {{-- Ligne 2 --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut</label>
                    <select name="status" class="rent-filter input-modern">
                        <option value="">Tous</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Encaissé</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Échoué</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Méthode</label>
                    <select name="payment_method" class="rent-filter input-modern">
                        <option value="">Toutes</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                        <option value="check" {{ request('payment_method') == 'check' ? 'selected' : '' }}>Chèque</option>
                        <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Virement</option>
                        <option value="mobile_money" {{ request('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        <option value="moneyfusion" {{ request('payment_method') == 'moneyfusion' ? 'selected' : '' }}>MoneyFusion</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Du</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="rent-filter input-modern">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Au</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="rent-filter input-modern">
                </div>
                <div class="lg:col-span-4 flex justify-end gap-2">
                    <button type="submit" class="btn-primary">Filtrer</button>
                    <a href="{{ route('rents.index') }}" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent payments table -->
    <div class="card-panel overflow-hidden">
        <div class="card-panel-header">Paiements recents</div>
        <div class="overflow-x-auto">
            <table class="min-w-full responsive-table">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Locataire</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Bien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Loyer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Encaisse</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Methode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentPayments ?? [] as $payment)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td data-label="Locataire" class="px-4 py-3 text-sm font-medium text-slate-900">{{ $payment->contract?->tenant?->full_name ?? '—' }}</td>
                            <td data-label="Bien" class="px-4 py-3 text-sm text-slate-600">{{ $payment->contract?->property?->full_address ?? '—' }}</td>
                            <td data-label="Loyer" class="px-4 py-3 text-sm text-slate-900">{{ number_format($payment->amount, 0, ',', ' ') }} F</td>
                            <td data-label="Encaissé" class="px-4 py-3 text-sm font-medium text-emerald-600">{{ number_format($payment->total_amount ?? $payment->amount, 0, ',', ' ') }} F</td>
                            <td data-label="Méthode" class="px-4 py-3 text-xs text-slate-500 capitalize">{{ str_replace('_', ' ', $payment->payment_method ?? '—') }}</td>
                            <td data-label="Statut" class="px-4 py-3"><x-status-badge :status="$payment->status" size="sm" /></td>
                            <td data-label="" class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('rents.show', $payment) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir</a>
                                    @if($payment->receipt)
                                    <a href="{{ route('rents.receipt', $payment) }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Quittance</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">Aucun paiement recent.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Overdue -->
    <div class="card-panel overflow-hidden">
        <div class="card-panel-header flex items-center gap-2">
            <span>Arrieres</span>
            @if(count($overduePayments ?? []) > 0)
                <span class="px-1.5 py-0.5 text-[10px] font-medium bg-red-50 text-red-600 rounded-md ring-1 ring-inset ring-red-600/20">{{ count($overduePayments ?? []) }}</span>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full responsive-table">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Locataire</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Bien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Montant</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Echeance</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($overduePayments ?? [] as $schedule)
                        <tr class="hover:bg-slate-50/50">
                            <td data-label="Locataire" class="px-4 py-3 text-sm font-medium text-slate-900">{{ $schedule->contract?->tenant?->full_name ?? '—' }}</td>
                            <td data-label="Bien" class="px-4 py-3 text-sm text-slate-600">{{ $schedule->contract?->property?->full_address ?? '—' }}</td>
                            <td data-label="Montant" class="px-4 py-3 text-sm font-medium text-slate-900">{{ number_format($schedule->amount, 0, ',', ' ') }} F</td>
                            <td data-label="Echéance" class="px-4 py-3 text-xs text-slate-500">{{ $schedule->due_date->format('d/m/Y') }}</td>
                            <td data-label="Statut" class="px-4 py-3"><x-status-badge status="impaye" size="sm" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">Aucun arriere.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- All payments -->
    <div class="card-panel overflow-hidden">
        <div class="card-panel-header">Tous les paiements</div>
        <div class="overflow-x-auto">
            <table class="min-w-full responsive-table">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Locataire</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Bien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Montant</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Methode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td data-label="Date" class="px-4 py-3 text-sm text-slate-900">{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td data-label="Locataire" class="px-4 py-3 text-sm font-medium text-slate-900">
                                {{ $payment->contract?->tenant?->full_name ?? '—' }}
                            </td>
                            <td data-label="Bien" class="px-4 py-3 text-sm text-slate-600">{{ $payment->contract?->property?->address ?? '—' }}</td>
                            <td data-label="Montant" class="px-4 py-3 text-sm font-semibold text-slate-900">{{ number_format($payment->total_amount, 0, ',', ' ') }} F</td>
                            <td data-label="Méthode" class="px-4 py-3 text-xs text-slate-500 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td data-label="Statut" class="px-4 py-3"><x-status-badge :status="$payment->status" size="sm" /></td>
                            <td data-label="" class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('rents.show', $payment) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir</a>
                                    @if($payment->receipt)
                                    <a href="{{ route('rents.receipt', $payment) }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">Quittance</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">
                                Aucun paiement. <a href="{{ route('rents.create') }}" class="text-primary-600 hover:underline font-medium">Encaisser un paiement</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $payments->withQueryString()->links() }}</div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('rents-filter-form');
    if (form) {
        form.querySelectorAll('select.rent-filter').forEach(function(el) {
            el.addEventListener('change', function() { form.submit(); });
        });
    }
    // Auto-remplir montant depuis le contrat sélectionné
    var contractSel = document.getElementById('rp-contract-id');
    var amountInput = document.getElementById('rp-amount');
    if (contractSel && amountInput) {
        contractSel.addEventListener('change', function() {
            var opt = contractSel.options[contractSel.selectedIndex];
            if (opt && opt.dataset.rent) amountInput.value = parseInt(opt.dataset.rent, 10);
        });
    }
});
</script>

{{-- Modal : Encaisser un paiement --}}
<div id="modal-rent-payment" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-xl bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Encaisser un paiement</h3>
            <button type="button" data-modal-close="modal-rent-payment" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('rents.store') }}" class="divide-y divide-slate-100">
            @csrf
            <div class="px-5 py-4 space-y-4">
                {{-- Recherche locataire --}}
                <div class="relative" id="rp-search-wrap">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Locataire / N° porte / Téléphone <span class="text-red-500">*</span></label>
                    <input type="text" id="rp-search" autocomplete="off" class="input-modern" placeholder="Tapez un nom, n° de porte ou téléphone...">
                    <input type="hidden" id="rp-contract-id" name="contract_id">
                    <div id="rp-results" class="absolute z-30 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-48 overflow-y-auto hidden"></div>
                </div>

                {{-- Sélection contrat (si plusieurs) --}}
                <div id="rp-contract-wrap" class="hidden">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Contrat</label>
                    <select id="rp-contract-select" class="input-modern"></select>
                </div>

                {{-- Résumé contrat --}}
                <div id="rp-info" class="hidden bg-slate-50 rounded-lg p-2.5 border border-slate-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-xs font-medium text-slate-700" id="rp-info-text"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" id="rp-amount" name="amount" required min="0" step="1" class="input-modern" placeholder="150000">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Période <span class="text-red-500">*</span></label>
                        <input type="month" id="rp-period" name="period" required value="{{ date('Y-m') }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Nb de paiements</label>
                        <input type="number" name="payment_count" value="1" min="1" max="24" class="input-modern" id="rp-count">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Méthode <span class="text-red-500">*</span></label>
                        <select name="payment_method" id="rp-method" required class="input-modern">
                            <option value="">Choisir...</option>
                            <option value="cash">Espèces</option>
                            <option value="moneyfusion">MoneyFusion (Mobile Money)</option>
                            <option value="check">Chèque</option>
                            <option value="transfer">Virement</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Commission (%)</label>
                        <input type="number" name="commission_percent" min="0" max="100" step="0.01" class="input-modern" placeholder="Ex: 10" id="rp-comm">
                    </div>
                </div>

                <div id="rp-phone-wrap" class="hidden">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" id="rp-phone" class="input-modern" placeholder="+225 07 XX XX XX XX">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                    <textarea name="notes" rows="2" class="input-modern" placeholder="Notes additionnelles..."></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="generate_receipt" value="1" checked class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    <label class="text-xs text-slate-600">Générer le reçu automatiquement</label>
                </div>
            </div>
            <div class="flex items-center justify-between px-5 py-4">
                <a href="{{ route('rents.create') }}" class="text-xs text-slate-500 hover:text-primary-600 transition-colors">Formulaire complet →</a>
                <div class="flex gap-2">
                    <button type="button" data-modal-close="modal-rent-payment" class="btn-secondary">Annuler</button>
                    <button type="submit" id="rp-submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Modal search ---
    var rpSearch   = document.getElementById('rp-search');
    var rpResults  = document.getElementById('rp-results');
    var rpContractId = document.getElementById('rp-contract-id');
    var rpContractWrap = document.getElementById('rp-contract-wrap');
    var rpContractSelect = document.getElementById('rp-contract-select');
    var rpInfo     = document.getElementById('rp-info');
    var rpAmount   = document.getElementById('rp-amount');
    var rpPhone    = document.getElementById('rp-phone');
    var rpTimer    = null;
    var rpAllContracts = [];

    rpSearch.addEventListener('input', function() {
        clearTimeout(rpTimer);
        var q = this.value.trim();
        if (q.length < 2) { rpResults.classList.add('hidden'); return; }
        rpTimer = setTimeout(function() {
            fetch('{{ route("rents.search-tenants") }}?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                rpAllContracts = data;
                rpResults.textContent = '';
                if (!data.length) {
                    var empty = document.createElement('div');
                    empty.className = 'px-4 py-3 text-xs text-slate-400';
                    empty.textContent = 'Aucun résultat';
                    rpResults.appendChild(empty);
                    rpResults.classList.remove('hidden');
                    return;
                }
                var grouped = {};
                data.forEach(function(c) {
                    if (!grouped[c.tenant_id]) grouped[c.tenant_id] = { tenant: c.tenant_name, phone: c.phone, contracts: [] };
                    grouped[c.tenant_id].contracts.push(c);
                });
                Object.keys(grouped).forEach(function(tid) {
                    var g = grouped[tid];
                    var row = document.createElement('div');
                    row.className = 'rp-row px-4 py-2 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0';
                    var nameSpan = document.createElement('span');
                    nameSpan.className = 'text-sm font-medium text-slate-800';
                    nameSpan.textContent = g.tenant + ' ';
                    var phoneSpan = document.createElement('span');
                    phoneSpan.className = 'text-[11px] text-slate-400';
                    phoneSpan.textContent = g.phone || '';
                    row.appendChild(nameSpan);
                    row.appendChild(phoneSpan);
                    g.contracts.forEach(function(c) {
                        var detail = document.createElement('div');
                        detail.className = 'text-[11px] text-slate-500';
                        detail.textContent = c.property + ' — ' + Number(c.rent_amount).toLocaleString('fr-FR') + ' F';
                        row.appendChild(detail);
                    });
                    row.addEventListener('click', function() {
                        var tc = rpAllContracts.filter(function(c) { return String(c.tenant_id) === tid; });
                        rpResults.classList.add('hidden');
                        if (tc.length === 1) {
                            rpSelectContract(tc[0]);
                            rpContractWrap.classList.add('hidden');
                        } else {
                            rpContractSelect.textContent = '';
                            var defOpt = document.createElement('option');
                            defOpt.value = '';
                            defOpt.textContent = 'Choisir...';
                            rpContractSelect.appendChild(defOpt);
                            tc.forEach(function(c) {
                                var o = document.createElement('option');
                                o.value = c.contract_id;
                                o.textContent = c.contract_number + ' — ' + c.property + ' (' + Number(c.rent_amount).toLocaleString('fr-FR') + ' F)';
                                rpContractSelect.appendChild(o);
                            });
                            rpContractWrap.classList.remove('hidden');
                            rpSearch.value = tc[0].tenant_name;
                        }
                    });
                    rpResults.appendChild(row);
                });
                rpResults.classList.remove('hidden');
            });
        }, 250);
    });

    rpContractSelect.addEventListener('change', function() {
        if (!this.value) return;
        var c = rpAllContracts.find(function(x) { return String(x.contract_id) === rpContractSelect.value; });
        if (c) rpSelectContract(c);
    });

    function rpSelectContract(c) {
        rpContractId.value = c.contract_id;
        rpSearch.value = c.tenant_name;
        rpAmount.value = Math.round(c.rent_amount);
        document.getElementById('rp-info-text').textContent = c.tenant_name + ' — ' + c.property + ' — ' + Number(c.rent_amount).toLocaleString('fr-FR') + ' F';
        rpInfo.classList.remove('hidden');
        if (c.phone && rpPhone) rpPhone.value = c.phone;
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#rp-search-wrap')) rpResults.classList.add('hidden');
    });

    // --- MoneyFusion toggle ---
    var rpMethod    = document.getElementById('rp-method');
    var rpPhoneWrap = document.getElementById('rp-phone-wrap');
    var rpSubmit    = document.getElementById('rp-submit');
    rpMethod.addEventListener('change', function() {
        var isMF = this.value === 'moneyfusion';
        rpPhoneWrap.classList.toggle('hidden', !isMF);
        if (rpPhone) rpPhone.required = isMF;
        if (rpSubmit) {
            rpSubmit.innerHTML = isMF
                ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Payer via Fusion Pay'
                : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Enregistrer';
        }
    });
});
</script>
@endsection
