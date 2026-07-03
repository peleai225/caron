<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Gestion Immobilière') - {{ config('app.name', 'Caron') }}</title>

    @php $favicon = auth()->check() && auth()->user()->agency?->favicon_path ? asset('storage/' . auth()->user()->agency->favicon_path) : null; @endphp
    @if($favicon)
        <link rel="icon" type="image/png" href="{{ $favicon }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    @include('partials.assets')
</head>
<body class="h-screen w-screen overflow-hidden bg-slate-50 text-slate-900">
    <div class="flex h-screen w-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar">
            @php
                $_user = auth()->user();
                $_isGestionnaireOnly = $_user->hasRole('gestionnaire') && !$_user->hasAnyRole(['super_admin', 'admin_agence']);
            @endphp

            <!-- Logo -->
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    @if(auth()->user()->agency && auth()->user()->agency->logo_path)
                        <img src="{{ asset('storage/' . auth()->user()->agency->logo_path) }}" alt="{{ auth()->user()->agency->name }}" class="h-8 w-auto rounded-md">
                    @else
                        <div class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                    @endif
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-slate-900 leading-tight">{{ auth()->user()->agency?->name ?? 'Caron' }}</span>
                        <span class="text-xs text-slate-500">{{ $_isGestionnaireOnly ? 'Gestion' : 'Immobilier' }}</span>
                    </div>
                </a>
                <button id="sidebar-close" class="lg:hidden p-1.5 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Search -->
            <div class="px-4 pb-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="sidebar-search" placeholder="Rechercher..." class="w-full pl-9 pr-3 py-2 text-sm bg-slate-100 border-0 rounded-lg text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-primary-500/20">
                </div>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                @php
                    $user = auth()->user();
                    $isAdmin = $user->hasAnyRole(['super_admin', 'admin_agence', 'gestionnaire', 'charge_recouvrement', 'agent_immobilier']);
                    $isGestionnaireOnly = $user->hasRole('gestionnaire') && !$user->hasAnyRole(['super_admin', 'admin_agence']);
                    $isChargeRecouvrement = $user->hasRole('charge_recouvrement');
                    $isAgentImmobilier = $user->hasRole('agent_immobilier');
                    $isOwner = $user->hasRole('proprietaire');
                    $isAccountant = $user->hasRole('comptable');

                    $agencyId = $user->agency_id ?? null;
                    $sidebarCounters = $agencyId ? cache()->remember('sidebar_agency_' . $agencyId . '_counters', 45, function () use ($agencyId) {
                        return [
                            'pendingPayments' => \App\Models\Payment::whereHas('contract', function($q) use ($agencyId) {
                                $q->where('agency_id', $agencyId);
                            })->where('status', 'pending')->count(),
                            'overdueCount' => \App\Models\PaymentSchedule::whereHas('contract', function($q) use ($agencyId) {
                                $q->where('agency_id', $agencyId);
                            })->overdue()->count(),
                            'unpaidPenalties' => \App\Models\Penalty::whereHas('paymentSchedule.contract', function($q) use ($agencyId) {
                                $q->where('agency_id', $agencyId);
                            })->whereNull('paid_at')->count(),
                            'expiringContracts' => \App\Models\Contract::where('agency_id', $agencyId)
                                ->where('status', 'active')
                                ->whereBetween('end_date', [now(), now()->addDays(30)])
                                ->count(),
                        ];
                    }) : [
                        'pendingPayments' => 0,
                        'overdueCount' => 0,
                        'unpaidPenalties' => 0,
                        'expiringContracts' => 0,
                    ];
                    $pendingPayments = $sidebarCounters['pendingPayments'];
                    $overdueCount = $sidebarCounters['overdueCount'];
                    $unpaidPenalties = $sidebarCounters['unpaidPenalties'];
                    $expiringContracts = $sidebarCounters['expiringContracts'];
                @endphp

                <!-- Dashboard -->
                @if($isOwner)
                    <a href="{{ route('owner.dashboard') }}" class="nav-link {{ request()->routeIs('owner.*') ? 'active' : '' }}">
                @elseif($isAccountant)
                    <a href="{{ route('accountant.dashboard') }}" class="nav-link {{ request()->routeIs('accountant.*') ? 'active' : '' }}">
                @else
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                @endif
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Tableau de bord</span>
                </a>

                @if($isAdmin)
                    @if($isChargeRecouvrement)
                    <div class="nav-section-label">Gestion</div>
                    <a href="{{ route('rents.index') }}" class="nav-link {{ request()->routeIs('rents.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Loyers & Paiements</span>
                        @if($pendingPayments + $overdueCount > 0)<span class="nav-badge">{{ $pendingPayments + $overdueCount }}</span>@endif
                    </a>
                    <a href="{{ route('litiges.index') }}" class="nav-link {{ request()->routeIs('litiges.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                        <span>Litiges</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span>Rapports</span>
                    </a>
                    <div class="nav-section-label">Finance</div>
                    <a href="{{ route('penalties.index') }}" class="nav-link {{ request()->routeIs('penalties.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Penalites</span>
                        @if($unpaidPenalties > 0)<span class="nav-badge">{{ $unpaidPenalties }}</span>@endif
                    </a>
                    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Factures</span>
                    </a>

                    @elseif($isAgentImmobilier)
                    <div class="nav-section-label">Terrain</div>
                    <a href="{{ route('properties.index') }}" class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>Biens</span>
                    </a>
                    <a href="{{ route('tenants.index') }}" class="nav-link {{ request()->routeIs('tenants.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>Locataires</span>
                    </a>
                    <a href="{{ route('contracts.index') }}" class="nav-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Contrats</span>
                        @if($expiringContracts > 0)<span class="nav-badge">{{ $expiringContracts }}</span>@endif
                    </a>
                    <a href="{{ route('etat-des-lieux.index') }}" class="nav-link {{ request()->routeIs('etat-des-lieux.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span>États des lieux</span>
                    </a>
                    <a href="{{ route('rents.index') }}" class="nav-link {{ request()->routeIs('rents.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Loyers & Paiements</span>
                        @if($pendingPayments + $overdueCount > 0)<span class="nav-badge">{{ $pendingPayments + $overdueCount }}</span>@endif
                    </a>
                    <a href="{{ route('owners.index') }}" class="nav-link {{ request()->routeIs('owners.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Propriétaires</span>
                    </a>

                    @elseif($isGestionnaireOnly)
                    <div class="nav-section-label">Gestion</div>
                    <a href="{{ route('properties.index') }}" class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>Biens</span>
                    </a>
                    <a href="{{ route('tenants.index') }}" class="nav-link {{ request()->routeIs('tenants.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>Locataires</span>
                    </a>
                    <a href="{{ route('contracts.index') }}" class="nav-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Contrats</span>
                        @if($expiringContracts > 0)<span class="nav-badge">{{ $expiringContracts }}</span>@endif
                    </a>
                    <a href="{{ route('etat-des-lieux.index') }}" class="nav-link {{ request()->routeIs('etat-des-lieux.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span>Etats des lieux</span>
                    </a>
                    <a href="{{ route('rents.index') }}" class="nav-link {{ request()->routeIs('rents.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Loyers & Paiements</span>
                        @if($pendingPayments + $overdueCount > 0)<span class="nav-badge">{{ $pendingPayments + $overdueCount }}</span>@endif
                    </a>
                    <a href="{{ route('litiges.index') }}" class="nav-link {{ request()->routeIs('litiges.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                        <span>Litiges</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span>Rapports</span>
                    </a>
                    <a href="{{ route('document-templates.index') }}" class="nav-link {{ request()->routeIs('document-templates.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        <span>Documents</span>
                    </a>
                    <a href="{{ route('ocr.index') }}" class="nav-link {{ request()->routeIs('ocr.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>OCR</span>
                    </a>

                    <div class="nav-section-label">Finance</div>
                    <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <span>Depenses</span>
                    </a>
                    <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <span>Comptes</span>
                    </a>
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                        <span>Transactions</span>
                    </a>
                    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Factures</span>
                    </a>
                    <a href="{{ route('penalties.index') }}" class="nav-link {{ request()->routeIs('penalties.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Penalites</span>
                        @if($unpaidPenalties > 0)<span class="nav-badge">{{ $unpaidPenalties }}</span>@endif
                    </a>

                    @else
                    <!-- Admin complet -->
                    <div class="nav-section-label">Gestion</div>
                    <a href="{{ route('properties.index') }}" class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>Biens</span>
                    </a>
                    <a href="{{ route('tenants.index') }}" class="nav-link {{ request()->routeIs('tenants.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>Locataires</span>
                    </a>
                    <a href="{{ route('contracts.index') }}" class="nav-link {{ request()->routeIs('contracts.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Contrats</span>
                        @if($expiringContracts > 0)<span class="nav-badge">{{ $expiringContracts }}</span>@endif
                    </a>
                    <a href="{{ route('etat-des-lieux.index') }}" class="nav-link {{ request()->routeIs('etat-des-lieux.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span>Etats des lieux</span>
                    </a>
                    <a href="{{ route('rents.index') }}" class="nav-link {{ request()->routeIs('rents.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Loyers & Paiements</span>
                        @if($pendingPayments + $overdueCount > 0)<span class="nav-badge">{{ $pendingPayments + $overdueCount }}</span>@endif
                    </a>
                    <a href="{{ route('litiges.index') }}" class="nav-link {{ request()->routeIs('litiges.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                        <span>Litiges</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span>Rapports</span>
                    </a>
                    <a href="{{ route('document-templates.index') }}" class="nav-link {{ request()->routeIs('document-templates.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                        <span>Documents</span>
                    </a>
                    <a href="{{ route('ocr.index') }}" class="nav-link {{ request()->routeIs('ocr.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>OCR</span>
                    </a>

                    <div class="nav-section-label">Finance</div>
                    <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <span>Depenses</span>
                    </a>
                    <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <span>Comptes</span>
                    </a>
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                        <span>Transactions</span>
                    </a>
                    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Factures</span>
                    </a>
                    <a href="{{ route('penalties.index') }}" class="nav-link {{ request()->routeIs('penalties.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Penalites</span>
                        @if($unpaidPenalties > 0)<span class="nav-badge">{{ $unpaidPenalties }}</span>@endif
                    </a>

                    @if($user->hasAnyRole(['super_admin', 'admin_agence']))
                    <div class="nav-section-label">Administration</div>
                    <a href="{{ route('owners.index') }}" class="nav-link {{ request()->routeIs('owners.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Proprietaires</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>Utilisateurs</span>
                    </a>
                    <a href="{{ route('agencies.index') }}" class="nav-link {{ request()->routeIs('agencies.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>Agences</span>
                    </a>
                    <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Logs</span>
                    </a>
                    @endif
                    @endif

                @elseif($isOwner)
                    <div class="nav-section-label">Mes biens</div>
                    <a href="{{ route('owner.dashboard') }}" class="nav-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>Vue d'ensemble</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span>Rapports</span>
                    </a>

                @elseif($isAccountant)
                    <div class="nav-section-label">Finance</div>
                    <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <span>Depenses</span>
                    </a>
                    <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <span>Comptes</span>
                    </a>
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                        <span>Transactions</span>
                    </a>
                    <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Factures</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <span>Rapports</span>
                    </a>
                @endif
            </nav>

            <!-- User Footer -->
            <div class="sidebar-footer">
                <a href="{{ route('profile.index') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    @if(auth()->user()->avatar_path)
                        <img src="{{ asset('storage/' . auth()->user()->avatar_path) }}" alt="" class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-sm font-medium">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 truncate">{{ auth()->user()->name ?? 'Utilisateur' }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ auth()->user()->roles->first()?->name ?? '' }}</p>
                    </div>
                </a>
                <div class="flex items-center gap-1 mt-2 pt-2 border-t border-slate-100">
                    <a href="{{ route('settings.index') }}" class="flex-1 flex items-center justify-center gap-1.5 py-1.5 text-xs text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-md transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Parametres
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-1.5 py-1.5 text-xs text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Deconnexion
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Overlay mobile -->
        <div id="sidebar-overlay" class="sidebar-overlay"></div>

        <!-- Main -->
        <div class="flex-1 flex flex-col h-screen min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 lg:px-6 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <button id="sidebar-toggle" class="lg:hidden p-2 -ml-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-slate-900">@yield('page-title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-2">
                    @php
                        $navUnreadCount = auth()->user()->notifications()
                            ->where(function($q) { $q->where('is_read', false)->orWhereNull('read_at'); })
                            ->count();
                        $navNotifications = auth()->user()->notifications()
                            ->orderByDesc('created_at')
                            ->limit(5)
                            ->get();
                    @endphp

                    {{-- Dropdown notifications --}}
                    <div class="relative" id="notif-wrapper">
                        <button id="notif-btn" type="button" class="relative p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($navUnreadCount > 0)
                                <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-medium rounded-full flex items-center justify-center">{{ $navUnreadCount > 9 ? '9+' : $navUnreadCount }}</span>
                            @endif
                        </button>

                        <div id="notif-dropdown"
                            class="absolute right-0 mt-1 w-80 bg-white rounded-xl shadow-lg border border-slate-200 z-50 overflow-hidden hidden">

                            {{-- Header dropdown --}}
                            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                                <p class="text-sm font-semibold text-slate-900">Notifications</p>
                                @if($navUnreadCount > 0)
                                <form action="{{ route('notifications.read-all') }}" method="POST" class="inline">
                                    @csrf @method('PUT')
                                    <button type="submit" class="text-[11px] text-primary-600 hover:text-primary-700 font-medium">Tout marquer lu</button>
                                </form>
                                @endif
                            </div>

                            {{-- Liste --}}
                            <div class="divide-y divide-slate-50 max-h-80 overflow-y-auto">
                                @forelse($navNotifications as $notif)
                                <div class="px-4 py-3 {{ (!$notif->is_read || is_null($notif->read_at)) ? 'bg-primary-50/30' : '' }} hover:bg-slate-50 transition-colors">
                                    <div class="flex items-start gap-2.5">
                                        {{-- Icône selon type --}}
                                        <div class="flex-shrink-0 mt-0.5">
                                            @if($notif->type === 'payment_received')
                                                <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center">
                                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                            @elseif($notif->type === 'payment_overdue')
                                                <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center">
                                                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                            @elseif($notif->type === 'contract_expiring')
                                                <div class="w-7 h-7 rounded-full bg-amber-100 flex items-center justify-center">
                                                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                            @else
                                                <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-slate-900 truncate">{{ $notif->title }}</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{{ $notif->message }}</p>
                                            <p class="text-[10px] text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(!$notif->is_read || is_null($notif->read_at))
                                            <span class="w-2 h-2 bg-primary-500 rounded-full flex-shrink-0 mt-1.5"></span>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="px-4 py-6 text-center text-xs text-slate-400">Aucune notification</div>
                                @endforelse
                            </div>

                            {{-- Footer --}}
                            <div class="px-4 py-2.5 border-t border-slate-100 bg-slate-50/60">
                                <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">
                                    Voir toutes les notifications →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6 bg-slate-50">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast container -->
    <div id="toast-container" class="fixed top-4 right-4 z-[60] flex flex-col gap-2 pointer-events-none">
    </div>

    <!-- Confirm modal -->
    <div id="confirm-modal" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
        <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
        <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-sm bg-white rounded-xl shadow-xl p-6 transform scale-95 transition-transform duration-200">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-slate-900 mb-1">Confirmer l'action</h3>
                    <p id="confirm-modal-message" class="text-xs text-slate-500 leading-relaxed">Êtes-vous sûr de vouloir effectuer cette action ?</p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-5">
                <button type="button" data-modal-close="confirm-modal" class="btn-secondary">Annuler</button>
                <button type="button" id="confirm-modal-submit" class="btn-danger">Confirmer</button>
            </div>
        </div>
    </div>

    <!-- Quick action modal -->
    <div id="quick-action-modal" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
        <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
        <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 id="quick-action-modal-title" class="text-sm font-semibold text-slate-900">Action</h3>
                <button type="button" data-modal-close="quick-action-modal" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="quick-action-modal-body" class="px-5 py-4"></div>
            <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-slate-100">
                <button type="button" data-modal-close="quick-action-modal" class="btn-secondary">Annuler</button>
                <button type="button" id="quick-action-modal-submit" class="btn-primary">Confirmer</button>
            </div>
        </div>
    </div>

    <!-- Lightbox modal -->
    <div id="lightbox-modal" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
        <div class="modal-backdrop absolute inset-0 bg-black/80"></div>
        <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 max-w-4xl w-full px-4">
            <img id="lightbox-img" src="" alt="" class="max-h-[85vh] w-auto mx-auto rounded-lg shadow-2xl">
            <button type="button" data-modal-close="lightbox-modal" class="absolute top-2 right-6 p-2 rounded-full bg-black/50 text-white hover:bg-black/70 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <!-- Flash messages → toast -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                window.Toast && Toast.success(@json(session('success')));
            @endif
            @if(session('error'))
                window.Toast && Toast.error(@json(session('error')));
            @endif
            @if(session('warning'))
                window.Toast && Toast.warning(@json(session('warning')));
            @endif
            @if(session('info'))
                window.Toast && Toast.info(@json(session('info')));
            @endif
            @if($errors->any())
                window.Toast && Toast.error('Veuillez corriger les erreurs dans le formulaire.');
            @endif
        });
    </script>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const sidebarSearch = document.getElementById('sidebar-search');

        function openSidebar() {
            sidebar.classList.add('open');
            sidebarOverlay.classList.add('open');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('open');
        }

        if (sidebarToggle) sidebarToggle.addEventListener('click', openSidebar);
        if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

        if (sidebarSearch) {
            sidebarSearch.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.nav-link').forEach(item => {
                    item.style.display = item.textContent.toLowerCase().includes(term) || term === '' ? '' : 'none';
                });
            });
        }
    </script>

    {{-- Dropdown notifications --}}
    <script>
    (function() {
        var btn = document.getElementById('notif-btn');
        var dropdown = document.getElementById('notif-dropdown');
        if (!btn || !dropdown) return;

        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!document.getElementById('notif-wrapper').contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    })();
    </script>
</body>
</html>
