@extends('layouts.app')

@section('title', $owner->name)
@section('page-title', $owner->name)

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">{{ $owner->name }}</h2>
            <p class="page-subtitle">{{ $owner->email ?? 'Aucun email' }} — {{ $owner->phone }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('owners.edit', $owner) }}" class="btn-primary">Modifier</a>
            <a href="{{ route('owners.index') }}" class="btn-secondary">Retour</a>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-5">
            <!-- Informations -->
            <div class="card-panel">
                <div class="card-panel-header">Informations</div>
                <div class="card-panel-body grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate-500">Nom</p>
                        <p class="text-sm font-medium text-slate-900 mt-0.5">{{ $owner->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Email</p>
                        <p class="text-sm font-medium text-slate-900 mt-0.5">{{ $owner->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Telephone</p>
                        <p class="text-sm font-medium text-slate-900 mt-0.5">{{ $owner->phone }}</p>
                    </div>
                    @if($owner->identification_number)
                    <div>
                        <p class="text-xs text-slate-500">Identification</p>
                        <p class="text-sm font-medium text-slate-900 mt-0.5">{{ $owner->identification_number }}</p>
                    </div>
                    @endif
                    @if($owner->address)
                    <div class="md:col-span-2">
                        <p class="text-xs text-slate-500">Adresse</p>
                        <p class="text-sm font-medium text-slate-900 mt-0.5">{{ $owner->address }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-slate-500">Statut</p>
                        <div class="mt-1">
                            @if($owner->is_active)
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">Actif</span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-slate-50 text-slate-600 ring-slate-500/20">Inactif</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($owner->notes)
            <div class="card-panel">
                <div class="card-panel-header">Notes</div>
                <div class="card-panel-body">
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $owner->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Properties -->
            @if($owner->properties->count() > 0)
            <div class="card-panel overflow-hidden">
                <div class="card-panel-header">Biens immobiliers ({{ $owner->properties->count() }})</div>
                <div class="divide-y divide-slate-50">
                    @foreach($owner->properties as $property)
                    <div class="px-5 py-3 hover:bg-slate-50/50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $property->address }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $property->city }} — {{ ucfirst($property->type) }}
                                    @if($property->monthly_rent) — {{ number_format($property->monthly_rent, 0, ',', ' ') }} F/mois @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-status-badge :status="$property->status" size="sm" />
                                <a href="{{ route('properties.show', $property) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Contracts -->
            @if($owner->contracts->count() > 0)
            <div class="card-panel overflow-hidden">
                <div class="card-panel-header">Contrats ({{ $owner->contracts->count() }})</div>
                <div class="divide-y divide-slate-50">
                    @foreach($owner->contracts as $contract)
                    <div class="px-5 py-3 hover:bg-slate-50/50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $contract->contract_number }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $contract->tenant->full_name }} — {{ $contract->start_date->format('d/m/Y') }} au {{ $contract->end_date->format('d/m/Y') }}
                                </p>
                                <p class="text-xs font-medium text-slate-700 mt-0.5">{{ number_format($contract->rent_amount, 0, ',', ' ') }} F/mois</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-status-badge :status="$contract->status" size="sm" />
                                <a href="{{ route('contracts.show', $contract) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                            </div>
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
                <div class="card-panel-header">Statistiques</div>
                <div class="card-panel-body space-y-3">
                    <div>
                        <p class="text-xs text-slate-500">Biens immobiliers</p>
                        <p class="text-xl font-bold text-slate-900">{{ $owner->properties->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Contrats actifs</p>
                        <p class="text-xl font-bold text-slate-900">{{ $owner->contracts->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="card-panel">
                <div class="card-panel-header">Actions</div>
                <div class="card-panel-body space-y-1">
                    <a href="{{ route('properties.create', ['owner_id' => $owner->id]) }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded transition-colors">Ajouter un bien</a>
                    <a href="{{ route('owners.edit', $owner) }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded transition-colors">Modifier</a>
                    <form id="form-delete-owner-{{ $owner->id }}" action="{{ route('owners.destroy', $owner) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" data-confirm="Supprimer ce proprietaire ?" data-confirm-form="form-delete-owner-{{ $owner->id }}" class="block w-full text-left px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 rounded transition-colors">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
