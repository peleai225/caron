@extends('layouts.app')

@section('title', 'Résultat OCR')
@section('page-title', 'Texte Extrait')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Texte extrait</h2>
            <p class="page-subtitle">Fichier source: {{ $originalFile }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="copyToClipboard()" class="btn-secondary">Copier</button>
            <a href="{{ route('ocr.download', $textPath) }}" class="btn-primary">Télécharger</a>
        </div>
    </header>

    <div class="card-panel">
        <div class="card-panel-body">
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100">
                <pre id="extracted-text" class="whitespace-pre-wrap text-xs text-slate-700 font-mono leading-relaxed">{{ $text }}</pre>
            </div>
        </div>
    </div>

    <a href="{{ route('ocr.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-slate-700">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Nouvelle extraction
    </a>
</div>

<script>
function copyToClipboard() {
    const text = document.getElementById('extracted-text').textContent;
    navigator.clipboard.writeText(text).then(function() {
        alert('Texte copié dans le presse-papiers !');
    });
}
</script>
@endsection
