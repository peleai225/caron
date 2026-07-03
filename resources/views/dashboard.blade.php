@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
@php
    $user = auth()->user();
    $isGestionnaireOnly = $user->hasRole('gestionnaire') && !$user->hasAnyRole(['super_admin', 'admin_agence']);
@endphp

<div class="space-y-6">
    <!-- Welcome -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">
                Bonjour, {{ $user->name }}
            </h2>
            <p class="text-sm text-slate-500 mt-0.5">Vue d'ensemble de votre activite immobiliere</p>
        </div>
        <div class="hidden sm:flex items-center gap-2">
            <button type="button" data-modal-open="modal-quick-payment" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Encaisser un paiement
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Proprietaires</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $stats['total_owners'] ?? 0 }}</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Biens</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $stats['total_properties'] ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $stats['available_properties'] ?? 0 }} disponible(s)</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Locataires</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $stats['active_tenants'] ?? 0 }}</p>
            <p class="text-xs text-emerald-600 mt-1">Actifs</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Revenus du mois</span>
                <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['monthly_revenue'] ?? 0, 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>
    </div>

    <!-- Second row stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Impayes</span>
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['overdue_amount'] ?? 0, 0, ',', ' ') }}</p>
            <p class="text-xs text-red-500 mt-1">{{ $stats['overdue_count'] ?? 0 }} echeance(s)</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Depenses</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_expenses'] ?? 0, 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA ce mois</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Commissions</span>
                <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['agency_commissions'] ?? 0, 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Disponibles</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $stats['available_properties'] ?? 0 }}</p>
            <p class="text-xs text-slate-500 mt-1">Biens a louer</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card-panel">
        <div class="card-panel-header">Actions rapides</div>
        <div class="card-panel-body">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <a href="{{ route('properties.create') }}" class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-primary-200 hover:bg-primary-50/50 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-primary-100 flex items-center justify-center group-hover:bg-primary-200 transition-colors">
                        <svg class="w-4.5 h-4.5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-900">Ajouter un bien</p>
                        <p class="text-xs text-slate-500">Nouveau bien</p>
                    </div>
                </a>

                <a href="{{ route('tenants.create') }}" class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-emerald-200 hover:bg-emerald-50/50 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                        <svg class="w-4.5 h-4.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-900">Ajouter locataire</p>
                        <p class="text-xs text-slate-500">Nouveau locataire</p>
                    </div>
                </a>

                <a href="{{ route('contracts.create') }}" class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-violet-200 hover:bg-violet-50/50 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center group-hover:bg-violet-200 transition-colors">
                        <svg class="w-4.5 h-4.5 text-violet-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-900">Creer un contrat</p>
                        <p class="text-xs text-slate-500">Bail de location</p>
                    </div>
                </a>

                <a href="{{ route('litiges.create') }}" class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-amber-200 hover:bg-amber-50/50 transition-colors group">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                        <svg class="w-4.5 h-4.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-900">Signaler un litige</p>
                        <p class="text-xs text-slate-500">Nouveau dossier</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Two columns: Recent Payments + Expiring Contracts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payments -->
        <div class="card-panel">
            <div class="card-panel-header flex items-center justify-between">
                <span>Paiements recents</span>
                <a href="{{ route('rents.index') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir tout</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($recentPayments ?? [] as $payment)
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $payment->contract->tenant ? trim($payment->contract->tenant->first_name . ' ' . $payment->contract->tenant->last_name) : '—' }}</p>
                                <p class="text-xs text-slate-500">{{ $payment->payment_date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-slate-900">{{ number_format($payment->amount, 0, ',', ' ') }} F</p>
                            <x-status-badge :status="$payment->status" size="sm" />
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <div class="empty-state-icon">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-sm text-slate-500">Aucun paiement recent</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Expiring Contracts -->
        <div class="card-panel">
            <div class="card-panel-header flex items-center justify-between">
                <span>Contrats expirant bientot</span>
                <a href="{{ route('contracts.index') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir tout</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($expiringContracts ?? [] as $contract)
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $contract->contract_number }}</p>
                                <p class="text-xs text-slate-500">Expire le {{ $contract->end_date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <x-status-badge :status="$contract->status" size="sm" />
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <div class="empty-state-icon">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-sm text-slate-500">Aucun contrat expirant bientot</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Properties section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card-panel">
            <div class="card-panel-header flex items-center justify-between">
                <span>Biens occupes</span>
                <a href="{{ route('properties.index', ['status' => 'occupe']) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir tout</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($activeProperties ?? [] as $prop)
                    <a href="{{ route('properties.show', $prop) }}" class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition-colors">
                        <span class="text-sm font-medium text-slate-700">{{ $prop->address ?? $prop->city }}</span>
                        <span class="text-xs text-slate-500">{{ $prop->owner?->name ?? '—' }}</span>
                    </a>
                @empty
                    <div class="px-5 py-6 text-center">
                        <p class="text-sm text-slate-500">Aucun bien occupe</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="card-panel">
            <div class="card-panel-header flex items-center justify-between">
                <span>Biens disponibles</span>
                <a href="{{ route('properties.index', ['status' => 'libre']) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir tout</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($availableProperties ?? [] as $prop)
                    <a href="{{ route('properties.show', $prop) }}" class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition-colors">
                        <span class="text-sm font-medium text-slate-700">{{ $prop->address ?? $prop->city }}</span>
                        <span class="text-xs text-slate-500">{{ $prop->owner?->name ?? '—' }}</span>
                    </a>
                @empty
                    <div class="px-5 py-6 text-center">
                        <p class="text-sm text-slate-500">Aucun bien disponible</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Modal : Encaisser un paiement --}}
<div id="modal-quick-payment" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-xl bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">

        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Encaisser un paiement</h3>
            <button type="button" data-modal-close="modal-quick-payment" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('rents.store') }}" class="divide-y divide-slate-100">
            @csrf
            <input type="hidden" name="redirect_to" value="dashboard">

            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Contrat <span class="text-red-500">*</span></label>
                    <select id="qp-contract-id" name="contract_id" required class="input-modern searchable-select">
                        <option value="">Sélectionner un contrat actif...</option>
                        @foreach($contracts ?? [] as $c)
                            <option value="{{ $c->id }}" data-rent="{{ $c->rent_amount }}">
                                {{ $c->property?->address ?? 'Bien #'.$c->property_id }} — {{ $c->tenant?->full_name ?? 'Locataire' }} ({{ number_format($c->rent_amount, 0, ',', ' ') }} F)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" id="qp-amount" name="amount" required min="0" step="1" class="input-modern" placeholder="150000">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Méthode <span class="text-red-500">*</span></label>
                        <select name="payment_method" id="qp-method" required class="input-modern">
                            <option value="">Choisir...</option>
                            <option value="cash">Espèces</option>
                            <option value="moneyfusion">MoneyFusion (Wave / OM / MTN)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Période <span class="text-red-500">*</span></label>
                        <input type="month" name="period" required value="{{ date('Y-m') }}" class="input-modern">
                    </div>
                </div>

                {{-- Champ téléphone — affiché uniquement pour MoneyFusion --}}
                <div id="qp-phone-wrap" class="hidden">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone du client <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" id="qp-phone" class="input-modern" placeholder="+225 07 XX XX XX XX">
                    <p class="mt-1 text-[11px] text-slate-400">Numéro qui recevra la demande de paiement (Wave, Orange Money ou MTN)</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Référence</label>
                    <input type="text" name="reference" class="input-modern" placeholder="N° de transaction (optionnel)">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <button type="button" data-modal-close="modal-quick-payment" class="btn-secondary">Annuler</button>
                <button type="submit" id="qp-submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var contractSel = document.getElementById('qp-contract-id');
    var amountInput  = document.getElementById('qp-amount');
    var methodSel    = document.getElementById('qp-method');
    var phoneWrap    = document.getElementById('qp-phone-wrap');
    var phoneInput   = document.getElementById('qp-phone');
    var submitBtn    = document.getElementById('qp-submit');

    if (contractSel && amountInput) {
        contractSel.addEventListener('change', function () {
            var opt = contractSel.options[contractSel.selectedIndex];
            if (opt && opt.dataset.rent) {
                amountInput.value = parseInt(opt.dataset.rent, 10);
            }
        });
    }

    if (methodSel && phoneWrap) {
        methodSel.addEventListener('change', function () {
            var isMF = this.value === 'moneyfusion';
            phoneWrap.classList.toggle('hidden', !isMF);
            if (phoneInput) phoneInput.required = isMF;
            if (submitBtn) {
                submitBtn.innerHTML = isMF
                    ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Payer via Fusion Pay'
                    : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Enregistrer';
            }
        });
    }
});
</script>
@endsection
