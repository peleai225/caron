@extends('layouts.app')

@section('title', 'Depenses')
@section('page-title', 'Depenses')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Depenses</h2>
            <p class="page-subtitle">Suivez toutes vos depenses immobilieres</p>
        </div>
        <button type="button" data-modal-open="modal-expense-create" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nouvelle depense
        </button>
    </header>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Ce mois</span>
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_month'], 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Cette annee</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_year'], 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Nb. ce mois</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $stats['count_month'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Depense(s)</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('expenses.index') }}" class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="input-modern">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bien</label>
                    <select name="property_id" class="input-modern">
                        <option value="">Tous les biens</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>{{ $property->address }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type</label>
                    <select name="type" class="input-modern">
                        <option value="">Tous les types</option>
                        <option value="maintenance" {{ request('type') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="tax" {{ request('type') == 'tax' ? 'selected' : '' }}>Taxe</option>
                        <option value="insurance" {{ request('type') == 'insurance' ? 'selected' : '' }}>Assurance</option>
                        <option value="utilities" {{ request('type') == 'utilities' ? 'selected' : '' }}>Services publics</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Autre</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary flex-1">Filtrer</button>
                    <a href="{{ route('expenses.index') }}" class="btn-secondary">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="card-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full responsive-table">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Bien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Montant</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($expenses as $expense)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td data-label="Date" class="px-4 py-3 text-sm text-slate-900">{{ $expense->expense_date->format('d/m/Y') }}</td>
                        <td data-label="Type" class="px-4 py-3">
                            <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-slate-50 text-slate-700 ring-slate-500/20">{{ $expense->type }}</span>
                        </td>
                        <td data-label="Bien" class="px-4 py-3 text-sm text-slate-600">{{ $expense->property ? $expense->property->address : '—' }}</td>
                        <td data-label="Description" class="px-4 py-3 text-sm text-slate-900">{{ Str::limit($expense->description, 50) }}</td>
                        <td data-label="Montant" class="px-4 py-3 text-sm font-semibold text-slate-900 text-right">{{ number_format($expense->amount, 0, ',', ' ') }} F</td>
                        <td data-label="" class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button"
                                    data-expense-voir
                                    data-type="{{ $expense->type }}"
                                    data-date="{{ $expense->expense_date->format('d/m/Y') }}"
                                    data-amount="{{ number_format($expense->amount, 0, ',', ' ') }}"
                                    data-description="{{ $expense->description }}"
                                    data-property="{{ $expense->property?->address ?? '—' }}"
                                    data-receipt="{{ $expense->receipt_path ? asset('storage/'.$expense->receipt_path) : '' }}"
                                    class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</button>
                                <button type="button"
                                    data-expense-edit
                                    data-id="{{ $expense->id }}"
                                    data-update-url="{{ route('expenses.update', $expense) }}"
                                    data-property-id="{{ $expense->property_id ?? '' }}"
                                    data-type="{{ $expense->type }}"
                                    data-amount="{{ $expense->amount }}"
                                    data-date="{{ $expense->expense_date->format('Y-m-d') }}"
                                    data-description="{{ $expense->description }}"
                                    class="px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded transition-colors">Modifier</button>
                                <form id="form-delete-expense-{{ $expense->id }}" action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" data-confirm="Supprimer cette dépense ?" data-confirm-form="form-delete-expense-{{ $expense->id }}" class="px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 rounded transition-colors">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="empty-state-icon">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucune depense enregistree</h3>
                            <p class="text-xs text-slate-500 mb-4">Commencez par enregistrer votre premiere depense</p>
                            <button type="button" data-modal-open="modal-expense-create" class="btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Nouvelle depense
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal : Nouvelle dépense --}}
<div id="modal-expense-create" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Nouvelle dépense</h3>
            <button type="button" data-modal-close="modal-expense-create" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="divide-y divide-slate-100">
            @csrf
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bien immobilier</label>
                    <select name="property_id" class="input-modern">
                        <option value="">Aucun bien (dépense générale)</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->address }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type <span class="text-red-500">*</span></label>
                    <select name="type" required class="input-modern">
                        <option value="">Sélectionner un type</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="tax">Taxe</option>
                        <option value="insurance">Assurance</option>
                        <option value="utilities">Services publics</option>
                        <option value="other">Autre</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" required min="0" step="0.01" class="input-modern" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="expense_date" required class="input-modern" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" required class="input-modern" placeholder="Détail de la dépense..."></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Justificatif <span class="text-xs text-slate-400">(optionnel)</span></label>
                    <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png" class="input-modern text-xs">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <button type="button" data-modal-close="modal-expense-create" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- Side-panel : Voir dépense --}}
<div id="modal-expense-voir" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-0 right-0 h-full w-full max-w-sm bg-white shadow-xl transform translate-x-full transition-transform duration-300 flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-900">Détails de la dépense</h3>
            <button type="button" data-modal-close="modal-expense-voir" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs text-slate-500 mb-0.5">Type</p><p id="ev-type" class="text-sm font-semibold text-slate-900">—</p></div>
                <div><p class="text-xs text-slate-500 mb-0.5">Date</p><p id="ev-date" class="text-sm font-medium text-slate-900">—</p></div>
                <div><p class="text-xs text-slate-500 mb-0.5">Montant</p><p id="ev-amount" class="text-sm font-bold text-red-600">—</p></div>
                <div><p class="text-xs text-slate-500 mb-0.5">Bien</p><p id="ev-property" class="text-sm text-slate-700">—</p></div>
            </div>
            <div><p class="text-xs text-slate-500 mb-0.5">Description</p><p id="ev-description" class="text-sm text-slate-700 whitespace-pre-line">—</p></div>
            <div id="ev-receipt-wrap" class="hidden">
                <a id="ev-receipt-link" href="#" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-medium text-primary-600 hover:text-primary-700 px-3 py-1.5 bg-primary-50 rounded-md">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Voir le justificatif
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Modal : Modifier dépense --}}
<div id="modal-expense-edit" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Modifier la dépense</h3>
            <button type="button" data-modal-close="modal-expense-edit" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-expense-edit" method="POST" enctype="multipart/form-data" class="divide-y divide-slate-100">
            @csrf
            @method('PUT')
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bien immobilier</label>
                    <select name="property_id" id="ee-property-id" class="input-modern">
                        <option value="">Aucun bien (dépense générale)</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->address }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type <span class="text-red-500">*</span></label>
                    <select name="type" id="ee-type" required class="input-modern">
                        <option value="">Sélectionner un type</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="tax">Taxe</option>
                        <option value="insurance">Assurance</option>
                        <option value="utilities">Services publics</option>
                        <option value="other">Autre</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" id="ee-amount" required min="0" step="0.01" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="expense_date" id="ee-date" required class="input-modern">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" id="ee-description" rows="3" required class="input-modern"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Justificatif <span class="text-xs text-slate-400">(optionnel)</span></label>
                    <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png" class="input-modern text-xs">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <button type="button" data-modal-close="modal-expense-edit" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Voir dépense — side-panel
    document.querySelectorAll('[data-expense-voir]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('ev-type').textContent        = btn.dataset.type || '—';
            document.getElementById('ev-date').textContent        = btn.dataset.date || '—';
            document.getElementById('ev-amount').textContent      = (btn.dataset.amount || '—') + ' FCFA';
            document.getElementById('ev-property').textContent    = btn.dataset.property || '—';
            document.getElementById('ev-description').textContent = btn.dataset.description || '—';
            var wrap = document.getElementById('ev-receipt-wrap');
            var link = document.getElementById('ev-receipt-link');
            if (btn.dataset.receipt) {
                link.href = btn.dataset.receipt;
                wrap.classList.remove('hidden');
            } else {
                wrap.classList.add('hidden');
            }
            openModal('modal-expense-voir');
        });
    });

    // Modifier dépense — modal
    document.querySelectorAll('[data-expense-edit]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var form = document.getElementById('form-expense-edit');
            form.action = btn.dataset.updateUrl;
            document.getElementById('ee-type').value        = btn.dataset.type || '';
            document.getElementById('ee-amount').value      = btn.dataset.amount || '';
            document.getElementById('ee-date').value        = btn.dataset.date || '';
            document.getElementById('ee-description').value = btn.dataset.description || '';
            var sel = document.getElementById('ee-property-id');
            sel.value = btn.dataset.propertyId || '';
            openModal('modal-expense-edit');
        });
    });
});
</script>
@endsection
