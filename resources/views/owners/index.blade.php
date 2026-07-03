@extends('layouts.app')

@section('title', 'Proprietaires')
@section('page-title', 'Proprietaires')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Proprietaires</h2>
            <p class="page-subtitle">Gerez vos proprietaires</p>
        </div>
        <button type="button" data-modal-open="modal-owner-create" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Ajouter un proprietaire
        </button>
    </header>

    <!-- Filters -->
    <form method="GET" action="{{ route('owners.index') }}" class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email, telephone..." class="input-modern">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary">Filtrer</button>
                    <a href="{{ route('owners.index') }}" class="btn-secondary">Reset</a>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Proprietaire</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Biens</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($owners as $owner)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td data-label="Proprietaire" class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center">
                                    <span class="text-violet-700 text-xs font-medium">{{ substr($owner->name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">{{ $owner->name }}</p>
                                    @if($owner->identification_number)
                                        <p class="text-xs text-slate-500">ID: {{ $owner->identification_number }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td data-label="Contact" class="px-4 py-3">
                            <p class="text-sm text-slate-900">{{ $owner->phone }}</p>
                            @if($owner->email)
                                <p class="text-xs text-slate-500">{{ $owner->email }}</p>
                            @endif
                        </td>
                        <td data-label="Biens" class="px-4 py-3">
                            <p class="text-sm text-slate-900">{{ $owner->properties->count() }} bien{{ $owner->properties->count() > 1 ? 's' : '' }}</p>
                            <p class="text-xs text-slate-500">{{ $owner->contracts->count() }} contrat{{ $owner->contracts->count() > 1 ? 's' : '' }}</p>
                        </td>
                        <td data-label="Statut" class="px-4 py-3">
                            @if($owner->is_active)
                                <x-status-badge status="active" size="sm" />
                            @else
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[11px] rounded-md font-medium ring-1 ring-inset bg-slate-50 text-slate-600 ring-slate-500/20">Inactif</span>
                            @endif
                        </td>
                        <td data-label="" class="px-4 py-3">
                            <div class="flex gap-1">
                                <a href="{{ route('owners.show', $owner) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                                <button type="button"
                                    data-owner-edit
                                    data-id="{{ $owner->id }}"
                                    data-update-url="{{ route('owners.update', $owner) }}"
                                    data-name="{{ $owner->name }}"
                                    data-email="{{ $owner->email }}"
                                    data-phone="{{ $owner->phone }}"
                                    data-address="{{ $owner->address }}"
                                    data-identification="{{ $owner->identification_number }}"
                                    data-is-active="{{ $owner->is_active ? '1' : '0' }}"
                                    data-notes="{{ $owner->notes }}"
                                    class="px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded transition-colors">Modifier</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <div class="empty-state-icon">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucun proprietaire enregistre</h3>
                            <p class="text-xs text-slate-500 mb-4">Commencez par ajouter votre premier proprietaire</p>
                            <button type="button" data-modal-open="modal-owner-create" class="btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Ajouter un proprietaire
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($owners->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $owners->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal : Modifier un propriétaire --}}
<div id="modal-owner-edit" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Modifier le propriétaire</h3>
            <button type="button" data-modal-close="modal-owner-edit" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-owner-edit" method="POST" class="divide-y divide-slate-100">
            @csrf
            @method('PUT')
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="oe-name" required class="input-modern">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                        <input type="email" name="email" id="oe-email" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" id="oe-phone" required class="input-modern">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Adresse</label>
                    <textarea name="address" id="oe-address" rows="2" class="input-modern"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">N° d'identification</label>
                    <input type="text" name="identification_number" id="oe-identification" class="input-modern">
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="oe-is-active" value="1" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-medium text-slate-600">Propriétaire actif</span>
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                    <textarea name="notes" id="oe-notes" rows="2" class="input-modern"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <button type="button" data-modal-close="modal-owner-edit" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal : Ajouter un propriétaire --}}
<div id="modal-owner-create" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Nouveau propriétaire</h3>
            <button type="button" data-modal-close="modal-owner-create" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('owners.store') }}" class="divide-y divide-slate-100">
            @csrf
            <div class="px-5 py-4 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="input-modern" placeholder="Nom du propriétaire">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                        <input type="email" name="email" class="input-modern" placeholder="proprietaire@domaine.com">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" required class="input-modern" placeholder="+225 07 XX XX XX XX">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Adresse</label>
                    <textarea name="address" rows="2" class="input-modern" placeholder="Adresse complète"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">N° d'identification</label>
                    <input type="text" name="identification_number" class="input-modern" placeholder="CNI, Passeport...">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                    <textarea name="notes" rows="2" class="input-modern" placeholder="Notes supplémentaires..."></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <button type="button" data-modal-close="modal-owner-create" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-owner-edit]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var form = document.getElementById('form-owner-edit');
            form.action = btn.dataset.updateUrl;
            document.getElementById('oe-name').value           = btn.dataset.name || '';
            document.getElementById('oe-email').value          = btn.dataset.email || '';
            document.getElementById('oe-phone').value          = btn.dataset.phone || '';
            document.getElementById('oe-address').value        = btn.dataset.address || '';
            document.getElementById('oe-identification').value = btn.dataset.identification || '';
            document.getElementById('oe-is-active').checked    = btn.dataset.isActive === '1';
            document.getElementById('oe-notes').value          = btn.dataset.notes || '';
            openModal('modal-owner-edit');
        });
    });
});
</script>
@endsection
