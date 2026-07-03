@extends('layouts.app')

@section('title', 'Comptes')
@section('page-title', 'Comptes')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Comptes</h2>
            <p class="page-subtitle">Gerez vos comptes bancaires et caisse</p>
        </div>
        <button type="button" data-modal-open="modal-account-create" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nouveau compte
        </button>
    </header>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Solde total</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_balance'], 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total comptes</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $stats['total_accounts'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Compte(s)</p>
        </div>
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Comptes actifs</span>
                <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $stats['active_accounts'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Actif(s)</p>
        </div>
    </div>

    <!-- Accounts Grid -->
    @if($accounts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($accounts as $account)
            <div class="card-panel hover:border-slate-300 transition-colors">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">{{ $account->name }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                @if($account->type === 'cash') Especes
                                @elseif($account->type === 'bank') Banque
                                @else Mobile Money
                                @endif
                            </p>
                        </div>
                        @if($account->is_active)
                            <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">Actif</span>
                        @else
                            <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-slate-50 text-slate-600 ring-slate-500/20">Inactif</span>
                        @endif
                    </div>

                    <div class="mb-4">
                        <p class="text-xs text-slate-500">Solde</p>
                        <p class="text-xl font-bold text-slate-900">{{ number_format($account->balance, 0, ',', ' ') }} F</p>
                    </div>

                    @if($account->account_number)
                    <div class="mb-3">
                        <p class="text-xs text-slate-500">N de compte</p>
                        <p class="text-sm font-medium text-slate-700">{{ $account->account_number }}</p>
                    </div>
                    @endif

                    @if($account->bank_name)
                    <div class="mb-3">
                        <p class="text-xs text-slate-500">Banque</p>
                        <p class="text-sm font-medium text-slate-700">{{ $account->bank_name }}</p>
                    </div>
                    @endif

                    <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                        <a href="{{ route('accounts.show', $account) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                        <button type="button"
                            data-account-edit
                            data-update-url="{{ route('accounts.update', $account) }}"
                            data-name="{{ $account->name }}"
                            data-type="{{ $account->type }}"
                            data-bank-name="{{ $account->bank_name }}"
                            data-account-number="{{ $account->account_number }}"
                            data-is-active="{{ $account->is_active ? '1' : '0' }}"
                            class="px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded transition-colors">Modifier</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="card-panel">
            <div class="card-panel-body text-center py-12">
                <div class="empty-state-icon">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucun compte enregistre</h3>
                <p class="text-xs text-slate-500 mb-4">Commencez par creer votre premier compte</p>
                <button type="button" data-modal-open="modal-account-create" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nouveau compte
                </button>
            </div>
        </div>
    @endif
</div>

{{-- Modal : Modifier un compte --}}
<div id="modal-account-edit" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Modifier le compte</h3>
            <button type="button" data-modal-close="modal-account-edit" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-account-edit" method="POST" class="divide-y divide-slate-100">
            @csrf
            @method('PUT')
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom du compte <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="ace-name" required class="input-modern">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type de compte <span class="text-red-500">*</span></label>
                    <select name="type" id="ace-type" class="input-modern">
                        <option value="cash">Especes</option>
                        <option value="bank">Banque</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div id="ace-bank-fields" class="hidden">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom de la banque</label>
                    <input type="text" name="bank_name" id="ace-bank-name" class="input-modern">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Numero de compte</label>
                    <input type="text" name="account_number" id="ace-account-number" class="input-modern">
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="ace-is-active" value="1" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-medium text-slate-600">Compte actif</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <button type="button" data-modal-close="modal-account-edit" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal : Nouveau compte --}}
<div id="modal-account-create" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Nouveau compte</h3>
            <button type="button" data-modal-close="modal-account-create" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('accounts.store') }}" class="divide-y divide-slate-100">
            @csrf
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom du compte <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="input-modern" placeholder="Ex: Compte principal, Caisse...">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type de compte <span class="text-red-500">*</span></label>
                    <select name="type" id="ac-type" class="input-modern">
                        <option value="cash">Especes</option>
                        <option value="bank">Banque</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>
                <div id="ac-bank-fields" class="hidden">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom de la banque</label>
                    <input type="text" name="bank_name" class="input-modern" placeholder="Ex: UBA, SGBC...">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Numero de compte</label>
                    <input type="text" name="account_number" class="input-modern" placeholder="Optionnel">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Solde initial (FCFA)</label>
                    <input type="number" name="balance" value="0" step="0.01" min="0" class="input-modern">
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-medium text-slate-600">Compte actif</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <button type="button" data-modal-close="modal-account-create" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">Créer le compte</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal create — champ banque conditionnel
    var acType = document.getElementById('ac-type');
    var acBankFields = document.getElementById('ac-bank-fields');
    if (acType && acBankFields) {
        acType.addEventListener('change', function() {
            acBankFields.classList.toggle('hidden', this.value !== 'bank');
        });
    }

    // Modal edit — champ banque conditionnel
    var aceType = document.getElementById('ace-type');
    var aceBankFields = document.getElementById('ace-bank-fields');
    if (aceType && aceBankFields) {
        aceType.addEventListener('change', function() {
            aceBankFields.classList.toggle('hidden', this.value !== 'bank');
        });
    }

    // Ouvrir modal edit avec données pré-remplies
    document.querySelectorAll('[data-account-edit]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var form = document.getElementById('form-account-edit');
            form.action = btn.dataset.updateUrl;
            document.getElementById('ace-name').value           = btn.dataset.name || '';
            document.getElementById('ace-type').value           = btn.dataset.type || 'cash';
            document.getElementById('ace-bank-name').value      = btn.dataset.bankName || '';
            document.getElementById('ace-account-number').value = btn.dataset.accountNumber || '';
            document.getElementById('ace-is-active').checked    = btn.dataset.isActive === '1';
            aceBankFields.classList.toggle('hidden', btn.dataset.type !== 'bank');
            openModal('modal-account-edit');
        });
    });
});
</script>
@endsection
