@extends('layouts.app')

@section('title', 'Modifier le profil')
@section('page-title', 'Modifier le profil')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Modifier le profil</h2>
            <p class="page-subtitle">Mettez a jour vos informations personnelles</p>
        </div>
    </header>

    <!-- Personal info -->
    <div class="card-panel">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-panel-header">Informations personnelles</div>
            <div class="card-panel-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input-modern">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input-modern">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Telephone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input-modern" placeholder="+225 07 00 00 00 00">
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Biographie</label>
                    <textarea name="bio" rows="3" class="input-modern" placeholder="Quelques mots sur vous...">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="px-5 py-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>

    <!-- Password -->
    <div class="card-panel">
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-panel-header">Changer le mot de passe</div>
            <div class="card-panel-body space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Mot de passe actuel <span class="text-red-500">*</span></label>
                        <input type="password" name="current_password" required class="input-modern">
                        @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Nouveau mot de passe <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required class="input-modern">
                        @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Confirmer <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required class="input-modern">
                </div>
            </div>
            <div class="px-5 py-4 border-t border-slate-100 flex justify-end">
                <button type="submit" class="btn-secondary">Changer le mot de passe</button>
            </div>
        </form>
    </div>

    <div>
        <a href="{{ route('profile.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-700 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Retour au profil
        </a>
    </div>
</div>
@endsection
