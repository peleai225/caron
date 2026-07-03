<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Activer votre compte — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @include('partials.assets')
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-xl bg-primary-600 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-xl font-semibold text-slate-900">Activez votre compte</h1>
            <p class="text-sm text-slate-500 mt-1">Bonjour <strong>{{ $user->name }}</strong>, choisissez votre mot de passe.</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <form method="POST" action="{{ route('invitation.accept', $token) }}" class="space-y-4">
                @csrf

                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                    <p class="text-xs text-slate-500">Compte</p>
                    <p class="text-sm font-medium text-slate-900">{{ $user->email }}</p>
                    @if($user->agency)
                        <p class="text-xs text-slate-400 mt-0.5">{{ $user->agency->name }}</p>
                    @endif
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input id="password" name="password" type="password" required
                        class="input-modern" placeholder="8 caractères minimum, lettres et chiffres">
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Confirmer le mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        class="input-modern" placeholder="••••••••">
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Activer mon compte
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </div>
</body>
</html>
