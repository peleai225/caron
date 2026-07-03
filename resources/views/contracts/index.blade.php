@extends('layouts.app')

@section('title', 'Contrats')
@section('page-title', 'Contrats')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Contrats</h2>
            <p class="page-subtitle">Gerez vos contrats locatifs</p>
        </div>
        <a href="{{ route('contracts.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Creer un contrat
        </a>
    </header>

    <!-- Filters -->
    <form method="GET" action="{{ route('contracts.index') }}" class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut</label>
                    <select name="status" class="input-modern">
                        <option value="">Tous</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expire</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Resilie</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary">Filtrer</button>
                    <a href="{{ route('contracts.index') }}" class="btn-secondary">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="card-panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full responsive-table">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Locataire</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Bien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Loyer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Periode</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($contracts as $contract)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td data-label="Locataire" class="px-4 py-3">
                            <p class="text-sm font-medium text-slate-900">{{ $contract->tenant->full_name ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500">{{ $contract->tenant->phone ?? '' }}</p>
                        </td>
                        <td data-label="Type" class="px-4 py-3 text-sm text-slate-600">
                            {{ $contract->type_contrat ? (\App\Models\Contract::typesContrat()[$contract->type_contrat] ?? $contract->type_contrat) : '—' }}
                        </td>
                        <td data-label="Bien" class="px-4 py-3">
                            <p class="text-sm text-slate-900">{{ $contract->property->address ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500">{{ $contract->property->city ?? '' }}</p>
                        </td>
                        <td data-label="Loyer" class="px-4 py-3">
                            <span class="text-sm font-medium text-slate-900">{{ number_format($contract->rent_amount, 0, ',', ' ') }} F</span>
                        </td>
                        <td data-label="Période" class="px-4 py-3">
                            <p class="text-xs text-slate-600">{{ $contract->start_date->format('d/m/Y') }}</p>
                            <p class="text-xs text-slate-500">{{ $contract->end_date->format('d/m/Y') }}</p>
                        </td>
                        <td data-label="Statut" class="px-4 py-3">
                            <x-status-badge :status="$contract->status" size="sm" />
                        </td>
                        <td data-label="" class="px-4 py-3">
                            <div class="flex gap-1">
                                <a href="{{ route('contracts.show', $contract) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                                <a href="{{ route('contracts.edit', $contract) }}" class="px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded transition-colors">Modifier</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="empty-state-icon">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucun contrat enregistre</h3>
                            <p class="text-xs text-slate-500 mb-4">Creez votre premier contrat locatif</p>
                            <a href="{{ route('contracts.create') }}" class="btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Creer un contrat
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($contracts->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $contracts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
