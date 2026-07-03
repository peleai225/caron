@extends('layouts.app')

@section('title', $tenant->full_name)
@section('page-title', $tenant->full_name)

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">{{ $tenant->full_name }}</h2>
            <p class="page-subtitle">{{ $tenant->email ?? 'Aucun email' }} — {{ $tenant->phone }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tenants.edit', $tenant) }}" class="btn-primary">Modifier</a>
            <a href="{{ route('tenants.index') }}" class="btn-secondary">Retour</a>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-5">
            <!-- Informations -->
            <div class="card-panel">
                <div class="card-panel-header">Informations personnelles</div>
                <div class="card-panel-body grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate-500">Prenom</p>
                        <p class="text-sm font-medium text-slate-900 mt-0.5">{{ $tenant->first_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Nom</p>
                        <p class="text-sm font-medium text-slate-900 mt-0.5">{{ $tenant->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Email</p>
                        <p class="text-sm font-medium text-slate-900 mt-0.5">{{ $tenant->email ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Telephone</p>
                        <p class="text-sm font-medium text-slate-900 mt-0.5">{{ $tenant->phone }}</p>
                    </div>
                    @if($tenant->cni_number)
                    <div>
                        <p class="text-xs text-slate-500">Numero CNI</p>
                        <p class="text-sm font-medium text-slate-900 mt-0.5">{{ $tenant->cni_number }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-slate-500">Statut</p>
                        <div class="mt-1"><x-status-badge :status="$tenant->status" size="sm" /></div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            @if(($tenant->documents ?? collect())->count() > 0 || $tenant->cni_path)
            <div class="card-panel">
                <div class="card-panel-header">Documents</div>
                <div class="card-panel-body space-y-2">
                    @if($tenant->cni_path)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <div>
                                <p class="text-sm font-medium text-slate-900">Carte Nationale d'Identite</p>
                                <p class="text-xs text-slate-500">CNI</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $tenant->cni_path) }}" target="_blank" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                    </div>
                    @endif
                    @foreach($tenant->documents ?? [] as $document)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $document->name }}</p>
                                <p class="text-xs text-slate-500 capitalize">{{ $document->type }}</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $document->path) }}" target="_blank" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($tenant->notes)
            <div class="card-panel">
                <div class="card-panel-header">Notes</div>
                <div class="card-panel-body">
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $tenant->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Contracts -->
            @if($tenant->contracts->count() > 0)
            <div class="card-panel overflow-hidden">
                <div class="card-panel-header">Contrats</div>
                <div class="divide-y divide-slate-50">
                    @foreach($tenant->contracts as $contract)
                    <div class="px-5 py-3 hover:bg-slate-50/50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $contract->property->address ?? 'Bien non associe' }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $contract->start_date->format('d/m/Y') }} — {{ $contract->end_date->format('d/m/Y') }} — {{ number_format($contract->rent_amount, 0, ',', ' ') }} F/mois</p>
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
                <div class="card-panel-header">Actions</div>
                <div class="card-panel-body space-y-1">
                    <a href="{{ route('contracts.create', ['tenant_id' => $tenant->id]) }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded transition-colors">Creer un contrat</a>
                    <a href="{{ route('tenants.edit', $tenant) }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded transition-colors">Modifier</a>
                    <form id="form-delete-tenant-{{ $tenant->id }}" action="{{ route('tenants.destroy', $tenant) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" data-confirm="Supprimer ce locataire ?" data-confirm-form="form-delete-tenant-{{ $tenant->id }}" class="block w-full text-left px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-50 rounded transition-colors">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
