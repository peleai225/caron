@extends('layouts.app')

@section('title', 'Nouvelle facture')
@section('page-title', 'Nouvelle facture')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouvelle facture</h2>
            <p class="page-subtitle">Creer une facture pour un contrat</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('invoices.store') }}" method="POST">
            @csrf

            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Contrat <span class="text-red-500">*</span></label>
                    <select name="contract_id" class="input-modern searchable-select" required>
                        <option value="">Selectionner un contrat</option>
                        @foreach($contracts as $contract)
                            <option value="{{ $contract->id }}" {{ old('contract_id') == $contract->id ? 'selected' : '' }}>
                                {{ $contract->tenant->full_name ?? 'N/A' }} - {{ $contract->property->address ?? 'N/A' }} ({{ $contract->contract_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('contract_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type de facture <span class="text-red-500">*</span></label>
                    <select name="invoice_type" class="input-modern" required>
                        <option value="loyer" {{ old('invoice_type') == 'loyer' ? 'selected' : '' }}>Loyer</option>
                        <option value="commission" {{ old('invoice_type') == 'commission' ? 'selected' : '' }}>Commission</option>
                        <option value="charges" {{ old('invoice_type') == 'charges' ? 'selected' : '' }}>Charges locatives</option>
                        <option value="other" {{ old('invoice_type') == 'other' ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('invoice_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Montant HT (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" placeholder="0" class="input-modern" required>
                        @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">TVA (FCFA)</label>
                        <input type="number" name="tax_amount" value="{{ old('tax_amount', 0) }}" step="0.01" min="0" placeholder="0" class="input-modern">
                        @error('tax_amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date d'emission <span class="text-red-500">*</span></label>
                        <input type="date" name="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}" class="input-modern" required>
                        @error('issue_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date d'echeance <span class="text-red-500">*</span></label>
                        <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" class="input-modern" required>
                        @error('due_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Description</label>
                    <textarea name="description" rows="3" placeholder="Details de la facture..." class="input-modern">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('invoices.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Creer la facture</button>
            </div>
        </form>
    </div>
</div>
@endsection
