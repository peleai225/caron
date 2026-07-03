@extends('layouts.app')

@section('title', 'Agences')
@section('page-title', 'Agences')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Agences</h2>
            <p class="page-subtitle">Gerez les agences immobilieres</p>
        </div>
        <a href="{{ route('agencies.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nouvelle agence
        </a>
    </header>

    <!-- Filters -->
    <form method="GET" action="{{ route('agencies.index') }}" class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email, ville..." class="input-modern">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut</label>
                    <select name="status" class="input-modern">
                        <option value="">Tous</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actives</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactives</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary flex-1">Filtrer</button>
                    <a href="{{ route('agencies.index') }}" class="btn-secondary">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Agencies Grid -->
    @if($agencies->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($agencies as $agency)
            <div class="card-panel hover:border-slate-300 transition-colors">
                <div class="p-5">
                    <div class="flex items-start gap-3 mb-4">
                        @if($agency->logo_path)
                            <img src="{{ asset('storage/' . $agency->logo_path) }}" alt="{{ $agency->name }}" class="w-10 h-10 rounded-lg object-cover" loading="lazy">
                        @else
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                <span class="text-primary-700 font-semibold text-sm">{{ substr($agency->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-slate-900">{{ $agency->name }}</h3>
                            @if($agency->is_active)
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">Active</span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-slate-50 text-slate-600 ring-slate-500/20">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-1.5 text-xs text-slate-500 mb-4">
                        @if($agency->email)
                            <p>{{ $agency->email }}</p>
                        @endif
                        @if($agency->phone)
                            <p>{{ $agency->phone }}</p>
                        @endif
                        @if($agency->city)
                            <p>{{ $agency->city }}, {{ $agency->country }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center text-xs mb-4 pb-4 border-b border-slate-100">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $agency->users_count }}</p>
                            <p class="text-slate-400">Users</p>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $agency->properties_count }}</p>
                            <p class="text-slate-400">Biens</p>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $agency->tenants_count }}</p>
                            <p class="text-slate-400">Locataires</p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('agencies.show', $agency) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                        <a href="{{ route('agencies.edit', $agency) }}" class="px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded transition-colors">Modifier</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($agencies->hasPages())
        <div class="mt-4">{{ $agencies->links() }}</div>
        @endif
    @else
        <div class="card-panel">
            <div class="card-panel-body text-center py-12">
                <div class="empty-state-icon">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucune agence</h3>
                <p class="text-xs text-slate-500 mb-4">Creez votre premiere agence</p>
                <a href="{{ route('agencies.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nouvelle agence
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
