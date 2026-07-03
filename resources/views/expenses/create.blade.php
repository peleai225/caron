@extends('layouts.app')

@section('title', 'Nouvelle depense')
@section('page-title', 'Nouvelle depense')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouvelle depense</h2>
            <p class="page-subtitle">Enregistrez une depense</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bien immobilier</label>
                    <select name="property_id" class="input-modern searchable-select">
                        <option value="">Aucun bien (depense generale)</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>{{ $property->address }} - {{ $property->city }}</option>
                        @endforeach
                    </select>
                    @error('property_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type de depense <span class="text-red-500">*</span></label>
                    <select name="type" class="input-modern" required>
                        <option value="">Sélectionner un type</option>
                        <option value="maintenance" {{ old('type') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="tax" {{ old('type') == 'tax' ? 'selected' : '' }}>Taxe</option>
                        <option value="insurance" {{ old('type') == 'insurance' ? 'selected' : '' }}>Assurance</option>
                        <option value="utilities" {{ old('type') == 'utilities' ? 'selected' : '' }}>Services publics</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" placeholder="0" class="input-modern" required>
                        @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="expense_date" value="{{ old('expense_date', now()->format('Y-m-d')) }}" class="input-modern">
                        @error('expense_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" placeholder="Details de la depense..." class="input-modern" required>{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Justificatif</label>
                    <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png" class="input-modern text-xs">
                    @error('receipt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('expenses.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
