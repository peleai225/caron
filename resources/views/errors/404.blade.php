<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page introuvable | {{ config('app.name', 'Caron') }}</title>
    @include('partials.assets')
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">
    <div class="text-center max-w-sm">
        <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <p class="text-5xl font-bold text-slate-900 mb-2">404</p>
        <h1 class="text-sm font-semibold text-slate-900 mb-1">Page introuvable</h1>
        <p class="text-xs text-slate-500 mb-6">La page que vous recherchez n'existe pas ou a été déplacée.</p>
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
