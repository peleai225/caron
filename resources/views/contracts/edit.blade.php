@extends('layouts.app')

@section('title', 'Modifier le contrat')
@section('page-title', 'Modifier le contrat')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Modifier le contrat</h2>
            <p class="page-subtitle">{{ $contract->contract_number }}</p>
        </div>
    </header>

    {{-- Résumé locataire / bien (lecture seule) --}}
    <div class="card-panel p-4 flex flex-col sm:flex-row gap-4 text-xs text-slate-600">
        <div class="flex items-start gap-3 flex-1">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="font-medium text-slate-400 uppercase tracking-wide text-[10px] mb-0.5">Locataire</p>
                <p class="font-semibold text-slate-900">{{ $contract->tenant?->full_name ?? '—' }}</p>
                <p class="text-slate-500">{{ $contract->tenant?->phone ?? '' }}</p>
            </div>
        </div>
        <div class="hidden sm:block w-px bg-slate-100"></div>
        <div class="flex items-start gap-3 flex-1">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <div>
                <p class="font-medium text-slate-400 uppercase tracking-wide text-[10px] mb-0.5">Bien</p>
                <p class="font-semibold text-slate-900">{{ $contract->property?->full_address ?? '—' }}{{ $contract->property?->designation ? ' · ' . $contract->property->designation : '' }}</p>
                <p class="text-slate-500">{{ $contract->property?->city ?? '' }}</p>
            </div>
        </div>
    </div>

    <div class="card-panel overflow-hidden">
        <form action="{{ route('contracts.update', $contract) }}" method="POST" class="flex flex-col lg:flex-row min-h-[400px]">
            @csrf
            @method('PUT')

            <nav class="lg:w-56 flex-shrink-0 border-b lg:border-b-0 lg:border-r border-slate-100 bg-slate-50/50">
                <div class="flex lg:flex-col overflow-x-auto lg:overflow-x-visible p-3 lg:py-5 gap-1">
                    <button type="button" data-section="section-1" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors active">
                        <span class="w-6 h-6 rounded-md bg-primary-100 text-primary-700 flex items-center justify-center text-[11px] font-bold flex-shrink-0">1</span>
                        <span class="text-slate-800">Conditions</span>
                    </button>
                    <button type="button" data-section="section-2" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">2</span>
                        <span class="text-slate-500">Dates et paiement</span>
                    </button>
                    <button type="button" data-section="section-3" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">3</span>
                        <span class="text-slate-500">Statut et notes</span>
                    </button>
                </div>
            </nav>

            <div class="flex-1 overflow-y-auto">
                <div class="p-5 lg:p-6 space-y-6">
                    <section id="section-1" class="section-content">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Type de contrat</label>
                                <select name="type_contrat" class="input-modern">
                                    <option value="">— Choisir —</option>
                                    @foreach(\App\Models\Contract::typesContrat() as $k => $v)
                                        <option value="{{ $k }}" {{ old('type_contrat', $contract->type_contrat) == $k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                                @error('type_contrat')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Propriétaire</label>
                                <select name="owner_id" class="input-modern">
                                    <option value="">Aucun propriétaire</option>
                                    @foreach($owners ?? [] as $owner)
                                        <option value="{{ $owner->id }}" {{ old('owner_id', $contract->owner_id) == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Loyer mensuel (FCFA) <span class="text-red-500">*</span></label>
                                <input type="number" name="rent_amount" value="{{ old('rent_amount', $contract->rent_amount) }}" step="1" min="0" required class="input-modern">
                                @error('rent_amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Caution (FCFA)</label>
                                <input type="number" name="deposit" value="{{ old('deposit', $contract->deposit) }}" step="1" min="0" class="input-modern">
                            </div>
                        </div>
                    </section>

                    <section id="section-2" class="section-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Date de début <span class="text-red-500">*</span></label>
                                <input type="date" name="start_date" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required class="input-modern">
                                @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Date de fin <span class="text-red-500">*</span></label>
                                <input type="date" name="end_date" value="{{ old('end_date', $contract->end_date?->format('Y-m-d')) }}" required class="input-modern">
                                @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Fréquence <span class="text-red-500">*</span></label>
                                <select name="payment_frequency" required class="input-modern">
                                    <option value="monthly" {{ old('payment_frequency', $contract->payment_frequency) == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                                    <option value="quarterly" {{ old('payment_frequency', $contract->payment_frequency) == 'quarterly' ? 'selected' : '' }}>Trimestriel</option>
                                    <option value="yearly" {{ old('payment_frequency', $contract->payment_frequency) == 'yearly' ? 'selected' : '' }}>Annuel</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Jour de paiement <span class="text-red-500">*</span></label>
                                <input type="number" name="payment_day" value="{{ old('payment_day', $contract->payment_day) }}" min="1" max="31" required class="input-modern">
                            </div>
                        </div>
                    </section>

                    <section id="section-3" class="section-content hidden">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut <span class="text-red-500">*</span></label>
                                <select name="status" required class="input-modern">
                                    <option value="draft" {{ old('status', $contract->status) == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                    <option value="active" {{ old('status', $contract->status) == 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="expired" {{ old('status', $contract->status) == 'expired' ? 'selected' : '' }}>Expiré</option>
                                    <option value="terminated" {{ old('status', $contract->status) == 'terminated' ? 'selected' : '' }}>Résilié</option>
                                </select>
                                @if($contract->status === 'draft')
                                <p class="mt-1 text-[11px] text-amber-600">Passer à "Actif" marquera automatiquement le bien comme occupé et enregistrera la date de signature.</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Notes</label>
                                <textarea name="notes" rows="4" class="input-modern">{{ old('notes', $contract->notes) }}</textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="flex items-center justify-between gap-3 px-5 py-4 border-t border-slate-100">
                    <a href="{{ route('contracts.show', $contract) }}" class="btn-secondary">Annuler</a>
                    <div class="flex gap-2">
                        <button type="button" id="btn-prev" class="hidden btn-secondary">Précédent</button>
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
    const sections = ['section-1', 'section-2', 'section-3'];
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
