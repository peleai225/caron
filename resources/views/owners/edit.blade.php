@extends('layouts.app')

@section('title', 'Modifier le proprietaire')
@section('page-title', 'Modifier le proprietaire')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Modifier le proprietaire</h2>
            <p class="page-subtitle">{{ $owner->name }}</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('owners.update', $owner) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-panel-body space-y-5">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $owner->name) }}" required class="input-modern">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $owner->email) }}" class="input-modern" placeholder="proprietaire@domaine.com">
                        <p class="mt-1 text-[11px] text-slate-400">Convention : email suivi du nom du bien gere</p>
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Telephone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $owner->phone) }}" required class="input-modern">
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Adresse</label>
                    <textarea name="address" rows="2" class="input-modern">{{ old('address', $owner->address) }}</textarea>
                    @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Numero d'identification</label>
                    <input type="text" name="identification_number" value="{{ old('identification_number', $owner->identification_number) }}" class="input-modern">
                    @error('identification_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $owner->is_active) ? 'checked' : '' }} class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                        <span class="text-xs font-medium text-slate-600">Proprietaire actif</span>
                    </label>
                    @error('is_active') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                    <textarea name="notes" rows="3" class="input-modern">{{ old('notes', $owner->notes) }}</textarea>
                    @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('owners.show', $owner) }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
