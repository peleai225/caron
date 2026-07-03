@extends('layouts.app')

@section('title', 'Nouvelle agence')
@section('page-title', 'Nouvelle agence')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouvelle agence</h2>
            <p class="page-subtitle">Creez une agence immobiliere</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('agencies.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Logo</label>
                    <input type="file" name="logo" accept="image/*" class="input-modern text-xs">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="input-modern">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="input-modern">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Telephone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Site web</label>
                        <input type="url" name="website" value="{{ old('website') }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Adresse</label>
                        <input type="text" name="address" value="{{ old('address') }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Ville</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Pays</label>
                        <input type="text" name="country" value="{{ old('country', 'CI') }}" maxlength="2" class="input-modern">
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-xs font-medium text-slate-600">Agence active</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Description</label>
                    <textarea name="description" rows="3" class="input-modern">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('agencies.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Creer l'agence</button>
            </div>
        </form>
    </div>
</div>
@endsection
