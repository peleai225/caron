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
            <form method="GET" action="{{ route('rents.index') }}" id="rents-filter-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
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
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Residence</label>
                    <select name="property_id" class="rent-filter input-modern searchable-select">
                        <option value="">Toutes</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" {{ request('property_id') == $p->id ? 'selected' : '' }}>{{ $p->address ?? $p->city }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Locataire</label>
                    <select name="tenant_id" class="rent-filter input-modern searchable-select">
                        <option value="">Tous</option>
                        @foreach($tenants as $t)
                            <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->first_name }} {{ $t->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Periode</label>
                    <input type="month" name="period" value="{{ request('period') }}" class="rent-filter input-modern">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary flex-1">Filtrer</button>
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
                            <td data-label="Locataire" class="px-4 py-3 text-sm font-medium text-slate-900">{{ $payment->contract->tenant->first_name ?? '' }} {{ $payment->contract->tenant->last_name ?? '' }}</td>
                            <td data-label="Bien" class="px-4 py-3 text-sm text-slate-600">{{ $payment->contract->property->address ?? '—' }}</td>
                            <td data-label="Loyer" class="px-4 py-3 text-sm text-slate-900">{{ number_format($payment->amount, 0, ',', ' ') }} F</td>
                            <td data-label="Encaissé" class="px-4 py-3 text-sm font-medium text-emerald-600">{{ number_format($payment->total_amount ?? $payment->amount, 0, ',', ' ') }} F</td>
                            <td data-label="Méthode" class="px-4 py-3 text-xs text-slate-500 capitalize">{{ str_replace('_', ' ', $payment->payment_method ?? '—') }}</td>
                            <td data-label="Statut" class="px-4 py-3"><x-status-badge :status="$payment->status" size="sm" /></td>
                            <td data-label="" class="px-4 py-3"><a href="{{ route('rents.show', $payment) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir</a></td>
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
                            <td data-label="Locataire" class="px-4 py-3 text-sm font-medium text-slate-900">{{ $schedule->contract->tenant->first_name ?? '' }} {{ $schedule->contract->tenant->last_name ?? '' }}</td>
                            <td data-label="Bien" class="px-4 py-3 text-sm text-slate-600">{{ $schedule->contract->property->address ?? '—' }}</td>
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
                                {{ $payment->contract?->tenant ? $payment->contract->tenant->first_name . ' ' . $payment->contract->tenant->last_name : '—' }}
                            </td>
                            <td data-label="Bien" class="px-4 py-3 text-sm text-slate-600">{{ $payment->contract?->property?->address ?? '—' }}</td>
                            <td data-label="Montant" class="px-4 py-3 text-sm font-semibold text-slate-900">{{ number_format($payment->total_amount, 0, ',', ' ') }} F</td>
                            <td data-label="Méthode" class="px-4 py-3 text-xs text-slate-500 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td data-label="Statut" class="px-4 py-3"><x-status-badge :status="$payment->status" size="sm" /></td>
                            <td data-label="" class="px-4 py-3">
                                <a href="{{ route('rents.show', $payment) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir</a>
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
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Contrat <span class="text-red-500">*</span></label>
                    <select id="rp-contract-id" name="contract_id" required class="input-modern searchable-select">
                        <option value="">Sélectionner un contrat actif...</option>
                        @foreach($tenants as $t)
                            @foreach($t->contracts ?? [] as $c)
                                @if($c->status === 'active')
                                <option value="{{ $c->id }}" data-rent="{{ $c->rent_amount }}">
                                    {{ $c->property?->address ?? 'Bien #'.$c->property_id }} — {{ $t->full_name }} ({{ number_format($c->rent_amount, 0, ',', ' ') }} F)
                                </option>
                                @endif
                            @endforeach
                        @endforeach
                    </select>
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
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Méthode <span class="text-red-500">*</span></label>
                        <select name="payment_method" id="rp-method" required class="input-modern">
                            <option value="">Choisir...</option>
                            <option value="cash">Espèces</option>
                            <option value="moneyfusion">MoneyFusion (Wave / OM / MTN)</option>
                            <option value="check">Chèque</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Période <span class="text-red-500">*</span></label>
                        <input type="month" name="period" required value="{{ date('Y-m') }}" class="input-modern">
                    </div>
                </div>

                {{-- Téléphone — affiché uniquement pour MoneyFusion --}}
                <div id="rp-phone-wrap" class="hidden">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone du client <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" id="rp-phone" class="input-modern" placeholder="+225 07 XX XX XX XX">
                    <p class="mt-1 text-[11px] text-slate-400">Numéro qui recevra la demande de paiement (Wave, Orange Money ou MTN)</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Référence</label>
                    <input type="text" name="reference" class="input-modern" placeholder="N° de transaction (optionnel)">
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
    var rpMethod    = document.getElementById('rp-method');
    var rpPhoneWrap = document.getElementById('rp-phone-wrap');
    var rpPhone     = document.getElementById('rp-phone');
    var rpSubmit    = document.getElementById('rp-submit');

    if (rpMethod && rpPhoneWrap) {
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
    }
});
</script>
@endsection
