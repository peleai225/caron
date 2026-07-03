<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Erreur serveur | {{ config('app.name', 'Caron') }}</title>
    @include('partials.assets')
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <div class="text-center max-w-sm">
        <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <p class="text-5xl font-bold text-slate-900 mb-2">500</p>
        <h1 class="text-sm font-semibold text-slate-900 mb-1">Erreur serveur</h1>
        <p class="text-xs text-slate-500 mb-6">Une erreur interne s'est produite. Veuillez réessayer plus tard.</p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ url('/') }}" class="btn-secondary text-xs">Accueil</a>
            <a href="{{ url()->current() }}" class="btn-primary text-xs">Réessayer</a>
        </div>
        <p class="text-[11px] text-slate-400 mt-8">&copy; {{ date('Y') }} {{ config('app.name', 'Caron') }}</p>
    </div>
</body>
</html>
