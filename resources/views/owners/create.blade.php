@extends('layouts.app')

@section('title', 'Ajouter un proprietaire')
@section('page-title', 'Ajouter un proprietaire')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouveau proprietaire</h2>
            <p class="page-subtitle">Enregistrez un nouveau proprietaire</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('owners.store') }}" method="POST">
            @csrf

            <div class="card-panel-body space-y-5">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input-modern" placeholder="Nom du proprietaire">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="input-modern" placeholder="proprietaire@domaine.com">
                        <p class="mt-1 text-[11px] text-slate-400">Convention : email suivi du nom du bien gere</p>
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Telephone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required class="input-modern" placeholder="+225 XX XX XX XX XX">
                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Adresse</label>
                    <textarea name="address" rows="2" class="input-modern" placeholder="Adresse complete">{{ old('address') }}</textarea>
                    @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Numero d'identification</label>
                    <input type="text" name="identification_number" value="{{ old('identification_number') }}" class="input-modern" placeholder="CNI, Passeport, etc.">
                    @error('identification_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                    <textarea name="notes" rows="3" class="input-modern" placeholder="Notes supplementaires...">{{ old('notes') }}</textarea>
                    @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('owners.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
