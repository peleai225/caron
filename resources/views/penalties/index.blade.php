@extends('layouts.app')

@section('title', 'Penalites')
@section('page-title', 'Penalites')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Penalites</h2>
            <p class="page-subtitle">Gestion des penalites de retard</p>
        </div>
        <button type="button" data-modal-open="modal-penalty-create" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nouvelle pénalité
        </button>
    </header>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $stats['total'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Penalite(s)</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Payees</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['paid'], 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Impayees</span>
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ number_format($stats['unpaid'], 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('penalties.index') }}" class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Contrat</label>
                    <select name="contract_id" class="input-modern">
                        <option value="">Tous</option>
                        @foreach($contracts as $contract)
                            <option value="{{ $contract->id }}" {{ request('contract_id') == $contract->id ? 'selected' : '' }}>{{ $contract->contract_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut</label>
                    <select name="status" class="input-modern">
                        <option value="">Tous</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Payees</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Impayees</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Date debut</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="input-modern">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Date fin</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="input-modern">
                </div>
            </div>
            <div class="flex gap-2 mt-3">
                <button type="submit" class="btn-primary">Filtrer</button>
                <a href="{{ route('penalties.index') }}" class="btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="card-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Contrat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Locataire</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Montant</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($penalties as $penalty)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td data-label="Contrat" class="px-4 py-3 text-sm text-slate-900">{{ $penalty->paymentSchedule->contract->contract_number ?? 'N/A' }}</td>
                        <td data-label="Locataire" class="px-4 py-3 text-sm text-slate-900">{{ $penalty->paymentSchedule->contract->tenant->first_name ?? '' }} {{ $penalty->paymentSchedule->contract->tenant->last_name ?? '' }}</td>
                        <td data-label="Montant" class="px-4 py-3 text-sm font-semibold text-slate-900">{{ number_format($penalty->amount, 0, ',', ' ') }} F</td>
                        <td data-label="Date" class="px-4 py-3 text-sm text-slate-600">{{ $penalty->created_at->format('d/m/Y') }}</td>
                        <td data-label="Statut" class="px-4 py-3">
                            @if($penalty->status === 'paid')
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">Payee</span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-red-50 text-red-700 ring-red-600/20">Impayee</span>
                            @endif
                        </td>
                        <td data-label="" class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <button type="button"
                                    data-penalty-voir
                                    data-contract="{{ $penalty->paymentSchedule->contract->contract_number ?? 'N/A' }}"
                                    data-tenant="{{ trim(($penalty->paymentSchedule->contract->tenant->first_name ?? '').' '.($penalty->paymentSchedule->contract->tenant->last_name ?? '')) }}"
                                    data-amount="{{ number_format($penalty->amount, 0, ',', ' ') }}"
                                    data-date="{{ $penalty->created_at->format('d/m/Y') }}"
                                    data-paid-at="{{ $penalty->paid_at ? $penalty->paid_at->format('d/m/Y à H:i') : '' }}"
                                    data-status="{{ $penalty->status }}"
                                    data-description="{{ $penalty->description ?? '' }}"
                                    data-mark-paid-url="{{ $penalty->status === 'unpaid' ? route('penalties.mark-as-paid', $penalty) : '' }}"
                                    class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</button>
                                @if($penalty->status === 'unpaid')
                                <form method="POST" action="{{ route('penalties.destroy', $penalty) }}" onsubmit="return confirm('Supprimer cette pénalité ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 rounded transition-colors">Supprimer</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="empty-state-icon">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucune penalite trouvee</h3>
                            <p class="text-xs text-slate-500">Les penalites apparaitront ici</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penalties->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $penalties->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal : Nouvelle pénalité --}}
<div id="modal-penalty-create" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Nouvelle pénalité</h3>
            <button type="button" data-modal-close="modal-penalty-create" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('penalties.store') }}" class="divide-y divide-slate-100">
            @csrf
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Échéance concernée <span class="text-red-500">*</span></label>
                    <select name="payment_schedule_id" required class="input-modern">
                        <option value="">Sélectionner une échéance</option>
                        @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}">
                                {{ $schedule->contract->contract_number ?? '—' }}
                                — {{ $schedule->contract->tenant?->full_name ?? '' }}
                                — Éch. {{ $schedule->due_date->format('d/m/Y') }}
                                ({{ number_format($schedule->amount, 0, ',', ' ') }} F)
                            </option>
                        @endforeach
                    </select>
                    @if($schedules->isEmpty())
                        <p class="text-[11px] text-slate-400 mt-1">Aucune échéance éligible sans pénalité impayée existante.</p>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" min="0" step="1" required class="input-modern" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Taux appliqué (%)</label>
                        <input type="number" name="rate" min="0" max="100" step="0.01" value="0" class="input-modern">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Jours de retard <span class="text-red-500">*</span></label>
                    <input type="number" name="days_late" min="1" required class="input-modern" placeholder="ex: 15">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <button type="button" data-modal-close="modal-penalty-create" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">Créer la pénalité</button>
            </div>
        </form>
    </div>
</div>

{{-- Side-panel : Voir pénalité --}}
<div id="modal-penalty-voir" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-0 right-0 h-full w-full max-w-sm bg-white shadow-xl transform translate-x-full transition-transform duration-300 flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-900">Détail de la pénalité</h3>
            <button type="button" data-modal-close="modal-penalty-voir" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs text-slate-500 mb-0.5">Contrat</p><p id="pv-contract" class="text-sm font-semibold text-slate-900">—</p></div>
                <div><p class="text-xs text-slate-500 mb-0.5">Locataire</p><p id="pv-tenant" class="text-sm text-slate-900">—</p></div>
                <div><p class="text-xs text-slate-500 mb-0.5">Montant</p><p id="pv-amount" class="text-lg font-bold text-red-600">—</p></div>
                <div><p class="text-xs text-slate-500 mb-0.5">Date</p><p id="pv-date" class="text-sm text-slate-900">—</p></div>
                <div><p class="text-xs text-slate-500 mb-0.5">Statut</p><div id="pv-status">—</div></div>
                <div id="pv-paid-at-wrap" class="hidden"><p class="text-xs text-slate-500 mb-0.5">Payée le</p><p id="pv-paid-at" class="text-sm text-slate-900">—</p></div>
            </div>
            <div id="pv-description-wrap" class="hidden">
                <p class="text-xs text-slate-500 mb-0.5">Description</p>
                <p id="pv-description" class="text-sm text-slate-700">—</p>
            </div>
        </div>
        <div id="pv-action-wrap" class="px-5 py-4 border-t border-slate-100">
            <form id="form-penalty-paid" method="POST" action="#">
                @csrf
                @method('PUT')
                <button type="submit" class="btn-primary w-full justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Marquer comme payée
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-penalty-voir]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('pv-contract').textContent    = btn.dataset.contract || '—';
            document.getElementById('pv-tenant').textContent      = btn.dataset.tenant || '—';
            document.getElementById('pv-amount').textContent      = (btn.dataset.amount || '—') + ' FCFA';
            document.getElementById('pv-date').textContent        = btn.dataset.date || '—';

            var statusEl = document.getElementById('pv-status');
            if (btn.dataset.status === 'paid') {
                statusEl.innerHTML = '<span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">Payée</span>';
            } else {
                statusEl.innerHTML = '<span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-red-50 text-red-700 ring-red-600/20">Impayée</span>';
            }

            var paidWrap = document.getElementById('pv-paid-at-wrap');
            if (btn.dataset.paidAt) {
                document.getElementById('pv-paid-at').textContent = btn.dataset.paidAt;
                paidWrap.classList.remove('hidden');
            } else {
                paidWrap.classList.add('hidden');
            }

            var descWrap = document.getElementById('pv-description-wrap');
            if (btn.dataset.description) {
                document.getElementById('pv-description').textContent = btn.dataset.description;
                descWrap.classList.remove('hidden');
            } else {
                descWrap.classList.add('hidden');
            }

            var actionWrap = document.getElementById('pv-action-wrap');
            if (btn.dataset.status === 'unpaid' && btn.dataset.markPaidUrl) {
                document.getElementById('form-penalty-paid').action = btn.dataset.markPaidUrl;
                actionWrap.classList.remove('hidden');
            } else {
                actionWrap.classList.add('hidden');
            }

            openModal('modal-penalty-voir');
        });
    });
});
</script>
@endsection
