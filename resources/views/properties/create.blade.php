@extends('layouts.app')

@section('title', 'Ajouter un bien')
@section('page-title', 'Ajouter un bien')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouveau bien immobilier</h2>
            <p class="page-subtitle">Renseignez les informations du bien</p>
        </div>
    </header>

    <div class="card-panel overflow-hidden">
        <form method="POST" action="{{ route('properties.store') }}" enctype="multipart/form-data" class="flex flex-col lg:flex-row min-h-[420px]">
            @csrf

            <nav class="lg:w-56 flex-shrink-0 border-b lg:border-b-0 lg:border-r border-slate-100 bg-slate-50/50">
                <div class="flex lg:flex-col overflow-x-auto lg:overflow-x-visible p-3 lg:py-5 gap-1">
                    <button type="button" data-section="section-0" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors active">
                        <span class="w-6 h-6 rounded-md bg-primary-100 text-primary-700 flex items-center justify-center text-[11px] font-bold flex-shrink-0">1</span>
                        <span class="text-slate-800">Proprietaire</span>
                    </button>
                    <button type="button" data-section="section-1" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">2</span>
                        <span class="text-slate-500">Type et contexte</span>
                    </button>
                    <button type="button" data-section="section-2" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">3</span>
                        <span class="text-slate-500">Localisation</span>
                    </button>
                    <button type="button" data-section="section-3" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">4</span>
                        <span class="text-slate-500">Details et prix</span>
                    </button>
                    <button type="button" data-section="section-4" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">5</span>
                        <span class="text-slate-500">Photos</span>
                    </button>
                    <button type="button" data-section="section-5" class="section-nav flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left w-full text-xs font-medium transition-colors">
                        <span class="w-6 h-6 rounded-md bg-slate-200 text-slate-500 flex items-center justify-center text-[11px] font-bold flex-shrink-0">6</span>
                        <span class="text-slate-500">Description</span>
                    </button>
                </div>
            </nav>

            <div class="flex-1 overflow-y-auto">
                <div class="p-5 lg:p-6 space-y-6">
                    <section id="section-0" class="section-content">
                        <div class="p-4 bg-primary-50 rounded-lg border border-primary-100 mb-5">
                            <p class="text-xs text-primary-800 font-medium">Les biens sont geres pour le compte des proprietaires. Selectionnez d'abord le proprietaire.</p>
                        </div>
                        <div class="max-w-md">
                            <label for="owner_id" class="block text-xs font-medium text-slate-600 mb-1.5">Proprietaire <span class="text-red-500">*</span></label>
                            <select id="owner_id" name="owner_id" required class="input-modern">
                                <option value="">— Selectionner —</option>
                                @foreach($owners ?? [] as $o)
                                    <option value="{{ $o->id }}" {{ old('owner_id') == $o->id ? 'selected' : '' }}>{{ $o->name }} {{ $o->email ? '(' . $o->email . ')' : '' }}</option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1.5">Aucun proprietaire ? <a href="{{ route('owners.create') }}?redirect=properties.create" class="text-primary-600 hover:underline font-medium">Ajouter</a></p>
                            @error('owner_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </section>

                    <section id="section-1" class="section-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="parent_id" class="block text-xs font-medium text-slate-600 mb-1.5">Immeuble parent (optionnel)</label>
                                <select id="parent_id" name="parent_id" class="input-modern">
                                    <option value="">— Bien autonome —</option>
                                    @foreach($buildings ?? [] as $b)
                                        <option value="{{ $b->id }}" {{ old('parent_id') == $b->id ? 'selected' : '' }}>{{ $b->address }} - {{ $b->city }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-slate-400 mt-1">Selectionnez un immeuble pour ajouter une unite</p>
                            </div>
                            <div id="unit-fields" class="md:col-span-2 hidden">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-lg border border-slate-100">
                                    <div>
                                        <label for="designation" class="block text-xs font-medium text-slate-600 mb-1.5">Designation <span class="text-red-500">*</span></label>
                                        <input type="text" id="designation" name="designation" value="{{ old('designation') }}" class="input-modern" placeholder="Ex: Studio A, Apt 3B">
                                        @error('designation')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label for="unit_type" class="block text-xs font-medium text-slate-600 mb-1.5">Type d'unite <span class="text-red-500">*</span></label>
                                        <select id="unit_type" name="unit_type" class="input-modern">
                                            <option value="">— Choisir —</option>
                                            @foreach(\App\Models\Property::unitTypes() as $k => $v)
                                                <option value="{{ $k }}" {{ old('unit_type') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                            @endforeach
                                        </select>
                                        @error('unit_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                            <div id="owner-inherit-note" class="md:col-span-2 hidden">
                                <p class="text-xs text-slate-500 bg-slate-50 p-3 rounded-lg">Le proprietaire sera celui de l'immeuble parent.</p>
                            </div>
                            <div>
                                <label for="type" class="block text-xs font-medium text-slate-600 mb-1.5">Type de bien <span class="text-red-500">*</span></label>
                                <select id="type" name="type" required class="input-modern">
                                    <option value="">Selectionner...</option>
                                    <option value="maison">Maison</option>
                                    <option value="immeuble">Immeuble</option>
                                    <option value="boutique">Boutique</option>
                                    <option value="terrain">Terrain</option>
                                </select>
                                @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="status" class="block text-xs font-medium text-slate-600 mb-1.5">Statut <span class="text-red-500">*</span></label>
                                <select id="status" name="status" required class="input-modern">
                                    <option value="">Selectionner...</option>
                                    <option value="libre">Libre</option>
                                    <option value="occupe">Occupe</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                                @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </section>

                    <section id="section-2" class="section-content hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div id="address-block" class="md:col-span-2">
                                <label for="address" class="block text-xs font-medium text-slate-600 mb-1.5">Adresse <span class="text-red-500">*</span></label>
                                <input type="text" id="address" name="address" value="{{ old('address') }}" class="input-modern" placeholder="Quartier, Ville">
                                @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div id="city-block">
                                <label for="city" class="block text-xs font-medium text-slate-600 mb-1.5">Ville <span class="text-red-500">*</span></label>
                                <input type="text" id="city" name="city" required value="{{ old('city') }}" class="input-modern" placeholder="Abidjan">
                                @error('city')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="neighborhood" class="block text-xs font-medium text-slate-600 mb-1.5">Quartier</label>
                                <input type="text" id="neighborhood" name="neighborhood" value="{{ old('neighborhood') }}" class="input-modern" placeholder="Yopougon">
                                @error('neighborhood')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </section>

                    <section id="section-3" class="section-content hidden">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label for="bedrooms" class="block text-xs font-medium text-slate-600 mb-1.5">Chambres</label>
                                <input type="number" id="bedrooms" name="bedrooms" value="{{ old('bedrooms') }}" min="0" class="input-modern">
                            </div>
                            <div>
                                <label for="bathrooms" class="block text-xs font-medium text-slate-600 mb-1.5">Salles de bain</label>
                                <input type="number" id="bathrooms" name="bathrooms" value="{{ old('bathrooms') }}" min="0" class="input-modern">
                            </div>
                            <div>
                                <label for="surface" class="block text-xs font-medium text-slate-600 mb-1.5">Surface (m2)</label>
                                <input type="number" id="surface" name="surface" value="{{ old('surface') }}" min="0" step="0.01" class="input-modern">
                            </div>
                            <div>
                                <label for="monthly_rent" class="block text-xs font-medium text-slate-600 mb-1.5">Loyer (FCFA)</label>
                                <input type="number" id="monthly_rent" name="monthly_rent" value="{{ old('monthly_rent') }}" min="0" step="1" class="input-modern" placeholder="0">
                            </div>
                        </div>
                    </section>

                    <section id="section-4" class="section-content hidden">
                        <div class="border-2 border-dashed border-slate-200 rounded-lg p-6 text-center hover:border-primary-300 hover:bg-slate-50/50 transition-colors">
                            <input type="file" id="photos" name="photos[]" multiple accept="image/*" class="hidden" onchange="previewImages(this, 'photos-preview')">
                            <label for="photos" class="cursor-pointer block">
                                <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-xs font-medium text-slate-600">Cliquez pour telecharger des photos</p>
                                <p class="text-[11px] text-slate-400 mt-1">PNG, JPG — 5MB max par image</p>
                            </label>
                        </div>
                        <div id="photos-preview" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
                    </section>

                    <section id="section-5" class="section-content hidden">
                        <label for="description" class="block text-xs font-medium text-slate-600 mb-1.5">Description</label>
                        <textarea id="description" name="description" rows="5" class="input-modern" placeholder="Description du bien...">{{ old('description') }}</textarea>
                    </section>
                </div>

                <div class="flex items-center justify-between gap-3 px-5 py-4 border-t border-slate-100">
                    <a href="{{ route('properties.index') }}" class="btn-secondary">Annuler</a>
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
    const sections = ['section-0', 'section-1', 'section-2', 'section-3', 'section-4', 'section-5'];
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

document.getElementById('parent_id').addEventListener('change', function() {
    const isUnit = !!this.value;
    document.getElementById('unit-fields').classList.toggle('hidden', !isUnit);
    document.getElementById('address-block').classList.toggle('hidden', isUnit);
    document.getElementById('city-block').classList.toggle('hidden', isUnit);
    document.getElementById('owner-inherit-note').classList.toggle('hidden', !isUnit);
    const ownerSelect = document.getElementById('owner_id');
    if (isUnit) {
        document.getElementById('address').removeAttribute('required');
        document.getElementById('city').removeAttribute('required');
        ownerSelect.removeAttribute('required');
    } else {
        document.getElementById('address').setAttribute('required', 'required');
        document.getElementById('city').setAttribute('required', 'required');
        ownerSelect.setAttribute('required', 'required');
    }
});
if (document.getElementById('parent_id').value) {
    document.getElementById('parent_id').dispatchEvent(new Event('change'));
}

function previewImages(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="w-full h-24 object-cover rounded-lg border border-slate-200"><button type="button" onclick="removeImagePreview(this, \'photos\')" class="absolute top-1 right-1 bg-slate-900/60 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">×</button>';
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

function removeImagePreview(button, inputId) {
    const input = document.getElementById(inputId);
    const dt = new DataTransfer();
    const files = Array.from(input.files);
    const previewDiv = button.closest('div');
    const index = Array.from(previewDiv.parentElement.children).indexOf(previewDiv);
    files.splice(index, 1);
    files.forEach(file => dt.items.add(file));
    input.files = dt.files;
    previewDiv.remove();
}
</script>

<style>
.section-nav.active { background: var(--color-primary-50); }
.section-nav:hover:not(.active) { background: rgb(248 250 252); }
</style>
@endsection
