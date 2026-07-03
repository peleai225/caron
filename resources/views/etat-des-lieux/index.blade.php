@extends('layouts.app')

@section('title', 'Etats des lieux')
@section('page-title', 'Etats des lieux')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Etats des lieux</h2>
            <p class="page-subtitle">Entrees et sorties des biens</p>
        </div>
        <button type="button" data-modal-open="modal-edl-create" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nouvel etat des lieux
        </button>
    </header>

    <!-- Filters -->
    <form method="GET" class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bien</label>
                    <select name="property_id" class="input-modern">
                        <option value="">Tous les biens</option>
                        @foreach($properties ?? [] as $p)
                            <option value="{{ $p->id }}" {{ request('property_id') == $p->id ? 'selected' : '' }}>{{ $p->name ?? $p->address }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Type</label>
                    <select name="type" class="input-modern">
                        <option value="">Tous</option>
                        <option value="entree" {{ request('type') == 'entree' ? 'selected' : '' }}>Entree</option>
                        <option value="sortie" {{ request('type') == 'sortie' ? 'selected' : '' }}>Sortie</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary">Filtrer</button>
                    <a href="{{ route('etat-des-lieux.index') }}" class="btn-secondary">Reset</a>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Bien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Locataire</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($etatDesLieux as $edl)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td data-label="Bien" class="px-4 py-3 text-sm font-medium text-slate-900">{{ $edl->property->name ?? $edl->property->address }}</td>
                        <td data-label="Type" class="px-4 py-3">
                            @if($edl->type === 'entree')
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-600/20">Entree</span>
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-600/20">Sortie</span>
                            @endif
                        </td>
                        <td data-label="Date" class="px-4 py-3 text-sm text-slate-600">{{ $edl->date->format('d/m/Y') }}</td>
                        <td data-label="Locataire" class="px-4 py-3 text-sm text-slate-600">{{ $edl->contract?->tenant?->full_name ?? '—' }}</td>
                        <td data-label="" class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('etat-des-lieux.show', $edl) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                                <button type="button"
                                    data-edl-edit
                                    data-update-url="{{ route('etat-des-lieux.update', $edl) }}"
                                    data-property-id="{{ $edl->property_id }}"
                                    data-contract-id="{{ $edl->contract_id }}"
                                    data-type="{{ $edl->type }}"
                                    data-date="{{ $edl->date->format('Y-m-d') }}"
                                    data-observations="{{ $edl->observations }}"
                                    class="px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded transition-colors">Modifier</button>
                                <form method="POST" action="{{ route('etat-des-lieux.destroy', $edl) }}" onsubmit="return confirm('Supprimer cet état des lieux ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50 rounded transition-colors">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <div class="empty-state-icon">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucun etat des lieux</h3>
                            <p class="text-xs text-slate-500 mb-4">Commencez par creer un etat des lieux</p>
                            <button type="button" data-modal-open="modal-edl-create" class="btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Nouvel etat des lieux
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-100">
            {{ $etatDesLieux->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- Modal : Modifier état des lieux --}}
<div id="modal-edl-edit" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Modifier l'état des lieux</h3>
            <button type="button" data-modal-close="modal-edl-edit" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-edl-edit" method="POST" action="#" class="divide-y divide-slate-100">
            @csrf @method('PUT')
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bien immobilier <span class="text-red-500">*</span></label>
                    <select id="edit-edl-property" name="property_id" required class="input-modern">
                        <option value="">Sélectionner un bien</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}">{{ $p->name ?? $p->address }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Contrat</label>
                    <select id="edit-edl-contract" name="contract_id" class="input-modern">
                        <option value="">Aucun contrat</option>
                        @foreach($contracts as $c)
                            <option value="{{ $c->id }}">{{ $c->contract_number }} — {{ $c->tenant?->full_name ?? '' }} ({{ $c->property->address ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Type <span class="text-red-500">*</span></label>
                        <select id="edit-edl-type" name="type" required class="input-modern">
                            <option value="entree">Entrée</option>
                            <option value="sortie">Sortie</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input id="edit-edl-date" type="date" name="date" required class="input-modern">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Observations</label>
                    <textarea id="edit-edl-observations" name="observations" rows="3" class="input-modern"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <button type="button" data-modal-close="modal-edl-edit" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal : Nouvel état des lieux --}}
<div id="modal-edl-create" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Nouvel état des lieux</h3>
            <button type="button" data-modal-close="modal-edl-create" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('etat-des-lieux.store') }}" class="divide-y divide-slate-100">
            @csrf
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Bien immobilier <span class="text-red-500">*</span></label>
                    <select name="property_id" required class="input-modern">
                        <option value="">Sélectionner un bien</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}">{{ $p->name ?? $p->address }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Contrat</label>
                    <select name="contract_id" class="input-modern">
                        <option value="">Aucun contrat</option>
                        @foreach($contracts as $c)
                            <option value="{{ $c->id }}">{{ $c->contract_number }} — {{ $c->tenant?->full_name ?? '' }} ({{ $c->property->address ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="input-modern">
                            <option value="entree">Entrée</option>
                            <option value="sortie">Sortie</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" required class="input-modern" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Observations</label>
                    <textarea name="observations" rows="3" class="input-modern" placeholder="Observations sur l'état du bien..."></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <button type="button" data-modal-close="modal-edl-create" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-edl-edit]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('form-edl-edit').action = btn.dataset.updateUrl;
            document.getElementById('edit-edl-property').value = btn.dataset.propertyId || '';
            document.getElementById('edit-edl-contract').value = btn.dataset.contractId || '';
            document.getElementById('edit-edl-type').value = btn.dataset.type || 'entree';
            document.getElementById('edit-edl-date').value = btn.dataset.date || '';
            document.getElementById('edit-edl-observations').value = btn.dataset.observations || '';
            openModal('modal-edl-edit');
        });
    });
});
</script>
@endsection
