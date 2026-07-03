@extends('layouts.app')

@section('title', 'Creer un contrat')
@section('page-title', 'Creer un contrat')

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
                        <span class="text-slate-500">Notes</span>
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
                                    <option value="">Selectionner...</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>{{ $tenant->full_name }} ({{ $tenant->phone }})</option>
                                    @endforeach
                                </select>
                                @error('tenant_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="property_id" class="block text-xs font-medium text-slate-600 mb-1.5">Bien immobilier <span class="text-red-500">*</span></label>
                                <select id="property_id" name="property_id" required class="input-modern searchable-select">
                                    <option value="">Selectionner...</option>
                                    @foreach($properties as $property)
                                        <option value="{{ $property->id }}" {{ old('property_id', request('property_id')) == $property->id ? 'selected' : '' }}>{{ $property->name }} — {{ $property->city }}</option>
                                    @endforeach
                                </select>
                                @error('property_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="owner_id" class="block text-xs font-medium text-slate-600 mb-1.5">Proprietaire</label>
                                <select id="owner_id" name="owner_id" class="input-modern searchable-select">
                                    <option value="">Selectionner (optionnel)...</option>
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
                                <input type="number" id="rent_amount" name="rent_amount" required value="{{ old('rent_amount') }}" min="0" step="0.01" class="input-modern" placeholder="150000">
                                @error('rent_amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="deposit" class="block text-xs font-medium text-slate-600 mb-1.5">Caution (FCFA)</label>
                                <input type="number" id="deposit" name="deposit" value="{{ old('deposit') }}" min="0" step="0.01" class="input-modern" placeholder="300000">
                                @error('deposit')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="start_date" class="block text-xs font-medium text-slate-600 mb-1.5">Date de debut <span class="text-red-500">*</span></label>
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
                                <label for="payment_frequency" class="block text-xs font-medium text-slate-600 mb-1.5">Frequence <span class="text-red-500">*</span></label>
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
                                <textarea id="notes" name="notes" rows="5" class="input-modern" placeholder="Conditions speciales, clauses particulieres...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="flex items-center justify-between gap-3 px-5 py-4 border-t border-slate-100">
                    <a href="{{ route('contracts.index') }}" class="btn-secondary">Annuler</a>
                    <div class="flex gap-2">
                        <button type="button" id="btn-prev" class="hidden btn-secondary">Precedent</button>
                        <button type="button" id="btn-next" class="btn-secondary">Suivant</button>
                        <button type="submit" id="btn-submit" class="hidden btn-primary">Creer le contrat</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
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
