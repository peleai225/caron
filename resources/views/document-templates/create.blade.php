@extends('layouts.app')

@section('title', 'Nouveau template')
@section('page-title', 'Nouveau template')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouveau template</h2>
            <p class="page-subtitle">Creez un modele de document</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('document-templates.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nom du template <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input-modern">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Categorie <span class="text-red-500">*</span></label>
                    <select name="category" required class="input-modern">
                        <option value="">Selectionner une categorie</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Description</label>
                    <textarea name="description" rows="3" class="input-modern">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Fichier .docx <span class="text-red-500">*</span></label>
                    <input type="file" name="file" accept=".docx" required class="input-modern text-xs">
                    <p class="text-[11px] text-slate-400 mt-1">Format accepte: .docx (max 10MB)</p>
                    @error('file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Pays</label>
                    <input type="text" name="country" value="{{ old('country', 'CI') }}" maxlength="2" class="input-modern">
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('document-templates.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Creer le template</button>
            </div>
        </form>
    </div>
</div>
@endsection
