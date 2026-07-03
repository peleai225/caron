@extends('layouts.app')

@section('title', 'Utilisateurs')
@section('page-title', 'Utilisateurs')

@section('content')
<div class="space-y-5">

    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Utilisateurs</h2>
            <p class="page-subtitle">Gérez les membres de votre équipe</p>
        </div>
        @can('manage users')
        <a href="{{ route('users.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Inviter un utilisateur
        </a>
        @endcan
    </header>

    {{-- Filtres --}}
    <form method="GET" class="flex flex-wrap gap-3 items-center">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." class="input-modern w-52">
        <select name="role" class="input-modern w-44">
            <option value="">Tous les rôles</option>
            @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-secondary">Filtrer</button>
        @if(request()->hasAny(['search','role']))
            <a href="{{ route('users.index') }}" class="text-xs text-slate-500 hover:text-slate-700">Réinitialiser</a>
        @endif
    </form>

    <div class="card-panel overflow-hidden">
        <table class="min-w-full">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Utilisateur</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Rôle</th>
                    @if(auth()->user()->hasRole('super_admin'))
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Agence</th>
                    @endif
                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50/50 transition-colors {{ !$user->is_active ? 'opacity-60' : '' }}">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($user->avatar_path)
                                <img src="{{ asset('storage/'.$user->avatar_path) }}" class="w-8 h-8 rounded-full object-cover" alt="">
                            @else
                                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-sm font-semibold text-primary-700">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-slate-900">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        @foreach($user->roles as $role)
                            <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md bg-primary-50 text-primary-700 ring-1 ring-inset ring-primary-600/20">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </span>
                        @endforeach
                    </td>
                    @if(auth()->user()->hasRole('super_admin'))
                    <td class="px-4 py-3 text-sm text-slate-600">{{ $user->agency?->name ?? '—' }}</td>
                    @endif
                    <td class="px-4 py-3 text-center">
                        @if(!$user->is_active && $user->invitation_token)
                            <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20">
                                Invitation envoyée
                            </span>
                        @elseif($user->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">
                                Actif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-slate-100 text-slate-500 ring-slate-500/20">
                                Désactivé
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            @can('manage users')
                                {{-- Renvoyer invitation --}}
                                @if(!$user->is_active && $user->id !== auth()->id())
                                <form action="{{ route('users.resend-invitation', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-primary-600 hover:text-primary-800 font-medium" title="Renvoyer l'invitation">
                                        Renvoyer
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('users.edit', $user) }}" class="text-xs text-slate-600 hover:text-slate-900 font-medium">Modifier</a>

                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.toggle-active', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs {{ $user->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-emerald-600 hover:text-emerald-800' }} font-medium">
                                        {{ $user->is_active ? 'Désactiver' : 'Réactiver' }}
                                    </button>
                                </form>

                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline"
                                    data-confirm="Supprimer {{ $user->name }} ? Cette action est irréversible.">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Supprimer</button>
                                </form>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-400">Aucun utilisateur trouvé</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}

</div>
@endsection
