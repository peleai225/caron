@extends('layouts.app')

@section('title', 'Super Admin — Vue globale')
@section('page-title', 'Super Admin')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-slate-900">Bonjour, {{ auth()->user()->name }}</h2>
            <p class="text-sm text-slate-500 mt-0.5">Vue globale de toutes les agences</p>
        </div>
        <a href="{{ route('agencies.index') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Gérer les agences
        </a>
    </div>

    {{-- Stats globales --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Agences</span>
                <div class="w-8 h-8 rounded-lg bg-primary-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $global['total_agencies'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $global['active_agencies'] }} active(s)</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Biens</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $global['total_properties'] }}</p>
            <p class="text-xs text-slate-500 mt-1">Total plateforme</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Locataires</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ $global['total_tenants'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $global['total_contracts'] }} contrats actifs</p>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Revenus {{ now()->format('M Y') }}</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ number_format($global['monthly_revenue'], 0, ',', ' ') }}</p>
            <p class="text-xs text-slate-500 mt-1">FCFA encaissés</p>
        </div>
    </div>

    {{-- Alerte impayés --}}
    @if($global['overdue_count'] > 0)
    <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-lg">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <p class="text-sm text-red-700">
            <span class="font-semibold">{{ $global['overdue_count'] }} échéance(s) en retard</span>
            sur toute la plateforme —
            <span class="font-semibold">{{ number_format($global['overdue_amount'], 0, ',', ' ') }} FCFA</span>
        </p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Liste des agences --}}
        <div class="lg:col-span-2 card-panel overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-900">Agences</h3>
                <a href="{{ route('agencies.create') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">+ Nouvelle agence</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="px-4 py-2.5 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Agence</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Biens</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Locataires</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Contrats</th>
                            <th class="px-4 py-2.5 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($agencies as $agency)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($agency->logo_path)
                                        <img src="{{ asset('storage/'.$agency->logo_path) }}" class="w-7 h-7 rounded object-contain border border-slate-100" alt="">
                                    @else
                                        <div class="w-7 h-7 rounded bg-primary-100 flex items-center justify-center text-[11px] font-bold text-primary-700">{{ substr($agency->name, 0, 2) }}</div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-slate-900">{{ $agency->name }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $agency->city ?? $agency->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-slate-700">{{ $agency->properties_count }}</td>
                            <td class="px-4 py-3 text-right text-sm text-slate-700">{{ $agency->tenants_count }}</td>
                            <td class="px-4 py-3 text-right text-sm text-slate-700">{{ $agency->contracts_count }}</td>
                            <td class="px-4 py-3 text-right">
                                @if($agency->is_active)
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">Active</span>
                                @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-slate-100 text-slate-500 ring-slate-500/20">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-400">Aucune agence créée</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Derniers paiements --}}
        <div class="card-panel overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-900">Derniers paiements</h3>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($recentPayments as $payment)
                <div class="px-4 py-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-900 truncate">
                                {{ $payment->contract?->tenant?->full_name ?? '—' }}
                            </p>
                            <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                {{ $payment->contract?->agency?->name ?? '—' }}
                                · {{ $payment->payment_date?->format('d/m/Y') }}
                            </p>
                        </div>
                        <span class="text-sm font-semibold text-emerald-600 flex-shrink-0">
                            {{ number_format($payment->amount, 0, ',', ' ') }} F
                        </span>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-xs text-slate-400">Aucun paiement ce mois</div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
