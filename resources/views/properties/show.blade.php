@extends('layouts.app')

@section('title', 'Details du bien')
@section('page-title', 'Details du bien')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">{{ $property->address }}</h2>
            <p class="page-subtitle">{{ $property->city }}{{ $property->neighborhood ? ', ' . $property->neighborhood : '' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('properties.edit', $property) }}" class="btn-primary">Modifier</a>
            <a href="{{ route('properties.index') }}" class="btn-secondary">Retour</a>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Main -->
        <div class="lg:col-span-2 space-y-5">
            <!-- Photos -->
            <div class="card-panel">
                <div class="card-panel-header">Photos</div>
                <div class="card-panel-body">
                    @if($property->images->count() > 0)
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($property->images as $image)
                                <div class="relative aspect-video bg-slate-100 rounded-lg overflow-hidden">
                                    <img src="{{ asset('storage/' . $image->path) }}" alt="Photo du bien" class="w-full h-full object-cover" loading="lazy">
                                    @if($image->is_primary)
                                        <span class="absolute top-2 right-2 px-1.5 py-0.5 text-[10px] font-medium bg-primary-600 text-white rounded">Principale</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-10">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-xs text-slate-400">Aucune photo disponible</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div class="card-panel">
                <div class="card-panel-header">Description</div>
                <div class="card-panel-body">
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $property->description ?? 'Aucune description disponible.' }}</p>
                </div>
            </div>

            <!-- Contracts -->
            @if($property->contracts->count() > 0)
            <div class="card-panel">
                <div class="card-panel-header">Contrats</div>
                <div class="divide-y divide-slate-100">
                    @foreach($property->contracts as $contract)
                        <div class="px-5 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $contract->tenant->full_name ?? 'Locataire non associe' }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $contract->start_date->format('d/m/Y') }} - {{ $contract->end_date->format('d/m/Y') }} — {{ number_format($contract->rent_amount, 0, ',', ' ') }} FCFA/mois</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-status-badge :status="$contract->status" />
                                <a href="{{ route('contracts.show', $contract) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-5">
            <div class="card-panel">
                <div class="card-panel-header">Informations</div>
                <div class="card-panel-body space-y-3">
                    <div>
                        <p class="text-xs text-slate-500">Type</p>
                        <p class="text-sm font-medium text-slate-900 capitalize">{{ $property->type }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 mb-0.5">Statut</p>
                        <x-status-badge :status="$property->status" />
                    </div>
                    @if($property->bedrooms)
                    <div>
                        <p class="text-xs text-slate-500">Chambres</p>
                        <p class="text-sm font-medium text-slate-900">{{ $property->bedrooms }}</p>
                    </div>
                    @endif
                    @if($property->bathrooms)
                    <div>
                        <p class="text-xs text-slate-500">Salles de bain</p>
                        <p class="text-sm font-medium text-slate-900">{{ $property->bathrooms }}</p>
                    </div>
                    @endif
                    @if($property->surface)
                    <div>
                        <p class="text-xs text-slate-500">Surface</p>
                        <p class="text-sm font-medium text-slate-900">{{ number_format($property->surface, 0, ',', ' ') }} m2</p>
                    </div>
                    @endif
                    @if($property->monthly_rent)
                    <div>
                        <p class="text-xs text-slate-500">Loyer mensuel</p>
                        <p class="text-sm font-medium text-slate-900">{{ number_format($property->monthly_rent, 0, ',', ' ') }} FCFA</p>
                    </div>
                    @endif
                    @if($property->owner)
                    <div>
                        <p class="text-xs text-slate-500">Proprietaire</p>
                        <p class="text-sm font-medium text-slate-900">{{ $property->owner->name }}</p>
                        @if($property->owner->phone)
                            <p class="text-xs text-slate-500">{{ $property->owner->phone }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="card-panel">
                <div class="card-panel-header">Actions rapides</div>
                <div class="card-panel-body space-y-1">
                    <a href="{{ route('contracts.create', ['property_id' => $property->id]) }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-md transition-colors">Creer un contrat</a>
                    <a href="{{ route('properties.edit', $property) }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-md transition-colors">Modifier le bien</a>
                    <form id="form-delete-property-{{ $property->id }}" action="{{ route('properties.destroy', $property) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" data-confirm="Supprimer ce bien ?" data-confirm-form="form-delete-property-{{ $property->id }}" class="block w-full text-left px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 rounded-md transition-colors">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
