@extends('layouts.app')

@section('title', 'Nouvel etat des lieux')
@section('page-title', 'Nouvel etat des lieux')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouvel etat des lieux</h2>
            <p class="page-subtitle">Enregistrez un etat des lieux</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('etat-des-lieux.store') }}" method="POST">
            @csrf

            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bien <span class="text-red-500">*</span></label>
                    <select name="property_id" required class="input-modern">
                        <option value="">Selectionner un bien</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" {{ ($property && $property->id == $p->id) ? 'selected' : '' }}>{{ $p->name ?? $p->address }}</option>
                        @endforeach
                    </select>
                    @error('property_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Contrat (optionnel)</label>
                    <select name="contract_id" class="input-modern">
                        <option value="">—</option>
                        @foreach($contracts as $c)
                            <option value="{{ $c->id }}" {{ ($contract && $contract->id == $c->id) ? 'selected' : '' }}>
                                {{ $c->property?->name ?? '#' . $c->id }} — {{ $c->tenant?->full_name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="input-modern">
                            <option value="entree" {{ old('type') == 'entree' ? 'selected' : '' }}>Entree</option>
                            <option value="sortie" {{ old('type') == 'sortie' ? 'selected' : '' }}>Sortie</option>
                        </select>
                        @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="input-modern">
                        @error('date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Observations</label>
                    <textarea name="observations" rows="4" class="input-modern" placeholder="Etat des lieux, remarques...">{{ old('observations') }}</textarea>
                    @error('observations')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('etat-des-lieux.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
