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
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Proprietaire</label>
                                <select name="owner_id" class="input-modern">
                                    <option value="">Aucun proprietaire</option>
                                    @foreach($owners ?? [] as $owner)
                                        <option value="{{ $owner->id }}" {{ old('owner_id', $contract->owner_id) == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Loyer mensuel (FCFA) <span class="text-red-500">*</span></label>
                                <input type="number" name="rent_amount" value="{{ old('rent_amount', $contract->rent_amount) }}" step="0.01" min="0" required class="input-modern">
                                @error('rent_amount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Caution (FCFA)</label>
                                <input type="number" name="deposit" value="{{ old('deposit', $contract->deposit) }}" step="0.01" min="0" class="input-modern">
                            </div>
                        </div>
                    </section>

                    <section id="section-2" class="section-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Date de debut <span class="text-red-500">*</span></label>
                                <input type="date" name="start_date" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required class="input-modern">
                                @error('start_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Date de fin <span class="text-red-500">*</span></label>
                                <input type="date" name="end_date" value="{{ old('end_date', $contract->end_date->format('Y-m-d')) }}" required class="input-modern">
                                @error('end_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Frequence <span class="text-red-500">*</span></label>
                                <select name="payment_frequency" required class="input-modern">
                                    <option value="monthly" {{ $contract->payment_frequency == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                                    <option value="quarterly" {{ $contract->payment_frequency == 'quarterly' ? 'selected' : '' }}>Trimestriel</option>
                                    <option value="yearly" {{ $contract->payment_frequency == 'yearly' ? 'selected' : '' }}>Annuel</option>
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
                                    <option value="draft" {{ $contract->status == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                    <option value="active" {{ $contract->status == 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="expired" {{ $contract->status == 'expired' ? 'selected' : '' }}>Expire</option>
                                    <option value="terminated" {{ $contract->status == 'terminated' ? 'selected' : '' }}>Resilie</option>
                                </select>
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
