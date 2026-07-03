@extends('layouts.app')

@section('title', 'Modifier la depense')
@section('page-title', 'Modifier la depense')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Modifier la depense</h2>
            <p class="page-subtitle">{{ $expense->type }} — {{ number_format($expense->amount, 0, ',', ' ') }} FCFA</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bien immobilier</label>
                    <select name="property_id" class="input-modern">
                        <option value="">Aucun bien (depense generale)</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id', $expense->property_id) == $property->id ? 'selected' : '' }}>{{ $property->address }} - {{ $property->city }}</option>
                        @endforeach
                    </select>
                    @error('property_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type de depense <span class="text-red-500">*</span></label>
                    <select name="type" class="input-modern" required>
                        <option value="">Sélectionner un type</option>
                        <option value="maintenance" {{ old('type', $expense->type) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="tax" {{ old('type', $expense->type) == 'tax' ? 'selected' : '' }}>Taxe</option>
                        <option value="insurance" {{ old('type', $expense->type) == 'insurance' ? 'selected' : '' }}>Assurance</option>
                        <option value="utilities" {{ old('type', $expense->type) == 'utilities' ? 'selected' : '' }}>Services publics</option>
                        <option value="other" {{ old('type', $expense->type) == 'other' ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount', $expense->amount) }}" step="0.01" min="0" class="input-modern">
                        @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" class="input-modern">
                        @error('expense_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" placeholder="Details de la depense..." class="input-modern">{{ old('description', $expense->description) }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Justificatif</label>
                    @if($expense->receipt_path)
                        <p class="text-xs text-slate-500 mb-2">Fichier actuel : <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="text-primary-600 hover:text-primary-700 font-medium">Voir</a></p>
                    @endif
                    <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png" class="input-modern text-xs">
                    @error('receipt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('expenses.show', $expense) }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
