@extends('layouts.app')

@section('title', 'Logs d\'activite')
@section('page-title', 'Journal d\'activite')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Journal d'activite</h2>
            <p class="page-subtitle">Historique des actions effectuees dans le systeme</p>
        </div>
    </header>

    <!-- Filters -->
    <form method="GET" action="{{ route('activity-logs.index') }}" class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Utilisateur</label>
                    <select name="user_id" class="input-modern">
                        <option value="">Tous</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type</label>
                    <select name="subject_type" class="input-modern">
                        <option value="">Tous</option>
                        <option value="App\Models\Property" {{ request('subject_type') == 'App\Models\Property' ? 'selected' : '' }}>Biens</option>
                        <option value="App\Models\Tenant" {{ request('subject_type') == 'App\Models\Tenant' ? 'selected' : '' }}>Locataires</option>
                        <option value="App\Models\Contract" {{ request('subject_type') == 'App\Models\Contract' ? 'selected' : '' }}>Contrats</option>
                        <option value="App\Models\Payment" {{ request('subject_type') == 'App\Models\Payment' ? 'selected' : '' }}>Paiements</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Evenement</label>
                    <select name="event" class="input-modern">
                        <option value="">Tous</option>
                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Cree</option>
                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Modifie</option>
                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Supprime</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Date debut</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="input-modern">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Date fin</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="input-modern">
                </div>
            </div>
            <div class="flex gap-2 mt-3">
                <button type="submit" class="btn-primary">Filtrer</button>
                <a href="{{ route('activity-logs.index') }}" class="btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="card-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Utilisateur</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Action</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Objet</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-3 text-sm text-slate-900">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $log->causer->name ?? 'Systeme' }}</td>
                        <td class="px-4 py-3">
                            @if($log->event === 'created')
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">Cree</span>
                            @elseif($log->event === 'updated')
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20">Modifie</span>
                            @elseif($log->event === 'deleted')
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-red-50 text-red-700 ring-red-600/20">Supprime</span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-slate-50 text-slate-600 ring-slate-500/20">{{ ucfirst($log->event) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-900">
                            {{ class_basename($log->subject_type) }}
                            @if($log->subject)
                                — {{ $log->subject->name ?? $log->subject->first_name ?? 'N/A' }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('activity-logs.show', $log) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <div class="empty-state-icon">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucun log d'activite</h3>
                            <p class="text-xs text-slate-500">Les actions apparaitront ici</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
