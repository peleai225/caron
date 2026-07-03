@extends('layouts.app')

@section('title', 'Modifier le template')
@section('page-title', 'Modifier: ' . $documentTemplate->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Modifier le template</h2>
            <p class="page-subtitle">{{ $documentTemplate->name }}</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('document-templates.update', $documentTemplate) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom du template <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $documentTemplate->name) }}" required class="input-modern">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Categorie <span class="text-red-500">*</span></label>
                    <select name="category" required class="input-modern">
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category', $documentTemplate->category) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Description</label>
                    <textarea name="description" rows="3" class="input-modern">{{ old('description', $documentTemplate->description) }}</textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $documentTemplate->is_active) ? 'checked' : '' }} class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-xs font-medium text-slate-600">Template actif</span>
                    </label>
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('document-templates.show', $documentTemplate) }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
