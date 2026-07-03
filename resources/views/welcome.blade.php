<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Caron') }}</title>
    @include('partials.assets')
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="min-h-screen flex flex-col">
        <header class="w-full px-6 py-4 flex items-center justify-between max-w-5xl mx-auto">
            @php $agency = \App\Models\Agency::first(); @endphp
            <div class="flex items-center gap-2">
                @if($agency && $agency->logo_path)
                    <img src="{{ asset('storage/' . $agency->logo_path) }}" alt="Caron" class="h-8 w-auto">
                @else
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                @endif
                <span class="text-sm font-bold text-slate-900">{{ config('app.name', 'Caron') }}</span>
            </div>
            <nav class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary text-xs">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary text-xs">Connexion</a>
                @endauth
            </nav>
        </header>

        <main class="flex-1 flex items-center justify-center px-6">
            <div class="max-w-lg text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-9 h-9 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 mb-2">Gestion immobilière simplifiée</h1>
                <p class="text-sm text-slate-600 mb-8 leading-relaxed">
                    Gérez vos biens, locataires, contrats et paiements depuis une plateforme unique, intuitive et moderne.
                </p>
                @guest
                <a href="{{ route('login') }}" class="btn-primary">Se connecter</a>
                @else
                <a href="{{ url('/dashboard') }}" class="btn-primary">Accéder au dashboard</a>
                @endguest
            </div>
        </main>

        <footer class="px-6 py-4 text-center">
            <p class="text-[11px] text-slate-400">&copy; {{ date('Y') }} {{ config('app.name', 'Caron') }}. Tous droits réservés.</p>
        </footer>
    </div>
</body>
</html>
