@extends('layouts.app')

@section('title', 'Mon profil')
@section('page-title', 'Mon profil')

@section('content')
<div class="space-y-5">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Main -->
        <div class="lg:col-span-2 space-y-5">
            <!-- Profile header -->
            <div class="card-panel">
                <div class="card-panel-body">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            @if($user->avatar_path)
                                <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-xl object-cover" loading="lazy">
                            @else
                                <div class="w-16 h-16 rounded-xl bg-primary-100 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-primary-700">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $user->name }}</h2>
                                <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                @if($user->agency)
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $user->agency->name }}</p>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="btn-primary">Modifier le profil</a>
                    </div>
                </div>
            </div>

            <!-- Info -->
            <div class="card-panel">
                <div class="card-panel-header">Informations personnelles</div>
                <div class="card-panel-body">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs text-slate-500">Nom complet</dt>
                            <dd class="text-sm font-medium text-slate-900 mt-0.5">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-500">Email</dt>
                            <dd class="text-sm font-medium text-slate-900 mt-0.5">{{ $user->email }}</dd>
                        </div>
                        @if($user->phone)
                        <div>
                            <dt class="text-xs text-slate-500">Telephone</dt>
                            <dd class="text-sm font-medium text-slate-900 mt-0.5">{{ $user->phone }}</dd>
                        </div>
                        @endif
                        @if($user->agency)
                        <div>
                            <dt class="text-xs text-slate-500">Agence</dt>
                            <dd class="text-sm font-medium text-slate-900 mt-0.5">{{ $user->agency->name }}</dd>
                        </div>
                        @endif
                        @if($user->bio)
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-slate-500">Biographie</dt>
                            <dd class="text-sm text-slate-700 mt-0.5">{{ $user->bio }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            @if($user->roles->count() > 0)
            <div class="card-panel">
                <div class="card-panel-header">Roles</div>
                <div class="card-panel-body">
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($user->roles as $role)
                        <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-primary-50 text-primary-700 ring-1 ring-inset ring-primary-600/20">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-5">
            <!-- Avatar -->
            <div class="card-panel">
                <div class="card-panel-header">Photo de profil</div>
                <div class="card-panel-body text-center">
                    @if($user->avatar_path)
                        <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-xl object-cover mx-auto border border-slate-100" loading="lazy">
                    @else
                        <div class="w-20 h-20 rounded-xl bg-primary-100 flex items-center justify-center mx-auto">
                            <span class="text-3xl font-bold text-primary-700">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                        @csrf
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                        <label for="avatar" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 text-slate-700 rounded-md hover:bg-slate-100 transition-colors cursor-pointer text-xs font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Changer la photo
                        </label>
                    </form>
                    @if($user->avatar_path)
                    <form action="{{ route('profile.avatar.delete') }}" method="POST" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-[11px] text-red-600 hover:text-red-700 font-medium">Supprimer</button>
                    </form>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card-panel">
                <div class="card-panel-header">Actions rapides</div>
                <div class="card-panel-body space-y-1">
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-md transition-colors">Modifier le profil</a>
                    <a href="{{ route('settings.index') }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-md transition-colors">Parametres</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
