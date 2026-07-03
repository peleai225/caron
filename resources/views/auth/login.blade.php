<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - {{ config('app.name', 'Caron') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @include('partials.assets')
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <!-- Logo -->
        <div class="text-center mb-8">
            @php $agency = \App\Models\Agency::first(); @endphp
            @if($agency && $agency->logo_path)
                <img src="{{ asset('storage/' . $agency->logo_path) }}" alt="Caron" class="h-10 w-auto mx-auto mb-4">
            @else
                <div class="w-10 h-10 rounded-lg bg-primary-600 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
            @endif
            <h1 class="text-xl font-semibold text-slate-900">Connexion</h1>
            <p class="text-sm text-slate-500 mt-1">Accedez a votre espace de gestion</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            @if(session('error'))
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        value="{{ old('email') }}"
                        class="input-modern"
                        placeholder="votre@email.com"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Mot de passe</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="input-modern"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input id="remember" name="remember" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500/20">
                        <span class="text-sm text-slate-600">Se souvenir</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                        Mot de passe oublié ?
                    </a>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Se connecter
                </button>
            </form>

            <p class="mt-4 pt-4 border-t border-slate-100 text-center text-xs text-slate-400">
                Accès sur invitation uniquement. Contactez votre administrateur.
            </p>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">&copy; {{ date('Y') }} Caron</p>
    </div>
</body>
</html>
