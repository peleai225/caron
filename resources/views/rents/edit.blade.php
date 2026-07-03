@extends('layouts.app')

@section('title', 'Modifier le paiement')
@section('page-title', 'Modifier le paiement')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Modifier le paiement #{{ $payment->id }}</h2>
            <p class="page-subtitle">
                @if($payment->contract && $payment->contract->tenant)
                    {{ $payment->contract->tenant->full_name }} - {{ $payment->contract->property->address ?? 'N/A' }}
                @else
                    Contrat non associé
                @endif
            </p>
        </div>
    </header>

    <div class="card-panel">
        <form method="POST" action="{{ route('rents.update', $payment) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="contract_id" value="{{ $payment->contract_id }}">

            <div class="card-panel-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="amount" class="block text-xs font-medium text-slate-600 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" id="amount" name="amount" required value="{{ old('amount', $payment->amount) }}" min="0" step="0.01" class="input-modern">
                        @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="penalty_amount" class="block text-xs font-medium text-slate-600 mb-1.5">Pénalités (FCFA)</label>
                        <input type="number" id="penalty_amount" name="penalty_amount" value="{{ old('penalty_amount', $payment->penalty_amount) }}" min="0" step="0.01" class="input-modern">
                        @error('penalty_amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payment_date" class="block text-xs font-medium text-slate-600 mb-1.5">Date de paiement <span class="text-red-500">*</span></label>
                        <input type="date" id="payment_date" name="payment_date" required value="{{ old('payment_date', $payment->payment_date?->format('Y-m-d')) }}" class="input-modern">
                        @error('payment_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="period" class="block text-xs font-medium text-slate-600 mb-1.5">Période <span class="text-red-500">*</span></label>
                        <input type="month" id="period" name="period" required value="{{ old('period', $payment->period) }}" class="input-modern">
                        @error('period') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payment_type" class="block text-xs font-medium text-slate-600 mb-1.5">Type de paiement</label>
                        <select id="payment_type" name="payment_type" class="input-modern">
                            @foreach(\App\Models\Payment::paymentTypes() as $value => $label)
                                <option value="{{ $value }}" {{ old('payment_type', $payment->payment_type ?? 'loyer') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="charges_amount" class="block text-xs font-medium text-slate-600 mb-1.5">Charges (FCFA)</label>
                        <input type="number" id="charges_amount" name="charges_amount" value="{{ old('charges_amount', $payment->charges_amount ?? 0) }}" min="0" step="0.01" class="input-modern">
                    </div>

                    <div>
                        <label for="depense_travaux" class="block text-xs font-medium text-slate-600 mb-1.5">Dépense travaux (FCFA)</label>
                        <input type="number" id="depense_travaux" name="depense_travaux" value="{{ old('depense_travaux', $payment->depense_travaux) }}" min="0" step="0.01" class="input-modern">
                    </div>

                    <div>
                        <label for="commission_percent" class="block text-xs font-medium text-slate-600 mb-1.5">Commission agence (%)</label>
                        <input type="number" id="commission_percent" name="commission_percent" value="{{ old('commission_percent', $payment->commission_percent) }}" min="0" max="100" step="0.01" class="input-modern">
                    </div>

                    <div>
                        <label for="payment_method" class="block text-xs font-medium text-slate-600 mb-1.5">Méthode de paiement <span class="text-red-500">*</span></label>
                        <select id="payment_method" name="payment_method" required class="input-modern">
                            <option value="moneyfusion" {{ old('payment_method', $payment->payment_method) == 'moneyfusion' ? 'selected' : '' }}>MoneyFusion (Mobile Money)</option>
                            <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>Espèces</option>
                            <option value="check" {{ old('payment_method', $payment->payment_method) == 'check' ? 'selected' : '' }}>Chèque</option>
                        </select>
                        @error('payment_method') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-medium text-slate-600 mb-1.5">Statut <span class="text-red-500">*</span></label>
                        <select id="status" name="status" required class="input-modern">
                            <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="completed" {{ old('status', $payment->status) == 'completed' ? 'selected' : '' }}>Complété</option>
                            <option value="failed" {{ old('status', $payment->status) == 'failed' ? 'selected' : '' }}>Échoué</option>
                            <option value="refunded" {{ old('status', $payment->status) == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                        </select>
                        @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="reference" class="block text-xs font-medium text-slate-600 mb-1.5">Référence de transaction</label>
                        <input type="text" id="reference" name="reference" value="{{ old('reference', $payment->reference) }}" class="input-modern" placeholder="Numéro de transaction">
                        @error('reference') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="input-modern" placeholder="Notes additionnelles...">{{ old('notes', $payment->notes) }}</textarea>
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('rents.show', $payment) }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
