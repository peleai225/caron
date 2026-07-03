@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur')
@section('page-title', 'Modifier l\'utilisateur')

@section('content')
<div class="max-w-lg mx-auto space-y-5">

    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Modifier l'utilisateur</h2>
            <p class="page-subtitle">{{ $user->name }} — {{ $user->email }}</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn-secondary">Retour</a>
    </header>

    <div class="card-panel">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input-modern">
                    @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Adresse email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input-modern">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Rôle <span class="text-red-500">*</span></label>
                    <select name="role" required class="input-modern">
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input-modern">
                    @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>

</div>
@endsection
