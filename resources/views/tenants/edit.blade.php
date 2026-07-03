@extends('layouts.app')

@section('title', 'Modifier le locataire')
@section('page-title', 'Modifier le locataire')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Modifier le locataire</h2>
            <p class="page-subtitle">{{ $tenant->full_name }}</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('tenants.update', $tenant) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-panel-body space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Prenom <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $tenant->first_name) }}" required class="input-modern">
                        @error('first_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $tenant->last_name) }}" required class="input-modern">
                        @error('last_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email', $tenant->email) }}" class="input-modern">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Telephone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" required class="input-modern">
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Numero CNI</label>
                        <input type="text" name="cni_number" value="{{ old('cni_number', $tenant->cni_number) }}" class="input-modern">
                        @error('cni_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut <span class="text-red-500">*</span></label>
                        <select name="status" required class="input-modern">
                            <option value="actif" {{ $tenant->status == 'actif' ? 'selected' : '' }}>Actif</option>
                            <option value="en_retard" {{ $tenant->status == 'en_retard' ? 'selected' : '' }}>En retard</option>
                            <option value="resilie" {{ $tenant->status == 'resilie' ? 'selected' : '' }}>Resilie</option>
                        </select>
                        @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                    <textarea name="notes" rows="3" class="input-modern">{{ old('notes', $tenant->notes) }}</textarea>
                    @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('tenants.show', $tenant) }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
