@extends('layouts.app')

@section('title', 'Modifier l\'état des lieux')
@section('page-title', 'Modifier l\'état des lieux')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Modifier l'état des lieux</h2>
            <p class="page-subtitle">{{ $etatDesLieux->property?->name ?? $etatDesLieux->property?->address }} — {{ $etatDesLieux->date?->format('d/m/Y') }}</p>
        </div>
        <a href="{{ route('etat-des-lieux.index') }}" class="btn-secondary">Retour</a>
    </header>

    <div class="card-panel">
        <form action="{{ route('etat-des-lieux.update', $etatDesLieux) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bien <span class="text-red-500">*</span></label>
                    <select name="property_id" required class="input-modern">
                        <option value="">Sélectionner un bien</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" {{ old('property_id', $etatDesLieux->property_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->name ?? $p->address }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Contrat (optionnel)</label>
                    <select name="contract_id" class="input-modern">
                        <option value="">—</option>
                        @foreach($contracts as $c)
                            <option value="{{ $c->id }}" {{ old('contract_id', $etatDesLieux->contract_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->property?->name ?? '#' . $c->id }} — {{ $c->tenant?->full_name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    @error('contract_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="input-modern">
                            <option value="entree" {{ old('type', $etatDesLieux->type) == 'entree' ? 'selected' : '' }}>Entrée</option>
                            <option value="sortie" {{ old('type', $etatDesLieux->type) == 'sortie' ? 'selected' : '' }}>Sortie</option>
                        </select>
                        @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ old('date', $etatDesLieux->date?->format('Y-m-d')) }}" required class="input-modern">
                        @error('date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Observations</label>
                    <textarea name="observations" rows="4" class="input-modern" placeholder="État des lieux, remarques...">{{ old('observations', $etatDesLieux->observations) }}</textarea>
                    @error('observations')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('etat-des-lieux.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>
@endsection
