@extends('layouts.app')

@section('title', 'Generer un document')
@section('page-title', 'Generer: ' . $documentTemplate->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Generer un document</h2>
            <p class="page-subtitle">{{ $documentTemplate->name }}</p>
        </div>
    </header>

    <div class="card-panel">
        <form action="{{ route('document-templates.store-generated', $documentTemplate) }}" method="POST">
            @csrf

            <div class="card-panel-body space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Format de sortie</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="format" value="docx" checked class="text-primary-600 focus:ring-primary-500">
                            <span class="text-xs text-slate-700">DOCX (Modifiable)</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="format" value="pdf" class="text-primary-600 focus:ring-primary-500">
                            <span class="text-xs text-slate-700">PDF (Archivage)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Contrat associe (optionnel)</label>
                    <select name="contract_id" id="contract-select" class="input-modern">
                        <option value="">Aucun contrat</option>
                        @foreach($contracts as $contractOption)
                            <option value="{{ $contractOption->id }}" {{ $contract && $contract->id === $contractOption->id ? 'selected' : '' }}>
                                {{ $contractOption->contract_number }} - {{ $contractOption->tenant->first_name }} {{ $contractOption->tenant->last_name }} - {{ $contractOption->property->address }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Selectionnez un contrat pour pre-remplir les variables</p>
                </div>

                @if(count($availableVariables) > 0)
                <div class="pt-2 border-t border-slate-100">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Variables du document</h3>
                    <div class="space-y-3">
                        @foreach($availableVariables as $variable)
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">
                                    {{ $variable }}
                                    <code class="text-[10px] text-slate-400 ml-1">@{{ {{ $variable }} }}</code>
                                </label>
                                <input type="text" name="variables[{{ $variable }}]" value="{{ $defaultVariables[$variable] ?? '' }}" class="input-modern" placeholder="Valeur pour {{ $variable }}">
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('document-templates.show', $documentTemplate) }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Generer</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('contract-select').addEventListener('change', function() {
    if (this.value) {
        window.location.href = '{{ route("document-templates.generate", $documentTemplate) }}?contract_id=' + this.value;
    }
});
</script>
@endsection
