@extends('layouts.app')

@section('title', 'Reconnaissance OCR')
@section('page-title', 'Extraction de Texte (OCR)')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Extraction de texte (OCR)</h2>
            <p class="page-subtitle">Extraire le texte d'une image ou PDF</p>
        </div>
    </header>

    @if(!$isAvailable)
    <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
        <p class="text-xs font-semibold text-amber-800 mb-1">Tesseract OCR non disponible</p>
        <p class="text-xs text-amber-700 mb-2">Tesseract OCR n'est pas installé ou n'est pas accessible sur votre système.</p>
        <div class="text-xs text-amber-700 space-y-0.5">
            <p><strong>Windows :</strong> Téléchargez depuis GitHub UB-Mannheim/tesseract</p>
            <p><strong>Linux :</strong> <code class="bg-amber-100 px-1.5 py-0.5 rounded text-[11px]">sudo apt-get install tesseract-ocr</code></p>
            <p><strong>macOS :</strong> <code class="bg-amber-100 px-1.5 py-0.5 rounded text-[11px]">brew install tesseract</code></p>
        </div>
    </div>
    @endif

    <div class="card-panel">
        <form action="{{ route('ocr.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Fichier (Image ou PDF) <span class="text-red-500">*</span></label>
                    <div class="flex justify-center px-6 py-8 border border-dashed border-slate-300 rounded-lg hover:border-primary-400 transition-colors">
                        <div class="text-center">
                            <svg class="mx-auto h-10 w-10 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="mt-2 flex text-xs text-slate-600">
                                <label for="file" class="relative cursor-pointer font-medium text-primary-600 hover:text-primary-700">
                                    <span>Télécharger un fichier</span>
                                    <input id="file" name="file" type="file" class="sr-only" accept="image/*,.pdf" required>
                                </label>
                                <p class="pl-1">ou glissez-déposez</p>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">PNG, JPG, PDF jusqu'à 10MB</p>
                        </div>
                    </div>
                    @error('file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="preprocess" value="1" checked class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-xs font-medium text-slate-700">Pré-traiter l'image</span>
                        </label>
                        <p class="text-[11px] text-slate-400 mt-1 ml-6">Améliore la qualité de l'OCR</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Langue</label>
                        <select name="language" class="input-modern">
                            <option value="fra+eng">Français + Anglais</option>
                            <option value="fra">Français</option>
                            <option value="eng">Anglais</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" {{ !$isAvailable ? 'disabled' : '' }} class="btn-primary disabled:opacity-50 disabled:cursor-not-allowed">Extraire le texte</button>
            </div>
        </form>
    </div>

    <div class="bg-primary-50 border border-primary-200 rounded-lg px-4 py-3">
        <p class="text-xs font-semibold text-primary-800 mb-1">Comment ça fonctionne ?</p>
        <ul class="text-xs text-primary-700 space-y-0.5 list-disc list-inside">
            <li>Uploadez une image (JPG, PNG) ou un PDF</li>
            <li>Le système utilise Tesseract OCR pour extraire le texte</li>
            <li>Le pré-traitement améliore la qualité de reconnaissance</li>
            <li>Le texte extrait peut être téléchargé ou copié</li>
        </ul>
    </div>
</div>
@endsection
