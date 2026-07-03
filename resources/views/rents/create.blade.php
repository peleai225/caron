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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="contract_id" class="block text-xs font-medium text-slate-600 mb-1.5">Contrat <span class="text-red-500">*</span></label>
                        <select id="contract_id" name="contract_id" required class="input-modern">
                            <option value="">Selectionner un contrat...</option>
                            @foreach($contracts ?? [] as $c)
                                <option value="{{ $c->id }}" data-rent-amount="{{ $c->rent_amount }}" {{ old('contract_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->contract_number ?? '#' . $c->id }} — {{ $c->property?->name ?? 'Bien' }} — {{ $c->tenant?->full_name ?? 'Locataire' }} ({{ number_format($c->rent_amount, 0, ',', ' ') }} F)
                                </option>
                            @endforeach
                        </select>
                        @error('contract_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="amount" class="block text-xs font-medium text-slate-600 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" id="amount" name="amount" required value="{{ old('amount') }}" min="0" step="0.01" class="input-modern" placeholder="150000">
                        @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payment_date" class="block text-xs font-medium text-slate-600 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" id="payment_date" name="payment_date" required value="{{ old('payment_date', date('Y-m-d')) }}" class="input-modern">
                        @error('payment_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs font-medium text-slate-600 mb-1.5">Méthode <span class="text-red-500">*</span></label>
                        <select id="payment_method" name="payment_method" required class="input-modern">
                            <option value="moneyfusion" {{ old('payment_method', 'moneyfusion') == 'moneyfusion' ? 'selected' : '' }}>MoneyFusion (Mobile Money)</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                            <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Chèque</option>
                        </select>
                        @error('payment_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div id="phone_field">
                        <label for="phone" class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone MoneyFusion <span class="text-red-500">*</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="input-modern" placeholder="+225 07 12 34 56 78">
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="period" class="block text-xs font-medium text-slate-600 mb-1.5">Periode <span class="text-red-500">*</span></label>
                        <input type="month" id="period" name="period" required value="{{ old('period', date('Y-m')) }}" class="input-modern">
                        @error('period') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="reference" class="block text-xs font-medium text-slate-600 mb-1.5">Reference</label>
                        <input type="text" id="reference" name="reference" value="{{ old('reference') }}" class="input-modern" placeholder="Numero de transaction">
                        @error('reference') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payment_type" class="block text-xs font-medium text-slate-600 mb-1.5">Type</label>
                        <select id="payment_type" name="payment_type" class="input-modern">
                            @foreach(\App\Models\Payment::paymentTypes() as $value => $label)
                                <option value="{{ $value }}" {{ old('payment_type', 'loyer') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="charges_amount" class="block text-xs font-medium text-slate-600 mb-1.5">Charges (FCFA)</label>
                        <input type="number" id="charges_amount" name="charges_amount" value="{{ old('charges_amount', 0) }}" min="0" step="0.01" class="input-modern">
                    </div>

                    <div>
                        <label for="depense_travaux" class="block text-xs font-medium text-slate-600 mb-1.5">Depense travaux (FCFA)</label>
                        <input type="number" id="depense_travaux" name="depense_travaux" value="{{ old('depense_travaux') }}" min="0" step="0.01" class="input-modern">
                    </div>

                    <div>
                        <label for="commission_percent" class="block text-xs font-medium text-slate-600 mb-1.5">Commission agence (%)</label>
                        <input type="number" id="commission_percent" name="commission_percent" value="{{ old('commission_percent') }}" min="0" max="100" step="0.01" placeholder="3 a 15" class="input-modern">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="input-modern" placeholder="Notes additionnelles...">{{ old('notes') }}</textarea>
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
    var contractSelect = document.getElementById('contract_id');
    var amountInput = document.getElementById('amount');
    function updateAmount() {
        var opt = contractSelect.options[contractSelect.selectedIndex];
        if (opt && opt.value && opt.dataset.rentAmount) {
            amountInput.value = parseFloat(opt.dataset.rentAmount).toFixed(0);
        }
    }
    if (contractSelect) {
        contractSelect.addEventListener('change', updateAmount);
        updateAmount();
    }

    var methodSelect = document.getElementById('payment_method');
    var phoneField = document.getElementById('phone_field');
    var phoneInput = document.getElementById('phone');
    function togglePhone() {
        var show = methodSelect.value === 'moneyfusion';
        phoneField.style.display = show ? '' : 'none';
        phoneInput.required = show;
    }
    if (methodSelect) {
        methodSelect.addEventListener('change', togglePhone);
        togglePhone();
    }
});
</script>
@endsection
