@extends('layouts.app')

@section('title', 'Modifier le bien')
@section('page-title', 'Modifier le bien')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Modifier le bien</h2>
            <p class="page-subtitle">{{ $property->address ?? $property->designation ?? 'Bien #'.$property->id }}</p>
        </div>
        <a href="{{ route('properties.show', $property) }}" class="btn-secondary">Voir le bien</a>
    </header>

    <div class="card-panel">
        <form action="{{ route('properties.update', $property) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-panel-body space-y-6">
                <!-- Proprietaire -->
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Proprietaire</label>
                    <select name="owner_id" class="input-modern">
                        <option value="">Aucun proprietaire</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" {{ $property->owner_id == $owner->id ? 'selected' : '' }}>{{ $owner->name }}</option>
                        @endforeach
                    </select>
                    @error('owner_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Hierarchie -->
                <div class="space-y-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider pt-2 border-t border-slate-100">Hierarchie</h3>
                    <div>
                        <label for="parent_id" class="block text-xs font-medium text-slate-600 mb-1.5">Immeuble parent</label>
                        <select id="parent_id" name="parent_id" class="input-modern">
                            <option value="">— Bien autonome —</option>
                            @foreach($buildings ?? [] as $b)
                                <option value="{{ $b->id }}" {{ old('parent_id', $property->parent_id) == $b->id ? 'selected' : '' }}>{{ $b->address }} - {{ $b->city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="unit-fields-edit" class="{{ !$property->parent_id ? 'hidden' : '' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-lg border border-slate-100">
                            <div>
                                <label for="designation" class="block text-xs font-medium text-slate-600 mb-1.5">Designation <span class="text-red-500">*</span></label>
                                <input type="text" id="designation" name="designation" value="{{ old('designation', $property->designation) }}" class="input-modern" placeholder="Ex: Studio A, Apt 3B">
                                @error('designation')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="unit_type" class="block text-xs font-medium text-slate-600 mb-1.5">Type d'unite</label>
                                <select id="unit_type" name="unit_type" class="input-modern">
                                    <option value="">— Choisir —</option>
                                    @foreach(\App\Models\Property::unitTypes() as $k => $v)
                                        <option value="{{ $k }}" {{ old('unit_type', $property->unit_type) == $k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                                @error('unit_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Type & Statut -->
                <div class="space-y-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider pt-2 border-t border-slate-100">Type et statut</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Type</label>
                            <select name="type" required class="input-modern">
                                <option value="maison" {{ $property->type == 'maison' ? 'selected' : '' }}>Maison</option>
                                <option value="immeuble" {{ $property->type == 'immeuble' ? 'selected' : '' }}>Immeuble</option>
                                <option value="boutique" {{ $property->type == 'boutique' ? 'selected' : '' }}>Boutique</option>
                                <option value="terrain" {{ $property->type == 'terrain' ? 'selected' : '' }}>Terrain</option>
                            </select>
                            @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Statut</label>
                            <select name="status" required class="input-modern">
                                <option value="libre" {{ $property->status == 'libre' ? 'selected' : '' }}>Libre</option>
                                <option value="occupe" {{ $property->status == 'occupe' ? 'selected' : '' }}>Occupe</option>
                                <option value="maintenance" {{ $property->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Adresse -->
                <div id="address-block-edit" class="space-y-4 {{ $property->parent_id ? 'hidden' : '' }}">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider pt-2 border-t border-slate-100">Localisation</h3>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Adresse</label>
                        <textarea name="address" id="address-edit" rows="2" class="input-modern">{{ old('address', $property->address) }}</textarea>
                        @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Ville</label>
                            <input type="text" name="city" id="city-edit" value="{{ old('city', $property->city) }}" class="input-modern">
                            @error('city')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Quartier</label>
                            <input type="text" name="neighborhood" value="{{ old('neighborhood', $property->neighborhood) }}" class="input-modern">
                            @error('neighborhood')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="space-y-4">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider pt-2 border-t border-slate-100">Details</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Chambres</label>
                            <input type="number" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}" min="0" class="input-modern">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Salles de bain</label>
                            <input type="number" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms) }}" min="0" class="input-modern">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Surface (m2)</label>
                            <input type="number" name="surface" value="{{ old('surface', $property->surface) }}" step="0.01" min="0" class="input-modern">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Loyer (FCFA)</label>
                            <input type="number" name="monthly_rent" value="{{ old('monthly_rent', $property->monthly_rent) }}" step="0.01" min="0" class="input-modern">
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Description</label>
                    <textarea name="description" rows="3" class="input-modern">{{ old('description', $property->description) }}</textarea>
                    @error('description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <a href="{{ route('properties.show', $property) }}" class="btn-secondary">Annuler</a>
                    <button type="button" onclick="document.getElementById('modal-delete').classList.remove('hidden')" class="btn-danger">Supprimer</button>
                </div>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete modal -->
<div id="modal-delete" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('modal-delete').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-xl shadow-2xl max-w-sm w-full p-5">
            <h3 class="text-sm font-semibold text-slate-800 mb-1">Supprimer ce bien ?</h3>
            <p class="text-xs text-slate-500 mb-4">Cette action est irreversible. Les contrats et paiements lies seront impactes.</p>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('modal-delete').classList.add('hidden')" class="btn-secondary">Annuler</button>
                <form action="{{ route('properties.destroy', $property) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('parent_id').addEventListener('change', function() {
    const isUnit = !!this.value;
    document.getElementById('unit-fields-edit').classList.toggle('hidden', !isUnit);
    document.getElementById('address-block-edit').classList.toggle('hidden', isUnit);
    document.getElementById('address-edit').toggleAttribute('required', !isUnit);
    document.getElementById('city-edit').toggleAttribute('required', !isUnit);
});
</script>
@endsection
