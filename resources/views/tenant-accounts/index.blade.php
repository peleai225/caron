@extends('layouts.app')

@section('title', 'Comptes Locataires')
@section('page-title', 'Gestion des Comptes Locataires')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Comptes Locataires</h2>
            <p class="page-subtitle">Gestion des accès locataires</p>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500 mb-1">Total Locataires</p>
            <p class="text-xl font-bold text-slate-900">{{ $stats['total'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500 mb-1">Avec Compte</p>
            <p class="text-xl font-bold text-emerald-600">{{ $stats['with_account'] }}</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500 mb-1">Sans Compte</p>
            <p class="text-xl font-bold text-amber-600">{{ $stats['without_account'] }}</p>
        </div>
    </div>

    @if($stats['without_account'] > 0)
    <div class="bg-primary-50 border border-primary-200 rounded-lg px-5 py-4 flex items-center justify-between">
        <div>
            <p class="text-xs font-semibold text-primary-800">Créer des comptes en masse</p>
            <p class="text-[11px] text-primary-700 mt-0.5">{{ $stats['without_account'] }} locataire(s) sans compte peuvent recevoir un accès.</p>
        </div>
        <form action="{{ route('tenant-accounts.create-all') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn-primary">Créer tous les comptes</button>
        </form>
    </div>
    @endif

    <div class="card-panel">
        <div class="card-panel-header">Liste des Locataires</div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Locataire</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Téléphone</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Statut</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Compte</th>
                        <th class="px-5 py-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                                    <span class="text-[11px] font-bold text-primary-700">{{ substr($tenant->first_name, 0, 1) }}{{ substr($tenant->last_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-900">{{ $tenant->full_name }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $tenant->cni_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <p class="text-xs text-slate-900">{{ $tenant->email ?? 'N/A' }}</p>
                            @if($tenant->user)
                            <p class="text-[11px] text-emerald-600">Compte: {{ $tenant->user->email }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-slate-700">{{ $tenant->phone }}</td>
                        <td class="px-5 py-3">
                            @php
                                $statusColors = [
                                    'actif' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                    'en_retard' => 'bg-red-50 text-red-700 ring-red-600/20',
                                ];
                                $sColor = $statusColors[$tenant->status] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md ring-1 ring-inset {{ $sColor }}">
                                {{ ucfirst(str_replace('_', ' ', $tenant->status)) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            @if($tenant->user)
                            <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Actif</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">Aucun</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                @if($tenant->user)
                                <form action="{{ route('tenant-accounts.reset-password', $tenant) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[11px] text-primary-600 hover:text-primary-700 font-medium" title="Réinitialiser le mot de passe">Réinitialiser</button>
                                </form>
                                <form id="form-deactivate-account-{{ $tenant->id }}" action="{{ route('tenant-accounts.deactivate', $tenant) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" data-confirm="Désactiver ce compte locataire ?" data-confirm-form="form-deactivate-account-{{ $tenant->id }}" class="text-[11px] text-red-600 hover:text-red-700 font-medium">Désactiver</button>
                                </form>
                                @else
                                    @if($tenant->email)
                                    <form action="{{ route('tenant-accounts.create', $tenant) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn-primary text-[11px] py-1 px-2.5">Créer compte</button>
                                    </form>
                                    @else
                                    <span class="text-[11px] text-slate-400">Pas d'email</span>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="empty-state-icon mx-auto mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-slate-900">Aucun locataire trouvé</p>
                            <p class="text-xs text-slate-500 mt-1">Créez d'abord des locataires dans le module Locataires</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tenants->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $tenants->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
