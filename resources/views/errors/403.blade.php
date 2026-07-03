<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Accès refusé | {{ config('app.name', 'Caron') }}</title>
    @include('partials.assets')
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <div class="text-center max-w-sm">
        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <p class="text-5xl font-bold text-slate-900 mb-2">403</p>
        <h1 class="text-sm font-semibold text-slate-900 mb-1">Accès refusé</h1>
        <p class="text-xs text-slate-500 mb-6">Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ url()->previous() ?: url('/') }}" class="btn-secondary text-xs">Retour</a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-primary text-xs">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-primary text-xs">Connexion</a>
            @endauth
        </div>
        <p class="text-[11px] text-slate-400 mt-8">&copy; {{ date('Y') }} {{ config('app.name', 'Caron') }}</p>
    </div>
</body>
</html>
