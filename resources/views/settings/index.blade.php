@extends('layouts.app')

@section('title', 'Paramètres')
@section('page-title', 'Paramètres')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Paramètres</h2>
            <p class="page-subtitle">Gérez les paramètres de votre agence et de l'application</p>
        </div>
    </header>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        {{ session('error') }}
    </div>
    @endif

    <div class="card-panel">
        <div class="border-b border-slate-100">
            <nav class="flex -mb-px overflow-x-auto">
                <button onclick="showTab('agency')" id="tab-agency" class="tab-btn px-5 py-3 text-xs font-semibold whitespace-nowrap border-b-2 border-primary-600 text-primary-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Agence
                </button>
                <button onclick="showTab('application')" id="tab-application" class="tab-btn px-5 py-3 text-xs font-semibold whitespace-nowrap border-b-2 border-transparent text-slate-500 hover:text-primary-700 hover:border-primary-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Application
                </button>
                <button onclick="showTab('notifications')" id="tab-notifications" class="tab-btn px-5 py-3 text-xs font-semibold whitespace-nowrap border-b-2 border-transparent text-slate-500 hover:text-primary-700 hover:border-primary-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Notifications
                </button>
                @if(auth()->user()->hasAnyRole(['super_admin', 'admin_agence']))
                <button onclick="showTab('payment')" id="tab-payment" class="tab-btn px-5 py-3 text-xs font-semibold whitespace-nowrap border-b-2 border-transparent text-slate-500 hover:text-primary-700 hover:border-primary-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Paiement
                </button>
                <button onclick="showTab('email')" id="tab-email" class="tab-btn px-5 py-3 text-xs font-semibold whitespace-nowrap border-b-2 border-transparent text-slate-500 hover:text-primary-700 hover:border-primary-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Email
                </button>
                <button onclick="showTab('canaux')" id="tab-canaux" class="tab-btn px-5 py-3 text-xs font-semibold whitespace-nowrap border-b-2 border-transparent text-slate-500 hover:text-primary-700 hover:border-primary-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Canaux
                </button>
                @endif
            </nav>
        </div>

        <div id="content-agency" class="tab-content p-5">
            @if($agency)
            <form action="{{ route('settings.agency') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="bg-slate-50 rounded-lg p-5">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Identité visuelle</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="block text-xs font-medium text-slate-600">Logo de l'agence</label>
                            <div class="flex items-start gap-4">
                                @if($agency->logo_path)
                                    <img src="{{ asset('storage/' . $agency->logo_path) }}" alt="Logo" class="w-16 h-16 object-contain rounded-lg border border-slate-200 bg-white" loading="lazy">
                                @else
                                    <div class="w-16 h-16 bg-slate-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div>
                                    <input type="file" name="logo" id="logo" accept="image/*" onchange="previewSingleImage(this, 'logo-preview')" class="text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-primary-50 file:text-primary-600 file:font-medium hover:file:bg-primary-100">
                                    <p class="text-[11px] text-slate-400 mt-1">PNG, JPG — 2MB max</p>
                                    <div id="logo-preview" class="mt-2"></div>
                                    @if($agency->logo_path)
                                    <button type="button" onclick="document.getElementById('delete-logo-form').submit()" class="text-[11px] text-red-600 hover:text-red-700 font-medium mt-1">Supprimer</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="block text-xs font-medium text-slate-600">Favicon</label>
                            <div class="flex items-start gap-4">
                                @if(optional($agency)->favicon_path)
                                    <img src="{{ asset('storage/' . $agency->favicon_path) }}" alt="Favicon" class="w-10 h-10 object-contain rounded-md border border-slate-200 bg-white" loading="lazy">
                                @else
                                    <div class="w-10 h-10 bg-slate-200 rounded-md flex items-center justify-center">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                    </div>
                                @endif
                                <div>
                                    <input type="file" name="favicon" id="favicon" accept="image/*,.ico" onchange="previewSingleImage(this, 'favicon-preview')" class="text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-primary-50 file:text-primary-600 file:font-medium hover:file:bg-primary-100">
                                    <p class="text-[11px] text-slate-400 mt-1">PNG, ICO — 512 Ko max</p>
                                    <div id="favicon-preview" class="mt-2"></div>
                                    @if(optional($agency)->favicon_path)
                                    <button type="button" onclick="document.getElementById('delete-favicon-form').submit()" class="text-[11px] text-red-600 hover:text-red-700 font-medium mt-1">Supprimer</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Informations principales</h3>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $agency->name) }}" required class="input-modern">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $agency->email) }}" required class="input-modern">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone</label>
                            <input type="text" name="phone" value="{{ old('phone', $agency->phone) }}" class="input-modern">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Site web</label>
                            <input type="url" name="website" value="{{ old('website', $agency->website) }}" class="input-modern" placeholder="https://">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Localisation</h3>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Adresse</label>
                            <textarea name="address" rows="2" class="input-modern">{{ old('address', $agency->address) }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Ville</label>
                                <input type="text" name="city" value="{{ old('city', $agency->city) }}" class="input-modern">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Pays</label>
                                <input type="text" name="country" value="{{ old('country', $agency->country) }}" class="input-modern">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Description</label>
                            <textarea name="description" rows="3" class="input-modern">{{ old('description', $agency->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="submit" class="btn-primary">Enregistrer les paramètres</button>
                </div>
            </form>
            @else
            <p class="text-sm text-slate-600">Aucune agence associée à votre compte.</p>
            @endif
        </div>

        <div id="content-application" class="tab-content p-5 hidden">
            <form action="{{ route('settings.application') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                @php $agencyId = $agency ? $agency->id : null; @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom de l'application</label>
                        <input type="text" name="app_name" value="{{ old('app_name', \App\Models\Setting::get('app_name', config('app.name'), $agencyId)) }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Email de contact</label>
                        <input type="email" name="app_email" value="{{ old('app_email', \App\Models\Setting::get('app_email', null, $agencyId)) }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone de contact</label>
                        <input type="text" name="app_phone" value="{{ old('app_phone', \App\Models\Setting::get('app_phone', null, $agencyId)) }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Devise</label>
                        <input type="text" name="currency" value="{{ old('currency', \App\Models\Setting::get('currency', 'FCFA', $agencyId)) }}" class="input-modern">
                    </div>
                </div>
                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="submit" class="btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>

        @if(auth()->user()->hasAnyRole(['super_admin', 'admin_agence']))
        <div id="content-payment" class="tab-content p-5 hidden">
            <div class="max-w-2xl space-y-6">
                <div class="flex items-start gap-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs text-amber-800 space-y-1">
                        <p class="font-semibold">Comment obtenir votre URL API MoneyFusion ?</p>
                        <ol class="list-decimal list-inside space-y-0.5 text-amber-700">
                            <li>Connectez-vous sur <span class="font-medium">app.moneyfusion.net</span></li>
                            <li>Cliquez sur <span class="font-medium">« API de paiement »</span> dans le menu gauche</li>
                            <li>Créez une application en renseignant l'URL de votre site</li>
                            <li>Copiez l'URL API générée ci-dessous</li>
                            <li>Enregistrez l'adresse IP de votre serveur dans l'application</li>
                        </ol>
                    </div>
                </div>

                {{-- MoneyFusion --}}
                <form action="{{ route('settings.payment-gateway') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    @php $agencyId = auth()->user()->hasRole('super_admin') ? null : optional(auth()->user()->agency)->id; @endphp

                    <div class="bg-slate-50 rounded-lg p-5 space-y-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900">Fusion Pay — MoneyFusion</h3>
                                <p class="text-[11px] text-slate-500">Acceptez Wave, Orange Money, MTN Money</p>
                            </div>
                            @php
                                $mfUrl = \App\Models\Setting::get('moneyfusion_api_url', null, $agencyId);
                                $mfKey = \App\Models\Setting::get('moneyfusion_api_key', null, $agencyId);
                                $mfConfigured = $mfUrl && $mfKey;
                            @endphp
                            @if($mfConfigured)
                                <span class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Configuré
                                </span>
                            @else
                                <span class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-500/20">
                                    Non configuré
                                </span>
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Clé API (API Key)</label>
                            @php $mfKey = \App\Models\Setting::get('moneyfusion_api_key', null, $agencyId); @endphp
                            <input type="text"
                                name="moneyfusion_api_key"
                                value="{{ old('moneyfusion_api_key', $mfKey) }}"
                                class="input-modern font-mono text-xs"
                                placeholder="Votre clé API MoneyFusion">
                            <p class="mt-1 text-[11px] text-slate-400">Clé fournie par MoneyFusion dans votre tableau de bord.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">URL API Fusion Pay</label>
                            <input type="url"
                                name="moneyfusion_api_url"
                                value="{{ old('moneyfusion_api_url', $mfUrl) }}"
                                class="input-modern font-mono text-xs"
                                placeholder="https://www.pay.moneyfusion.net/xxxxxxxxxxxxxxxx/pay/">
                            <p class="mt-1 text-[11px] text-slate-400">L'URL complète générée lors de la création de votre application (contient votre identifiant unique).</p>
                        </div>

                        @if($mfConfigured)
                        <div class="pt-3 border-t border-slate-200">
                            <p class="text-[11px] text-slate-500 mb-2">URL de webhook à enregistrer dans MoneyFusion :</p>
                            <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-md px-3 py-2">
                                <code class="text-xs text-slate-700 flex-1 break-all">{{ route('moneyfusion.webhook') }}</code>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ route('moneyfusion.webhook') }}'); this.textContent='Copié !'; setTimeout(() => this.textContent='Copier', 2000)" class="text-[11px] text-primary-600 font-medium hover:text-primary-700 flex-shrink-0">Copier</button>
                            </div>
                            <p class="mt-1 text-[11px] text-slate-400">Collez cette URL dans le champ <strong>webhook_url</strong> de votre application MoneyFusion.</p>
                        </div>
                        @endif
                    </div>

                    <div class="flex justify-end pt-2 border-t border-slate-100">
                        <button type="submit" class="btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Onglet Email --}}
        @if(auth()->user()->hasAnyRole(['super_admin', 'admin_agence']))
        <div id="content-email" class="tab-content p-5 hidden">
            @php
                $mailMailer = config('mail.default');
                $mailHost   = config('mail.mailers.smtp.host');
                $mailFrom   = config('mail.from.address');
                $isConfigured = $mailMailer !== 'log' && !empty($mailHost) && $mailHost !== '127.0.0.1';
            @endphp
            <div class="max-w-2xl space-y-6">

                {{-- Statut actuel --}}
                <div class="flex items-center gap-4 p-4 rounded-xl border {{ $isConfigured ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200' }}">
                    <div class="w-9 h-9 rounded-lg {{ $isConfigured ? 'bg-emerald-100' : 'bg-amber-100' }} flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 {{ $isConfigured ? 'text-emerald-700' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold {{ $isConfigured ? 'text-emerald-800' : 'text-amber-800' }}">
                            {{ $isConfigured ? 'Email opérationnel' : 'Email non configuré (mode log)' }}
                        </p>
                        <p class="text-xs {{ $isConfigured ? 'text-emerald-600' : 'text-amber-600' }} mt-0.5">
                            @if($isConfigured)
                                Serveur : <strong>{{ $mailHost }}</strong> · Expéditeur : <strong>{{ $mailFrom }}</strong>
                            @else
                                Les emails ne sont pas envoyés. Remplissez le formulaire ci-dessous pour activer l'envoi.
                            @endif
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold ring-1 ring-inset {{ $isConfigured ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }} flex-shrink-0">
                        {{ $isConfigured ? 'Actif' : 'Inactif' }}
                    </span>
                </div>

                {{-- Formulaire de configuration SMTP --}}
                <form action="{{ route('settings.email') }}" method="POST" class="rounded-xl border border-slate-200 overflow-hidden">
                    @csrf
                    @method('PUT')
                    <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-semibold text-slate-900">Configuration du serveur d'envoi</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Ces paramètres sont sauvegardés en base de données et appliqués immédiatement.</p>
                    </div>
                    <div class="p-5 space-y-5">

                        {{-- Driver --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-2">Driver d'envoi <span class="text-red-500">*</span></label>
                            <div class="flex gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="mail_mailer" value="smtp"
                                        {{ old('mail_mailer', config('mail.default', 'smtp')) === 'smtp' ? 'checked' : '' }}
                                        class="text-primary-600">
                                    <span class="text-sm font-medium text-slate-700">SMTP</span>
                                    <span class="text-[11px] text-slate-400">(recommandé)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="mail_mailer" value="log"
                                        {{ old('mail_mailer', config('mail.default')) === 'log' ? 'checked' : '' }}
                                        class="text-primary-600">
                                    <span class="text-sm font-medium text-slate-700">Log</span>
                                    <span class="text-[11px] text-slate-400">(développement uniquement)</span>
                                </label>
                            </div>
                        </div>

                        {{-- Host + Port --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Hôte SMTP <span class="text-red-500">*</span></label>
                                <input type="text" name="mail_host"
                                    value="{{ old('mail_host', config('mail.mailers.smtp.host')) }}"
                                    placeholder="smtp-relay.brevo.com"
                                    class="input-modern @error('mail_host') border-red-300 @enderror">
                                @error('mail_host')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Port <span class="text-red-500">*</span></label>
                                <select name="mail_port" class="input-modern @error('mail_port') border-red-300 @enderror">
                                    @foreach([587 => '587 (TLS)', 465 => '465 (SSL)', 25 => '25', 2525 => '2525'] as $val => $label)
                                        <option value="{{ $val }}" {{ (int) old('mail_port', config('mail.mailers.smtp.port', 587)) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('mail_port')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Chiffrement --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Chiffrement</label>
                            <select name="mail_encryption" class="input-modern max-w-xs">
                                <option value="tls" {{ old('mail_encryption', config('mail.mailers.smtp.encryption', 'tls')) === 'tls' ? 'selected' : '' }}>TLS (recommandé)</option>
                                <option value="ssl" {{ old('mail_encryption', config('mail.mailers.smtp.encryption')) === 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="starttls" {{ old('mail_encryption', config('mail.mailers.smtp.encryption')) === 'starttls' ? 'selected' : '' }}>STARTTLS</option>
                                <option value="" {{ old('mail_encryption', config('mail.mailers.smtp.encryption')) === '' ? 'selected' : '' }}>Aucun</option>
                            </select>
                        </div>

                        {{-- Identifiants SMTP --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nom d'utilisateur SMTP <span class="text-red-500">*</span></label>
                                <input type="text" name="mail_username"
                                    value="{{ old('mail_username', config('mail.mailers.smtp.username')) }}"
                                    placeholder="votre@email.com"
                                    autocomplete="off"
                                    class="input-modern @error('mail_username') border-red-300 @enderror">
                                @error('mail_username')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Mot de passe / Clé API</label>
                                <input type="password" name="mail_password"
                                    placeholder="{{ $isConfigured ? '••••••••••• (laisser vide pour garder)' : 'Votre clé API SMTP' }}"
                                    autocomplete="new-password"
                                    class="input-modern">
                                <p class="text-[11px] text-slate-400 mt-1">{{ $isConfigured ? 'Laisser vide pour conserver le mot de passe actuel.' : 'Clé API ou mot de passe de votre fournisseur SMTP.' }}</p>
                            </div>
                        </div>

                        <hr class="border-slate-100">

                        {{-- Expéditeur --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Adresse expéditeur <span class="text-red-500">*</span></label>
                                <input type="email" name="mail_from_address"
                                    value="{{ old('mail_from_address', config('mail.from.address')) }}"
                                    placeholder="no-reply@votre-domaine.com"
                                    class="input-modern @error('mail_from_address') border-red-300 @enderror">
                                @error('mail_from_address')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nom expéditeur <span class="text-red-500">*</span></label>
                                <input type="text" name="mail_from_name"
                                    value="{{ old('mail_from_name', config('mail.from.name')) }}"
                                    placeholder="{{ config('app.name') }}"
                                    class="input-modern @error('mail_from_name') border-red-300 @enderror">
                                @error('mail_from_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Fournisseurs recommandés (aide contextuelle) --}}
                        <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                            <p class="text-xs font-semibold text-slate-600 mb-3">Fournisseurs recommandés</p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="p-3 rounded-lg border border-slate-200 bg-white cursor-pointer hover:border-primary-400 transition-colors"
                                    onclick="document.querySelector('[name=mail_host]').value='smtp-relay.brevo.com';document.querySelector('[name=mail_port]').value='587';document.querySelector('[name=mail_encryption]').value='tls'">
                                    <p class="text-xs font-semibold text-slate-900">Brevo</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">300 emails/jour gratuits. Interface en français.</p>
                                    <p class="text-[10px] font-mono text-primary-600 mt-1">smtp-relay.brevo.com:587</p>
                                </div>
                                <div class="p-3 rounded-lg border border-slate-200 bg-white cursor-pointer hover:border-primary-400 transition-colors"
                                    onclick="document.querySelector('[name=mail_host]').value='smtp.resend.com';document.querySelector('[name=mail_port]').value='587';document.querySelector('[name=mail_encryption]').value='tls';document.querySelector('[name=mail_username]').value='resend'">
                                    <p class="text-xs font-semibold text-slate-900">Resend</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">3 000 emails/mois gratuits. Très simple.</p>
                                    <p class="text-[10px] font-mono text-primary-600 mt-1">smtp.resend.com:587</p>
                                </div>
                                <div class="p-3 rounded-lg border border-slate-200 bg-white cursor-pointer hover:border-primary-400 transition-colors"
                                    onclick="document.querySelector('[name=mail_host]').value='smtp.gmail.com';document.querySelector('[name=mail_port]').value='587';document.querySelector('[name=mail_encryption]').value='tls'">
                                    <p class="text-xs font-semibold text-slate-900">Gmail SMTP</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Votre compte Gmail + mot de passe d'application.</p>
                                    <p class="text-[10px] font-mono text-primary-600 mt-1">smtp.gmail.com:587</p>
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-2">Cliquez sur un fournisseur pour préremplir l'hôte et le port automatiquement.</p>
                        </div>

                        <div class="flex justify-end pt-1">
                            <button type="submit" class="btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Enregistrer la configuration
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Test d'envoi --}}
                <form action="{{ route('settings.email.test') }}" method="POST" class="rounded-xl border border-slate-200 overflow-hidden">
                    @csrf
                    <div class="px-5 py-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="text-sm font-semibold text-slate-900">Tester la configuration</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Envoie un email de test pour vérifier que tout fonctionne.</p>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-3">
                            <input type="email" name="test_email"
                                value="{{ auth()->user()->email }}"
                                placeholder="Email destinataire du test"
                                class="input-modern flex-1 max-w-xs">
                            <button type="submit" class="btn-secondary" {{ !$isConfigured ? 'title=Configurez d\'abord le serveur SMTP' : '' }}>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Envoyer un email de test
                            </button>
                        </div>
                        @if(!$isConfigured)
                            <p class="text-[11px] text-amber-600 mt-2">Configurez et enregistrez d'abord le serveur SMTP avant de tester.</p>
                        @endif
                    </div>
                </form>

            </div>
        </div>
        @endif

        {{-- Onglet Canaux --}}
        @if(auth()->user()->hasAnyRole(['super_admin', 'admin_agence']))
        <div id="content-canaux" class="tab-content p-5 hidden">
            <div class="max-w-2xl space-y-5">

                {{-- SMS --}}
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <div class="flex items-center gap-4 px-5 py-4 bg-slate-50 border-b border-slate-200">
                        <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-slate-900">SMS</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Rappels et confirmations par SMS aux locataires</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20 flex-shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Bientôt disponible
                        </span>
                    </div>
                    <div class="px-5 py-4 space-y-3 opacity-50 pointer-events-none select-none">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Clé API SMS</label>
                                <input type="text" disabled placeholder="Votre clé API" class="input-modern bg-slate-50">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">URL API</label>
                                <input type="text" disabled placeholder="https://..." class="input-modern bg-slate-50">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Identifiant expéditeur</label>
                            <input type="text" disabled placeholder="CARON" class="input-modern bg-slate-50 max-w-xs">
                        </div>
                    </div>
                    <div class="px-5 py-3 bg-amber-50/60 border-t border-amber-100">
                        <p class="text-xs text-amber-700">L'intégration SMS sera disponible dans une prochaine mise à jour. Vous serez notifié dès qu'elle sera activable.</p>
                    </div>
                </div>

                {{-- WhatsApp --}}
                <div class="rounded-xl border border-slate-200 overflow-hidden">
                    <div class="flex items-center gap-4 px-5 py-4 bg-slate-50 border-b border-slate-200">
                        <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-slate-900">WhatsApp Business</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Rappels et confirmations via WhatsApp (Meta Business API)</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20 flex-shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Bientôt disponible
                        </span>
                    </div>
                    <div class="px-5 py-4 space-y-3 opacity-50 pointer-events-none select-none">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Token d'accès</label>
                                <input type="text" disabled placeholder="Votre token WhatsApp" class="input-modern bg-slate-50">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Phone Number ID</label>
                                <input type="text" disabled placeholder="ID Meta Business" class="input-modern bg-slate-50">
                            </div>
                        </div>
                    </div>
                    <div class="px-5 py-3 bg-amber-50/60 border-t border-amber-100">
                        <p class="text-xs text-amber-700">L'intégration WhatsApp Business sera disponible dans une prochaine mise à jour. Vous serez notifié dès qu'elle sera activable.</p>
                    </div>
                </div>

                {{-- Email --}}
                <div class="rounded-xl border border-emerald-200 overflow-hidden">
                    <div class="flex items-center gap-4 px-5 py-4 bg-emerald-50 border-b border-emerald-200">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-slate-900">Email</h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">Confirmations et rappels par email (configuré via .env)</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20 flex-shrink-0">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Actif
                        </span>
                    </div>
                    <div class="px-5 py-4">
                        <p class="text-xs text-slate-600">L'envoi d'emails est opérationnel. Les notifications email se basent sur la configuration <code class="text-[11px] bg-slate-100 px-1 py-0.5 rounded">MAIL_*</code> de votre fichier <code class="text-[11px] bg-slate-100 px-1 py-0.5 rounded">.env</code>.</p>
                        <p class="text-xs text-slate-500 mt-1.5">Pour activer ou désactiver les notifications, rendez-vous dans l'onglet <strong>Notifications</strong>.</p>
                    </div>
                </div>

            </div>
        </div>
        @endif

        <div id="content-notifications" class="tab-content p-5 hidden">
            <form action="{{ route('settings.notifications') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                @php $agencyId = $agency ? $agency->id : null; @endphp
                <p class="text-xs text-slate-600 mb-4">Choisissez les types de notifications que vous souhaitez recevoir.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-2xl">
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="email_notifications" value="1" {{ \App\Models\Setting::get('email_notifications', true, $agencyId) ? 'checked' : '' }} class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-medium text-slate-700">Notifications par email</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="sms_notifications" value="1" {{ \App\Models\Setting::get('sms_notifications', false, $agencyId) ? 'checked' : '' }} class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-medium text-slate-700">Notifications par SMS</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="payment_reminders" value="1" {{ \App\Models\Setting::get('payment_reminders', true, $agencyId) ? 'checked' : '' }} class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-medium text-slate-700">Rappels de paiement</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="contract_expiry_alerts" value="1" {{ \App\Models\Setting::get('contract_expiry_alerts', true, $agencyId) ? 'checked' : '' }} class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-medium text-slate-700">Alertes d'expiration de contrat</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors sm:col-span-2">
                        <input type="checkbox" name="overdue_payment_alerts" value="1" {{ \App\Models\Setting::get('overdue_payment_alerts', true, $agencyId) ? 'checked' : '' }} class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-medium text-slate-700">Alertes de paiement en retard</span>
                    </label>
                </div>
                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="submit" class="btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-primary-600', 'text-primary-700');
        btn.classList.add('border-transparent', 'text-slate-500');
    });
    var content = document.getElementById('content-' + tabName);
    if (content) content.classList.remove('hidden');
    var btn = document.getElementById('tab-' + tabName);
    if (btn) {
        btn.classList.remove('border-transparent', 'text-slate-500');
        btn.classList.add('border-primary-600', 'text-primary-700');
    }
}

// Ouvrir l'onglet indiqué en paramètre URL (?tab=payment)
document.addEventListener('DOMContentLoaded', function() {
    var params = new URLSearchParams(window.location.search);
    var tab = params.get('tab');
    if (tab && document.getElementById('tab-' + tab)) {
        showTab(tab);
    }
});

function previewSingleImage(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const size = previewId === 'favicon-preview' ? 'w-10 h-10' : 'w-20 h-20';
                preview.innerHTML = `
                    <div class="relative inline-block">
                        <img src="${e.target.result}" alt="Preview" class="${size} object-cover rounded-lg border border-slate-200">
                        <button type="button" onclick="clearImagePreview('${input.id}', '${previewId}')" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px] hover:bg-red-600">&times;</button>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    }
}

function clearImagePreview(inputId, previewId) {
    document.getElementById(inputId).value = '';
    document.getElementById(previewId).innerHTML = '';
}
</script>

@if(isset($agency) && $agency)
    @if($agency->logo_path)
    <form id="delete-logo-form" action="{{ route('settings.logo.delete') }}" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>
    @endif
    @if($agency->favicon_path)
    <form id="delete-favicon-form" action="{{ route('settings.favicon.delete') }}" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>
    @endif
@endif
@endsection
