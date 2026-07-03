@extends('layouts.app')

@section('title', 'Locataires')
@section('page-title', 'Locataires')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Locataires</h2>
            <p class="page-subtitle">Gerez vos locataires</p>
        </div>
        <button type="button" data-modal-open="modal-tenant-create" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Ajouter un locataire
        </button>
    </header>

    <!-- Filters -->
    <form method="GET" action="{{ route('tenants.index') }}" data-no-protect class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email, telephone..." class="input-modern">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut</label>
                    <select name="status" class="input-modern">
                        <option value="">Tous</option>
                        <option value="actif" {{ request('status') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="en_retard" {{ request('status') == 'en_retard' ? 'selected' : '' }}>En retard</option>
                        <option value="resilie" {{ request('status') == 'resilie' ? 'selected' : '' }}>Resilie</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary flex-1">Filtrer</button>
                    <a href="{{ route('tenants.index') }}" class="btn-secondary">Reset</a>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Contact</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Bien</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($tenants as $tenant)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td data-label="Locataire" class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-primary-700 text-xs font-medium">{{ substr($tenant->first_name, 0, 1) }}{{ substr($tenant->last_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">{{ $tenant->full_name }}</p>
                                    @if($tenant->cni_number)
                                        <p class="text-xs text-slate-500">CNI: {{ $tenant->cni_number }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td data-label="Contact" class="px-4 py-3">
                            <p class="text-sm text-slate-900">{{ $tenant->phone }}</p>
                            @if($tenant->email)
                                <p class="text-xs text-slate-500">{{ $tenant->email }}</p>
                            @endif
                        </td>
                        <td data-label="Bien" class="px-4 py-3">
                            @if($tenant->contracts->count() > 0)
                                <p class="text-sm text-slate-900">{{ $tenant->contracts->first()->property->address ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500">{{ $tenant->contracts->count() }} contrat{{ $tenant->contracts->count() > 1 ? 's' : '' }}</p>
                            @else
                                <span class="text-xs text-slate-400">Aucun contrat</span>
                            @endif
                        </td>
                        <td data-label="Statut" class="px-4 py-3">
                            <x-status-badge :status="$tenant->status" size="sm" />
                        </td>
                        <td data-label="" class="px-4 py-3">
                            <div class="flex gap-1">
                                <a href="{{ route('tenants.show', $tenant) }}" class="px-2 py-1 text-xs font-medium text-primary-600 hover:bg-primary-50 rounded transition-colors">Voir</a>
                                <button type="button"
                                    data-tenant-edit
                                    data-id="{{ $tenant->id }}"
                                    data-update-url="{{ route('tenants.update', $tenant) }}"
                                    data-first-name="{{ $tenant->first_name }}"
                                    data-last-name="{{ $tenant->last_name }}"
                                    data-email="{{ $tenant->email }}"
                                    data-phone="{{ $tenant->phone }}"
                                    data-cni="{{ $tenant->cni_number }}"
                                    data-status="{{ $tenant->status }}"
                                    data-notes="{{ $tenant->notes }}"
                                    class="px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded transition-colors">Modifier</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <div class="empty-state-icon">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-1">Aucun locataire enregistre</h3>
                            <p class="text-xs text-slate-500 mb-4">Commencez par ajouter votre premier locataire</p>
                            <button type="button" data-modal-open="modal-tenant-create" class="btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Ajouter un locataire
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal : Ajouter un locataire --}}
<div id="modal-tenant-create" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Nouveau locataire</h3>
            <button type="button" data-modal-close="modal-tenant-create" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('tenants.store') }}" enctype="multipart/form-data" class="divide-y divide-slate-100">
            @csrf
            <div class="px-5 py-4 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" required class="input-modern" placeholder="Jean">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" required class="input-modern" placeholder="Dupont">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                        <input type="email" name="email" class="input-modern" placeholder="jean@email.com">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" required class="input-modern" placeholder="+225 07 XX XX XX XX">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">CNI / Passeport</label>
                        <input type="file" name="cni" accept="image/*,.pdf" class="input-modern text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Numéro CNI</label>
                        <input type="text" name="cni_number" class="input-modern" placeholder="Optionnel">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                    <textarea name="notes" rows="2" class="input-modern" placeholder="Informations supplémentaires..."></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <button type="button" data-modal-close="modal-tenant-create" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal : Modifier locataire --}}
<div id="modal-tenant-edit" class="modal-overlay fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
    <div class="modal-backdrop absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>
    <div class="modal-content absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-lg bg-white rounded-xl shadow-xl transform scale-95 transition-transform duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 sticky top-0 bg-white z-10">
            <h3 class="text-sm font-semibold text-slate-900">Modifier le locataire</h3>
            <button type="button" data-modal-close="modal-tenant-edit" class="p-1 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="form-tenant-edit" method="POST" class="divide-y divide-slate-100">
            @csrf
            @method('PUT')
            <div class="px-5 py-4 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" id="te-first-name" required class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" id="te-last-name" required class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                        <input type="email" name="email" id="te-email" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Téléphone <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" id="te-phone" required class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Numéro CNI</label>
                        <input type="text" name="cni_number" id="te-cni" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut <span class="text-red-500">*</span></label>
                        <select name="status" id="te-status" required class="input-modern">
                            <option value="actif">Actif</option>
                            <option value="en_retard">En retard</option>
                            <option value="resilie">Résilié</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                    <textarea name="notes" id="te-notes" rows="2" class="input-modern"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <button type="button" data-modal-close="modal-tenant-edit" class="btn-secondary">Annuler</button>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-tenant-edit]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var form = document.getElementById('form-tenant-edit');
            form.action = btn.dataset.updateUrl;
            document.getElementById('te-first-name').value = btn.dataset.firstName || '';
            document.getElementById('te-last-name').value  = btn.dataset.lastName || '';
            document.getElementById('te-email').value      = btn.dataset.email || '';
            document.getElementById('te-phone').value      = btn.dataset.phone || '';
            document.getElementById('te-cni').value        = btn.dataset.cni || '';
            document.getElementById('te-status').value     = btn.dataset.status || 'actif';
            document.getElementById('te-notes').value      = btn.dataset.notes || '';
            openModal('modal-tenant-edit');
        });
    });
});
</script>
@endsection
