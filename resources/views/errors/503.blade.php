<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="30">
    <title>503 - Maintenance | {{ config('app.name', 'Caron') }}</title>
    @include('partials.assets')
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <div class="text-center max-w-sm">
        <div class="w-14 h-14 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <svg class="w-7 h-7 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </div>
        <p class="text-5xl font-bold text-slate-900 mb-2">503</p>
        <h1 class="text-sm font-semibold text-slate-900 mb-1">Maintenance en cours</h1>
        <p class="text-xs text-slate-500 mb-6">L'application est temporairement indisponible. Réessayez dans quelques instants.</p>
        <a href="{{ url()->current() }}" class="btn-primary text-xs">Actualiser</a>
        <p class="text-[11px] text-slate-400 mt-8">Rafraîchissement automatique dans 30s</p>
    </div>
</body>
</html>
