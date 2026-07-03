@extends('layouts.app')

@section('title', 'Inviter un utilisateur')
@section('page-title', 'Inviter un utilisateur')

@section('content')
<div class="max-w-lg mx-auto space-y-5">

    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Inviter un utilisateur</h2>
            <p class="page-subtitle">Un email d'activation lui sera envoyé automatiquement.</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="card-panel-body space-y-4">

                <div class="flex gap-3 p-3 bg-primary-50 rounded-lg border border-primary-100">
                    <svg class="w-5 h-5 text-primary-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-xs text-primary-700 leading-relaxed">
                        L'utilisateur recevra un email avec un lien pour activer son compte et définir son mot de passe. Le lien expire après <strong>48h</strong>.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input-modern" placeholder="Ex: Konan Yao">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Adresse email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="input-modern" placeholder="prenom.nom@email.com">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Rôle <span class="text-red-500">*</span></label>
                    <select name="role" required class="input-modern">
                        <option value="">Sélectionner un rôle</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="input-modern" placeholder="+225 07 00 00 00 00">
                    @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Description des rôles --}}
                <div class="pt-1">
                    <p class="text-xs font-medium text-slate-500 mb-2">Guide des rôles</p>
                    <div class="space-y-1.5 text-xs text-slate-500">
                        <div class="flex gap-2"><span class="font-medium text-slate-700 w-36 flex-shrink-0">Admin agence</span>Accès complet à l'agence</div>
                        <div class="flex gap-2"><span class="font-medium text-slate-700 w-36 flex-shrink-0">Gestionnaire</span>Biens, locataires, contrats, loyers</div>
                        <div class="flex gap-2"><span class="font-medium text-slate-700 w-36 flex-shrink-0">Agent immobilier</span>Locataires et loyers uniquement</div>
                        <div class="flex gap-2"><span class="font-medium text-slate-700 w-36 flex-shrink-0">Chargé recouvrement</span>Recouvrement, litiges, pénalités</div>
                        <div class="flex gap-2"><span class="font-medium text-slate-700 w-36 flex-shrink-0">Comptable</span>Finances et rapports</div>
                        <div class="flex gap-2"><span class="font-medium text-slate-700 w-36 flex-shrink-0">Propriétaire</span>Vue lecture seule de ses biens</div>
                    </div>
                </div>

            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Envoyer l'invitation
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
