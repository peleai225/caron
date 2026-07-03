@extends('layouts.app')

@section('title', 'Ajouter un locataire')
@section('page-title', 'Ajouter un locataire')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouveau locataire</h2>
            <p class="page-subtitle">Enregistrez un nouveau locataire</p>
        </div>
    </header>

    <div class="card-panel">
        <form method="POST" action="{{ route('tenants.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Personal Information -->
            <div class="card-panel-body space-y-5">
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Informations personnelles</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-xs font-medium text-slate-600 mb-1.5">Prenom <span class="text-red-500">*</span></label>
                        <input type="text" id="first_name" name="first_name" required value="{{ old('first_name') }}" class="input-modern" placeholder="Jean">
                        @error('first_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-xs font-medium text-slate-600 mb-1.5">Nom <span class="text-red-500">*</span></label>
                        <input type="text" id="last_name" name="last_name" required value="{{ old('last_name') }}" class="input-modern" placeholder="Dupont">
                        @error('last_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="input-modern" placeholder="jean.dupont@email.com">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-xs font-medium text-slate-600 mb-1.5">Telephone <span class="text-red-500">*</span></label>
                        <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}" class="input-modern" placeholder="+225 07 12 34 56 78">
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="cni_number" class="block text-xs font-medium text-slate-600 mb-1.5">Numéro CNI / Passeport</label>
                        <input type="text" id="cni_number" name="cni_number" value="{{ old('cni_number') }}" class="input-modern" placeholder="Ex: CI-0123456789">
                        @error('cni_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Documents -->
                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider pt-4">Documents</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="cni" class="block text-xs font-medium text-slate-600 mb-1.5">CNI / Passeport</label>
                        <input type="file" id="cni" name="cni" accept="image/*,.pdf" class="input-modern text-xs">
                    </div>
                    <div>
                        <label for="contract" class="block text-xs font-medium text-slate-600 mb-1.5">Contrat (si disponible)</label>
                        <input type="file" id="contract" name="contract" accept=".pdf" class="input-modern text-xs">
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="input-modern" placeholder="Informations supplementaires...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('tenants.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
