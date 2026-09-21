@extends('layouts.app')

@section('title', 'Créer un contrat')
@section('page-title', 'Créer un contrat')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouveau contrat</h2>
            <p class="page-subtitle">Renseignez les informations du contrat de location</p>
        </div>
    </header>

    <div class="card-panel overflow-hidden">
        <form method="POST" action="{{ route('contracts.store') }}" class="flex flex-col lg:flex-row min-h-[400px]">
            @csrf

            <nav class="lg:w-56 flex-shrink-0 border-b lg:border-b-0 lg:border-r border-slate-100 bg-slate-50/50">
                <div class="flex lg:flex-col overflow-x-auto lg:overflow-x-visible p-3 lg:py-5 gap-1">
                    <button type="button" data-section="section-1" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors active">
                        <span class="w-6 h-6 rounded-md bg-primary-100 text-primary-700 flex items-center justify-center text-[11px] font-bold flex-shrink-0">1</span>
                        <span class="text-slate-800">Parties</span>
                    </button>
                    <button type="button" data-section="section-2" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">2</span>
                        <span class="text-slate-500">Conditions</span>
                    </button>
                    <button type="button" data-section="section-3" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">3</span>
                        <span class="text-slate-500">Paiement</span>
                    </button>
                    <button type="button" data-section="section-4" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">4</span>
                        <span class="text-slate-500">Modèle et notes</span>
                    </button>
                </div>
            </nav>

            <div class="flex-1 overflow-y-auto">
                <div class="p-5 lg:p-6 space-y-6">
                    <section id="section-1" class="section-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="tenant_id" class="block text-xs font-medium text-slate-600 mb-1.5">Locataire <span class="text-red-500">*</span></label>
                                <select id="tenant_id" name="tenant_id" required class="input-modern searchable-select">
                                    <option value="">Sélectionner...</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                            {{ $tenant->full_name }} ({{ $tenant->phone }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('tenant_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Recherche AJAX bien disponible --}}
                            <div class="relative" id="prop-search-wrap">
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Bien immobilier <span class="text-red-500">*</span></label>
                                <input type="text" id="prop-search" autocomplete="off" class="input-modern" placeholder="Rechercher par adresse, désignation, ville...">
                                <input type="hidden" id="property_id" name="property_id" value="{{ old('property_id', request('property_id')) }}" required>
                                <div id="prop-results" class="absolute z-30 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-56 overflow-y-auto hidden"></div>
                                @error('property_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="owner_id" class="block text-xs font-medium text-slate-600 mb-1.5">Propriétaire</label>
                                <select id="owner_id" name="owner_id" class="input-modern searchable-select">
                                    <option value="">Sélectionner (optionnel)...</option>
                                    @foreach($owners ?? [] as $owner)
                                        <option value="{{ $owner->id }}" {{ old('owner_id') == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                                    @endforeach
                                </select>
                                @error('owner_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </section>

                    <section id="section-2" class="section-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="type_contrat" class="block text-xs font-medium text-slate-600 mb-1.5">Type de contrat</label>
                                <select id="type_contrat" name="type_contrat" class="input-modern">
                                    <option value="">— Choisir —</option>
                                    @foreach(\App\Models\Contract::typesContrat() as $k => $v)
                                        <option value="{{ $k }}" {{ old('type_contrat') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                                @error('type_contrat')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="rent_amount" class="block text-xs font-medium text-slate-600 mb-1.5">Loyer (FCFA) <span class="text-red-500">*</span></label>
                                <input type="number" id="rent_amount" name="rent_amount" required value="{{ old('rent_amount') }}" min="0" step="1" class="input-modern" placeholder="150000">
                                @error('rent_amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="deposit" class="block text-xs font-medium text-slate-600 mb-1.5">Caution (FCFA)</label>
                                <input type="number" id="deposit" name="deposit" value="{{ old('deposit') }}" min="0" step="1" class="input-modern" placeholder="300000">
                                @error('deposit')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="start_date" class="block text-xs font-medium text-slate-600 mb-1.5">Date de début <span class="text-red-500">*</span></label>
                                <input type="date" id="start_date" name="start_date" required value="{{ old('start_date') }}" class="input-modern">
                                @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="end_date" class="block text-xs font-medium text-slate-600 mb-1.5">Date de fin <span class="text-red-500">*</span></label>
                                <input type="date" id="end_date" name="end_date" required value="{{ old('end_date') }}" class="input-modern">
                                @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </section>

                    <section id="section-3" class="section-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="payment_frequency" class="block text-xs font-medium text-slate-600 mb-1.5">Fréquence <span class="text-red-500">*</span></label>
                                <select id="payment_frequency" name="payment_frequency" required class="input-modern">
                                    <option value="monthly">Mensuel</option>
                                    <option value="quarterly">Trimestriel</option>
                                    <option value="yearly">Annuel</option>
                                </select>
                            </div>
                            <div>
                                <label for="payment_day" class="block text-xs font-medium text-slate-600 mb-1.5">Jour de paiement <span class="text-red-500">*</span></label>
                                <input type="number" id="payment_day" name="payment_day" required value="{{ old('payment_day', 1) }}" min="1" max="31" class="input-modern">
                            </div>
                        </div>
                    </section>

                    <section id="section-4" class="section-content hidden">
                        <div class="space-y-4">
                            @if($templates->isNotEmpty())
                            <div>
                                <label for="template_id" class="block text-xs font-medium text-slate-600 mb-1.5">Modèle de contrat</label>
                                <select id="template_id" name="template_id" class="input-modern">
                                    <option value="">— Aucun modèle —</option>
                                    @foreach($templates as $tpl)
                                        <option value="{{ $tpl->id }}" {{ old('template_id') == $tpl->id ? 'selected' : '' }}>{{ $tpl->name }}{{ $tpl->is_default ? ' (par défaut)' : '' }}</option>
                                    @endforeach
                                </select>
                                @error('template_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            @endif
                            <div>
                                <label for="notes" class="block text-xs font-medium text-slate-600 mb-1.5">Notes additionnelles</label>
                                <textarea id="notes" name="notes" rows="5" class="input-modern" placeholder="Conditions spéciales, clauses particulières...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="flex items-center justify-between gap-3 px-5 py-4 border-t border-slate-100">
                    <a href="{{ route('contracts.index') }}" class="btn-secondary">Annuler</a>
                    <div class="flex gap-2">
                        <button type="button" id="btn-prev" class="hidden btn-secondary">Précédent</button>
                        <button type="button" id="btn-next" class="btn-secondary">Suivant</button>
                        <button type="submit" id="btn-submit" class="hidden btn-primary">Créer le contrat</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    // ── Recherche AJAX biens disponibles ──────────────────────────────────
    var propSearch  = document.getElementById('prop-search');
    var propHidden  = document.getElementById('property_id');
    var propResults = document.getElementById('prop-results');
    var rentInput   = document.getElementById('rent_amount');
    var propTimer   = null;

    function fetchProperties(q) {
        fetch('/contracts/search-properties?q=' + encodeURIComponent(q), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            propResults.textContent = '';
            if (!data.length) {
                var el = document.createElement('div');
                el.className = 'px-4 py-3 text-xs text-slate-400';
                el.textContent = 'Aucun bien disponible';
                propResults.appendChild(el);
                propResults.classList.remove('hidden');
                return;
            }
            data.forEach(function (item) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full text-left px-4 py-2.5 hover:bg-slate-50 border-b border-slate-100 last:border-0';
                var name = document.createElement('p');
                name.className = 'text-xs font-semibold text-slate-900';
                name.textContent = item.label;
                var sub = document.createElement('p');
                sub.className = 'text-[11px] text-slate-500';
                sub.textContent = (item.type ?? '') + (item.monthly_rent ? ' · Loyer suggéré : ' + Number(item.monthly_rent).toLocaleString('fr-FR') + ' FCFA' : '');
                btn.appendChild(name);
                btn.appendChild(sub);
                btn.addEventListener('click', function () {
                    propHidden.value = item.id;
                    propSearch.value = item.label;
                    if (rentInput && item.monthly_rent) {
                        rentInput.value = item.monthly_rent;
                    }
                    propResults.classList.add('hidden');
                });
                propResults.appendChild(btn);
            });
            propResults.classList.remove('hidden');
        });
    }

    if (propSearch) {
        // Afficher les biens au focus (sans taper)
        propSearch.addEventListener('focus', function () {
            if (propResults.children.length === 0 || propResults.classList.contains('hidden')) {
                fetchProperties(this.value.trim());
            } else {
                propResults.classList.remove('hidden');
            }
        });

        propSearch.addEventListener('input', function () {
            clearTimeout(propTimer);
            propHidden.value = '';
            var q = this.value.trim();
            propTimer = setTimeout(function () { fetchProperties(q); }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!document.getElementById('prop-search-wrap').contains(e.target)) {
                propResults.classList.add('hidden');
            }
        });

        // Pré-remplir le label si property_id est déjà défini (via URL ou old())
        var prefilledId = propHidden.value ? parseInt(propHidden.value) : null;
        if (prefilledId) {
            fetch('/contracts/search-properties?q=', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var match = data.find(function (item) { return item.id === prefilledId; });
                if (match) {
                    propSearch.value = match.label;
                    if (rentInput && !rentInput.value && match.monthly_rent) {
                        rentInput.value = match.monthly_rent;
                    }
                }
            });
        }
    }

    // ── Navigation sections ───────────────────────────────────────────────
    const sections = ['section-1', 'section-2', 'section-3', 'section-4'];
    let currentIdx = 0;

    function showSection(idx) {
        currentIdx = Math.max(0, Math.min(idx, sections.length - 1));
        document.querySelectorAll('.section-content').forEach((el, i) => {
            el.classList.toggle('hidden', i !== currentIdx);
        });
        document.querySelectorAll('.section-nav').forEach((btn, i) => {
            const isActive = i === currentIdx;
            btn.classList.toggle('active', isActive);
            btn.querySelector('span:first-child').className = 'w-6 h-6 rounded-md flex items-center justify-center text-[11px] font-bold flex-shrink-0 ' + (isActive ? 'bg-primary-100 text-primary-700' : 'bg-slate-200 text-slate-500');
            btn.querySelector('span:last-child').className = isActive ? 'text-slate-800' : 'text-slate-500';
        });
        document.getElementById('btn-prev').classList.toggle('hidden', currentIdx === 0);
        document.getElementById('btn-next').classList.toggle('hidden', currentIdx === sections.length - 1);
        document.getElementById('btn-submit').classList.toggle('hidden', currentIdx !== sections.length - 1);
    }

    document.querySelectorAll('.section-nav').forEach((btn, i) => {
        btn.addEventListener('click', () => showSection(i));
    });
    document.getElementById('btn-prev').addEventListener('click', () => showSection(currentIdx - 1));
    document.getElementById('btn-next').addEventListener('click', () => showSection(currentIdx + 1));
    showSection(0);
})();
</script>

<style>
.section-nav.active { background: var(--color-primary-50); }
.section-nav:hover:not(.active) { background: rgb(248 250 252); }
</style>
@endsection
