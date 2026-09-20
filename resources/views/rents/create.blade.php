@extends('layouts.app')

@section('title', 'Enregistrer un paiement')
@section('page-title', 'Enregistrer un paiement')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouveau paiement</h2>
            <p class="page-subtitle">Enregistrez un paiement de loyer</p>
        </div>
    </header>

    <div class="card-panel">
        <form method="POST" action="{{ route('rents.store') }}">
            @csrf

            <div class="card-panel-body space-y-5">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Informations de paiement</h3>

                {{-- Recherche locataire --}}
                <div class="relative" id="tenant-search-wrap">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Locataire / N° porte / Téléphone <span class="text-red-500">*</span></label>
                    <input type="text" id="tenant-search" autocomplete="off" class="input-modern" placeholder="Tapez un nom, n° de porte ou téléphone...">
                    <input type="hidden" id="contract_id" name="contract_id" value="{{ old('contract_id') }}">
                    <div id="search-results" class="absolute z-30 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-64 overflow-y-auto hidden"></div>
                    @error('contract_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Sélection contrat (si plusieurs) --}}
                <div id="contract-select-wrap" class="hidden">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Contrat <span class="text-red-500">*</span></label>
                    <select id="contract-select" class="input-modern">
                        <option value="">Choisir un contrat...</option>
                    </select>
                </div>

                {{-- Résumé du contrat sélectionné --}}
                <div id="contract-info" class="hidden bg-slate-50 rounded-lg p-3 border border-slate-200">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-xs font-semibold text-slate-700" id="info-tenant"></span>
                    </div>
                    <p class="text-xs text-slate-500" id="info-property"></p>
                    <p class="text-xs text-slate-500">Loyer : <span class="font-semibold text-slate-700" id="info-rent"></span> FCFA</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="amount" class="block text-xs font-medium text-slate-600 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" id="amount" name="amount" required value="{{ old('amount') }}" min="0" step="1" class="input-modern" placeholder="150000">
                        @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payment_date" class="block text-xs font-medium text-slate-600 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" id="payment_date" name="payment_date" required value="{{ old('payment_date', date('Y-m-d')) }}" class="input-modern">
                        @error('payment_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="period" class="block text-xs font-medium text-slate-600 mb-1.5">Période <span class="text-red-500">*</span></label>
                        <input type="month" id="period" name="period" required value="{{ old('period', date('Y-m')) }}" class="input-modern">
                        @error('period') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payment_count" class="block text-xs font-medium text-slate-600 mb-1.5">Nombre de paiements</label>
                        <input type="number" id="payment_count" name="payment_count" value="{{ old('payment_count', 1) }}" min="1" max="24" class="input-modern" placeholder="1">
                        <p class="mt-1 text-[11px] text-slate-400" id="payment-count-hint"></p>
                        @error('payment_count') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs font-medium text-slate-600 mb-1.5">Méthode <span class="text-red-500">*</span></label>
                        <select id="payment_method" name="payment_method" required class="input-modern">
                            <option value="">Choisir...</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                            <option value="moneyfusion" {{ old('payment_method') == 'moneyfusion' ? 'selected' : '' }}>MoneyFusion (Mobile Money)</option>
                            <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Chèque</option>
                            <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Virement</option>
                        </select>
                        @error('payment_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div id="phone_field" class="hidden">
                        <label for="phone" class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone MoneyFusion <span class="text-red-500">*</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="input-modern" placeholder="+225 07 12 34 56 78">
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="commission_percent" class="block text-xs font-medium text-slate-600 mb-1.5">Commission agence (%)</label>
                        <input type="number" id="commission_percent" name="commission_percent" value="{{ old('commission_percent') }}" min="0" max="100" step="0.01" placeholder="Ex: 10" class="input-modern">
                        <p class="mt-1 text-[11px] text-slate-400" id="commission-hint"></p>
                    </div>

                    <div>
                        <label for="reference" class="block text-xs font-medium text-slate-600 mb-1.5">Référence</label>
                        <input type="text" id="reference" name="reference" value="{{ old('reference') }}" class="input-modern" placeholder="N° de transaction">
                        @error('reference') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                    <textarea id="notes" name="notes" rows="2" class="input-modern" placeholder="Notes additionnelles...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="generate_receipt" name="generate_receipt" value="1" checked class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    <label for="generate_receipt" class="text-xs text-slate-600">Générer le reçu (quittance PDF) automatiquement</label>
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('rents.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer le paiement</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput   = document.getElementById('tenant-search');
    var searchResults = document.getElementById('search-results');
    var contractInput = document.getElementById('contract_id');
    var contractSelectWrap = document.getElementById('contract-select-wrap');
    var contractSelect = document.getElementById('contract-select');
    var contractInfo  = document.getElementById('contract-info');
    var amountInput   = document.getElementById('amount');
    var debounceTimer = null;
    var allContracts  = [];

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        var q = this.value.trim();
        if (q.length < 2) { searchResults.classList.add('hidden'); return; }
        debounceTimer = setTimeout(function() {
            fetch('{{ route("rents.search-tenants") }}?q=' + encodeURIComponent(q), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                allContracts = data;
                searchResults.textContent = '';
                if (!data.length) {
                    var empty = document.createElement('div');
                    empty.className = 'px-4 py-3 text-xs text-slate-400';
                    empty.textContent = 'Aucun résultat';
                    searchResults.appendChild(empty);
                    searchResults.classList.remove('hidden');
                    return;
                }

                var grouped = {};
                data.forEach(function(c) {
                    var key = c.tenant_id;
                    if (!grouped[key]) grouped[key] = { tenant: c.tenant_name, phone: c.phone, contracts: [] };
                    grouped[key].contracts.push(c);
                });

                Object.keys(grouped).forEach(function(tid) {
                    var g = grouped[tid];
                    var row = document.createElement('div');
                    row.className = 'tenant-result px-4 py-2.5 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0';
                    row.dataset.tenantId = tid;

                    var header = document.createElement('div');
                    header.className = 'flex items-center justify-between';
                    var nameSpan = document.createElement('span');
                    nameSpan.className = 'text-sm font-medium text-slate-800';
                    nameSpan.textContent = g.tenant;
                    var phoneSpan = document.createElement('span');
                    phoneSpan.className = 'text-[11px] text-slate-400';
                    phoneSpan.textContent = g.phone || '';
                    header.appendChild(nameSpan);
                    header.appendChild(phoneSpan);
                    row.appendChild(header);

                    g.contracts.forEach(function(c) {
                        var detail = document.createElement('div');
                        detail.className = 'text-[11px] text-slate-500 mt-0.5';
                        detail.textContent = c.property + ' — ' + Number(c.rent_amount).toLocaleString('fr-FR') + ' F';
                        row.appendChild(detail);
                    });

                    row.addEventListener('click', function() {
                        var tenantContracts = allContracts.filter(function(c) { return String(c.tenant_id) === tid; });
                        selectTenant(tenantContracts);
                        searchResults.classList.add('hidden');
                    });

                    searchResults.appendChild(row);
                });
                searchResults.classList.remove('hidden');
            });
        }, 250);
    });

    function selectTenant(contracts) {
        if (contracts.length === 1) {
            selectContract(contracts[0]);
            contractSelectWrap.classList.add('hidden');
        } else {
            contractSelect.innerHTML = '<option value="">Choisir un contrat...</option>';
            contracts.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.contract_id;
                opt.textContent = c.contract_number + ' — ' + c.property + ' (' + Number(c.rent_amount).toLocaleString('fr-FR') + ' F)';
                opt.dataset.rent = c.rent_amount;
                opt.dataset.tenant = c.tenant_name;
                opt.dataset.property = c.property;
                contractSelect.appendChild(opt);
            });
            contractSelectWrap.classList.remove('hidden');
            searchInput.value = contracts[0].tenant_name;
        }
    }

    contractSelect.addEventListener('change', function() {
        var opt = this.options[this.selectedIndex];
        if (!opt.value) return;
        var c = allContracts.find(function(x) { return String(x.contract_id) === opt.value; });
        if (c) selectContract(c);
    });

    function selectContract(c) {
        contractInput.value = c.contract_id;
        searchInput.value = c.tenant_name;
        amountInput.value = Math.round(c.rent_amount);
        document.getElementById('info-tenant').textContent = c.tenant_name;
        document.getElementById('info-property').textContent = c.property + (c.address ? ' — ' + c.address : '');
        document.getElementById('info-rent').textContent = Number(c.rent_amount).toLocaleString('fr-FR');
        contractInfo.classList.remove('hidden');

        if (c.phone) document.getElementById('phone').value = c.phone;
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#tenant-search-wrap')) searchResults.classList.add('hidden');
    });

    // MoneyFusion toggle
    var methodSelect = document.getElementById('payment_method');
    var phoneField   = document.getElementById('phone_field');
    var phoneInput   = document.getElementById('phone');
    function togglePhone() {
        var show = methodSelect.value === 'moneyfusion';
        phoneField.classList.toggle('hidden', !show);
        phoneInput.required = show;
    }
    methodSelect.addEventListener('change', togglePhone);
    togglePhone();

    // Commission hint
    var commInput = document.getElementById('commission_percent');
    function updateCommHint() {
        var hint = document.getElementById('commission-hint');
        var pct = parseFloat(commInput.value);
        var amt = parseFloat(amountInput.value);
        if (pct > 0 && amt > 0) {
            hint.textContent = 'Déduction : ' + Math.round(amt * pct / 100).toLocaleString('fr-FR') + ' FCFA';
        } else {
            hint.textContent = '';
        }
    }
    commInput.addEventListener('input', updateCommHint);
    amountInput.addEventListener('input', updateCommHint);

    // Payment count hint
    var countInput = document.getElementById('payment_count');
    var periodInput = document.getElementById('period');
    function updateCountHint() {
        var hint = document.getElementById('payment-count-hint');
        var n = parseInt(countInput.value) || 1;
        if (n > 1 && periodInput.value) {
            var parts = periodInput.value.split('-');
            var base = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1);
            var end = new Date(base);
            end.setMonth(end.getMonth() + n - 1);
            var months = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
            hint.textContent = n + ' paiements : ' + months[base.getMonth()] + ' ' + base.getFullYear() + ' → ' + months[end.getMonth()] + ' ' + end.getFullYear();
        } else {
            hint.textContent = '';
        }
    }
    countInput.addEventListener('input', updateCountHint);
    periodInput.addEventListener('input', updateCountHint);
});
</script>
@endsection
