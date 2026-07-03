@extends('layouts.app')

@section('title', 'Nouveau litige')
@section('page-title', 'Nouveau litige')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouveau litige</h2>
            <p class="page-subtitle">Declarez un litige et suivez son evolution</p>
        </div>
    </header>

    <div class="card-panel overflow-hidden">
        <form action="{{ route('litiges.store') }}" method="POST" class="flex flex-col lg:flex-row min-h-[400px]">
            @csrf

            <nav class="lg:w-56 flex-shrink-0 border-b lg:border-b-0 lg:border-r border-slate-100 bg-slate-50/50">
                <div class="flex lg:flex-col overflow-x-auto lg:overflow-x-visible p-3 lg:py-5 gap-1">
                    <button type="button" data-section="section-1" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors active">
                        <span class="w-6 h-6 rounded-md bg-primary-100 text-primary-700 flex items-center justify-center text-[11px] font-bold flex-shrink-0">1</span>
                        <span class="text-slate-800">Contexte</span>
                    </button>
                    <button type="button" data-section="section-2" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">2</span>
                        <span class="text-slate-500">Details</span>
                    </button>
                    <button type="button" data-section="section-3" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">3</span>
                        <span class="text-slate-500">Couts</span>
                    </button>
                    <button type="button" data-section="section-4" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">4</span>
                        <span class="text-slate-500">Pertes</span>
                    </button>
                    <button type="button" data-section="section-5" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">5</span>
                        <span class="text-slate-500">Suivi</span>
                    </button>
                </div>
            </nav>

            <div class="flex-1 overflow-y-auto">
                <div class="p-5 lg:p-6 space-y-6">
                    <section id="section-1" class="section-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Contrat (optionnel)</label>
                                <select name="contract_id" id="contract_id" class="input-modern">
                                    <option value="">— Aucun —</option>
                                    @foreach($contracts as $c)
                                        <option value="{{ $c->id }}" data-tenant="{{ $c->tenant_id }}" data-property="{{ $c->property_id }}" data-owner="{{ $c->owner_id }}">{{ $c->contract_number }} - {{ $c->tenant ? trim($c->tenant->first_name . ' ' . $c->tenant->last_name) : '—' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Reference</label>
                                <input type="text" name="reference" value="{{ old('reference') }}" placeholder="Ex: LIT-2026-001" class="input-modern">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Personnes concernees</label>
                                <input type="text" name="personnes_concernées" value="{{ old('personnes_concernées') }}" placeholder="Nom du locataire / contractuel" class="input-modern">
                                @error('personnes_concernées')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Lieu d'intervention</label>
                                <input type="text" name="lieu_intervention" value="{{ old('lieu_intervention') }}" class="input-modern">
                            </div>
                        </div>
                    </section>

                    <section id="section-2" class="section-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Type de contrat</label>
                                <select name="type_contrat" class="input-modern">
                                    <option value="">— Choisir —</option>
                                    @foreach(\App\Models\Litige::typesContrat() as $k => $v)
                                        <option value="{{ $k }}" {{ old('type_contrat') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Bailleur concerne</label>
                                <select name="owner_id" class="input-modern">
                                    <option value="">— Choisir —</option>
                                    @foreach($owners as $o)
                                        <option value="{{ $o->id }}" {{ old('owner_id') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Nature du litige</label>
                                <select name="nature_litige" class="input-modern">
                                    <option value="">— Choisir —</option>
                                    @foreach(\App\Models\Litige::naturesLitige() as $k => $v)
                                        <option value="{{ $k }}" {{ old('nature_litige') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Explication detaillee</label>
                                <textarea name="description" rows="4" class="input-modern">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </section>

                    <section id="section-3" class="section-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Frais huissier (FCFA)</label>
                                <input type="number" name="couts_frais_huissier" value="{{ old('couts_frais_huissier') }}" step="0.01" min="0" class="input-modern">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Honoraires avocat (FCFA)</label>
                                <input type="number" name="couts_honoraires_avocat" value="{{ old('couts_honoraires_avocat') }}" step="0.01" min="0" class="input-modern">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Reparation (FCFA)</label>
                                <input type="number" name="couts_frais_reparation" value="{{ old('couts_frais_reparation') }}" step="0.01" min="0" class="input-modern">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Dedommagement (FCFA)</label>
                                <input type="number" name="couts_dedommagement" value="{{ old('couts_dedommagement') }}" step="0.01" min="0" class="input-modern">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Transport (FCFA)</label>
                                <input type="number" name="couts_transport" value="{{ old('couts_transport') }}" step="0.01" min="0" class="input-modern">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Autres (FCFA)</label>
                                <input type="number" name="couts_autres" value="{{ old('couts_autres') }}" step="0.01" min="0" class="input-modern">
                            </div>
                        </div>
                    </section>

                    <section id="section-4" class="section-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Loyer impaye (FCFA)</label>
                                <input type="number" name="pertes_loyer_impaye" value="{{ old('pertes_loyer_impaye') }}" step="0.01" min="0" class="input-modern">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Charges non recouvrees (FCFA)</label>
                                <input type="number" name="pertes_charges_non_recouvrees" value="{{ old('pertes_charges_non_recouvrees') }}" step="0.01" min="0" class="input-modern">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Risque perte locataire (FCFA)</label>
                                <input type="number" name="pertes_risque_perte_locataire" value="{{ old('pertes_risque_perte_locataire') }}" step="0.01" min="0" class="input-modern">
                            </div>
                        </div>
                    </section>

                    <section id="section-5" class="section-content hidden">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Suivi / Commentaires</label>
                                <textarea name="suivi_commentaires" rows="4" placeholder="Reglement amiable, decision judiciaire, mediation..." class="input-modern">{{ old('suivi_commentaires') }}</textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut</label>
                                    <select name="statut" class="input-modern">
                                        <option value="en_cours" {{ old('statut', 'en_cours') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                        <option value="regle" {{ old('statut') == 'regle' ? 'selected' : '' }}>Regle</option>
                                        <option value="cloture" {{ old('statut') == 'cloture' ? 'selected' : '' }}>Cloture</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Date de debut</label>
                                    <input type="date" name="date_debut" value="{{ old('date_debut', now()->format('Y-m-d')) }}" class="input-modern">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Date de fin</label>
                                    <input type="date" name="date_fin" value="{{ old('date_fin') }}" class="input-modern">
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="flex items-center justify-between gap-3 px-5 py-4 border-t border-slate-100">
                    <a href="{{ route('litiges.index') }}" class="btn-secondary">Annuler</a>
                    <div class="flex gap-2">
                        <button type="button" id="btn-prev" class="hidden btn-secondary">Precedent</button>
                        <button type="button" id="btn-next" class="btn-secondary">Suivant</button>
                        <button type="submit" id="btn-submit" class="hidden btn-primary">Enregistrer</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const sections = ['section-1', 'section-2', 'section-3', 'section-4', 'section-5'];
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
