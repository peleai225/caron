<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inscription - {{ config('app.name', 'Caron') }}</title>
    @include('partials.assets')
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md space-y-6">
            <!-- Logo -->
            <div class="text-center">
                @php $agency = \App\Models\Agency::first(); @endphp
                @if($agency && $agency->logo_path)
                    <img src="{{ asset('storage/' . $agency->logo_path) }}" alt="Caron" class="h-12 w-auto mx-auto mb-4">
                @else
                    <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                @endif
                <h1 class="text-xl font-bold text-slate-900">Creer un compte</h1>
                <p class="text-xs text-slate-500 mt-1">Rejoignez Caron pour gerer votre patrimoine</p>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-medium text-slate-600 mb-1.5">Nom complet</label>
                        <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}" class="input-modern" placeholder="Jean Dupont">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="input-modern" placeholder="votre@email.com">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-medium text-slate-600 mb-1.5">Mot de passe</label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required class="input-modern" placeholder="••••••••">
                            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-xs font-medium text-slate-600 mb-1.5">Confirmer</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="input-modern" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-start pt-1">
                        <label class="flex items-start gap-2 cursor-pointer">
                            <input id="terms" name="terms" type="checkbox" required class="mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-xs text-slate-600 leading-relaxed">
                                J'accepte les <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">conditions d'utilisation</a> et la <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">politique de confidentialite</a>
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="btn-primary w-full justify-center py-2.5">Creer mon compte</button>
                </form>
            </div>

            <!-- Login link -->
            <p class="text-center text-xs text-slate-500">
                Deja un compte ?
                <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-700">Se connecter</a>
            </p>

            <p class="text-center text-[11px] text-slate-400">
                &copy; {{ date('Y') }} Caron. Tous droits reserves.
            </p>
        </div>
    </div>
</body>
</html>
