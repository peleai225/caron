@extends('layouts.app')

@section('title', 'Biens Immobiliers')
@section('page-title', 'Biens Immobiliers')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Biens immobiliers</h2>
            <p class="page-subtitle">Gerez votre parc immobilier</p>
        </div>
        <a href="{{ route('properties.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Ajouter un bien
        </a>
    </header>

    <!-- Filters -->
    <form method="GET" action="{{ route('properties.index') }}" class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Adresse, quartier..." class="input-modern">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Proprietaire</label>
                    <select name="owner_id" class="input-modern">
                        <option value="">Tous</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" {{ request('owner_id') == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type</label>
                    <select name="type" class="input-modern">
                        <option value="">Tous</option>
                        <option value="maison" {{ request('type') == 'maison' ? 'selected' : '' }}>Maison</option>
                        <option value="immeuble" {{ request('type') == 'immeuble' ? 'selected' : '' }}>Immeuble</option>
                        <option value="boutique" {{ request('type') == 'boutique' ? 'selected' : '' }}>Boutique</option>
                        <option value="terrain" {{ request('type') == 'terrain' ? 'selected' : '' }}>Terrain</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut</label>
                    <select name="status" class="input-modern">
                        <option value="">Tous</option>
                        <option value="libre" {{ request('status') == 'libre' ? 'selected' : '' }}>Libre</option>
                        <option value="occupe" {{ request('status') == 'occupe' ? 'selected' : '' }}>Occupe</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary flex-1">Filtrer</button>
                    <a href="{{ route('properties.index') }}" class="btn-secondary">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Properties Grid -->
    @if($properties->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($properties as $property)
                <div class="card-panel overflow-hidden group hover:border-slate-300 transition-colors">
                    <!-- Image -->
                    <div class="h-40 bg-slate-100 flex items-center justify-center relative overflow-hidden">
                        @if($property->primaryImage)
                            <img src="{{ asset('storage/' . $property->primaryImage->path) }}" alt="{{ $property->address }}" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        @endif
                        <div class="absolute top-2 right-2">
                            <x-status-badge :status="$property->status ?? 'libre'" size="sm" />
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <div class="mb-3">
                            <h3 class="text-sm font-semibold text-slate-900 mb-0.5">{{ $property->name }}</h3>
                            <p class="text-xs text-slate-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $property->city ?? 'N/A' }}
                            </p>
                            @if($property->parent_id)
                                <span class="inline-block mt-1 px-1.5 py-0.5 text-[10px] font-medium bg-slate-100 text-slate-600 rounded">Unite</span>
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="flex items-center gap-3 text-xs text-slate-500 mb-3">
                            @if($property->bedrooms)
                                <span>{{ $property->bedrooms }} ch.</span>
                            @endif
                            @if($property->bathrooms)
                                <span>{{ $property->bathrooms }} sdb</span>
                            @endif
                            @if($property->surface)
                                <span>{{ number_format($property->surface, 0, ',', ' ') }} m2</span>
                            @endif
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                            @if($property->monthly_rent)
                                <span class="text-sm font-semibold text-slate-900">{{ number_format($property->monthly_rent, 0, ',', ' ') }} F</span>
                            @else
                                <span class="text-xs text-slate-400">Prix non defini</span>
                            @endif
                            <div class="flex gap-1">
                                <a href="{{ route('properties.show', $property) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                                <a href="{{ route('properties.edit', $property) }}" class="px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded transition-colors">Modifier</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card-panel">
            <div class="card-panel-body text-center py-12">
                <div class="empty-state-icon">
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucun bien enregistre</h3>
                <p class="text-xs text-slate-500 mb-4">Commencez par ajouter votre premier bien immobilier</p>
                <a href="{{ route('properties.create') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Ajouter un bien
                </a>
            </div>
        </div>
    @endif

    @if($properties->hasPages())
        <div class="mt-4">
            {{ $properties->links() }}
        </div>
    @endif
</div>
@endsection
