@extends('layouts.app')

@section('title', $documentTemplate->name)
@section('page-title', $documentTemplate->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">{{ $documentTemplate->name }}</h2>
            <p class="page-subtitle">
                @php
                    $categoryLabels = [
                        'contract' => 'Contrats', 'termination' => 'Resiliations', 'amendment' => 'Avenants',
                        'notification' => 'Notifications', 'legal' => 'Documents juridiques', 'receipt' => 'Recus',
                        'option' => 'Options', 'sale' => 'Ventes', 'management' => 'Gestion', 'other' => 'Autres',
                    ];
                @endphp
                {{ $categoryLabels[$documentTemplate->category] ?? $documentTemplate->category }} — {{ $documentTemplate->usage_count }} utilisation(s)
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('document-templates.generate', $documentTemplate) }}" class="btn-primary">Generer</a>
            @if($documentTemplate->canBeEdited())
                <a href="{{ route('document-templates.edit', $documentTemplate) }}" class="btn-secondary">Modifier</a>
            @endif
        </div>
    </header>

    @if($documentTemplate->is_system)
        <div class="bg-primary-50 border border-primary-200 rounded-lg px-4 py-2">
            <p class="text-xs font-medium text-primary-700">Template systeme</p>
        </div>
    @endif

    @if($documentTemplate->description)
    <div class="card-panel">
        <div class="card-panel-body">
            <p class="text-sm text-slate-700">{{ $documentTemplate->description }}</p>
        </div>
    </div>
    @endif

    @if(count($availableVariables) > 0)
    <div class="card-panel">
        <div class="card-panel-header">Variables disponibles</div>
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach($availableVariables as $variable)
                    <div class="px-2.5 py-1.5 bg-slate-50 rounded-md">
                        <code class="text-xs font-mono text-primary-600">@{{ {{ $variable }} }}</code>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <a href="{{ route('document-templates.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-700 flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Retour a la liste
    </a>
</div>
@endsection
