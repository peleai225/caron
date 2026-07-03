@extends('layouts.app')

@section('title', $agency->name)
@section('page-title', $agency->name)

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div class="flex items-center gap-3">
            @if($agency->logo_path)
                <img src="{{ asset('storage/' . $agency->logo_path) }}" alt="{{ $agency->name }}" class="w-12 h-12 rounded-lg object-cover" loading="lazy">
            @else
                <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                    <span class="text-primary-700 font-bold text-lg">{{ substr($agency->name, 0, 1) }}</span>
                </div>
            @endif
            <div>
                <h2 class="page-title-main">{{ $agency->name }}</h2>
                <div class="flex items-center gap-2 mt-0.5">
                    @if($agency->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Active</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-slate-50 text-slate-600 ring-1 ring-inset ring-slate-500/20">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('agencies.edit', $agency) }}" class="btn-primary">Modifier</a>
            <a href="{{ route('agencies.index') }}" class="btn-secondary">Retour</a>
        </div>
    </header>

    @if($agency->description)
    <div class="card-panel">
        <div class="card-panel-body">
            <p class="text-sm text-slate-700">{{ $agency->description }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Contact -->
        <div class="card-panel">
            <div class="card-panel-header">Contact</div>
            <div class="card-panel-body space-y-2.5">
                @if($agency->email)
                <div class="flex items-center gap-2 text-xs text-slate-700">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    {{ $agency->email }}
                </div>
                @endif
                @if($agency->phone)
                <div class="flex items-center gap-2 text-xs text-slate-700">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    {{ $agency->phone }}
                </div>
                @endif
                @if($agency->address)
                <div class="flex items-center gap-2 text-xs text-slate-700">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $agency->address }}, {{ $agency->city }}, {{ $agency->country }}
                </div>
                @endif
                @if($agency->website)
                <div class="flex items-center gap-2 text-xs text-slate-700">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    <a href="{{ $agency->website }}" target="_blank" class="text-primary-600 hover:text-primary-700">{{ $agency->website }}</a>
                </div>
                @endif
            </div>
        </div>

        <!-- Stats -->
        <div class="card-panel">
            <div class="card-panel-header">Statistiques</div>
            <div class="card-panel-body">
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <p class="text-xl font-bold text-slate-900">{{ $agency->users_count }}</p>
                        <p class="text-[11px] text-slate-500">Utilisateurs</p>
                    </div>
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <p class="text-xl font-bold text-slate-900">{{ $agency->properties_count }}</p>
                        <p class="text-[11px] text-slate-500">Biens</p>
                    </div>
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <p class="text-xl font-bold text-slate-900">{{ $agency->tenants_count }}</p>
                        <p class="text-[11px] text-slate-500">Locataires</p>
                    </div>
                    <div class="text-center p-3 bg-slate-50 rounded-lg">
                        <p class="text-xl font-bold text-slate-900">{{ $agency->contracts_count }}</p>
                        <p class="text-[11px] text-slate-500">Contrats</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
