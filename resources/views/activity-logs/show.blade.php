@extends('layouts.app')

@section('title', 'Détails du Log')
@section('page-title', 'Détails du Log d\'Activité')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Détails du log d'activité</h2>
            <p class="page-subtitle">{{ $activityLog->created_at->format('d/m/Y à H:i:s') }}</p>
        </div>
        <a href="{{ route('activity-logs.index') }}" class="btn-secondary">Retour</a>
    </header>

    <div class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Date</p>
                    <p class="text-sm font-medium text-slate-900">{{ $activityLog->created_at->format('d/m/Y à H:i:s') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Utilisateur</p>
                    <p class="text-sm font-medium text-slate-900">{{ $activityLog->causer->name ?? 'Système' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Action</p>
                    @php
                        $eventColors = [
                            'created' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'updated' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                            'deleted' => 'bg-red-50 text-red-700 ring-red-600/20',
                        ];
                        $color = $eventColors[$activityLog->event] ?? 'bg-slate-50 text-slate-700 ring-slate-600/20';
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-md ring-1 ring-inset {{ $color }}">
                        {{ ucfirst($activityLog->event) }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Type d'objet</p>
                    <p class="text-sm font-medium text-slate-900">{{ class_basename($activityLog->subject_type) }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($activityLog->subject)
    <div class="card-panel">
        <div class="card-panel-header">Objet concerné</div>
        <div class="card-panel-body">
            <div class="bg-slate-50 rounded-lg p-4">
                <pre class="text-xs text-slate-700 whitespace-pre-wrap font-mono">{{ json_encode($activityLog->subject->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>
    @endif

    @if($activityLog->changes)
    <div class="card-panel">
        <div class="card-panel-header">Modifications</div>
        <div class="card-panel-body space-y-4">
            @if(isset($activityLog->changes['attributes']))
            <div>
                <p class="text-xs font-medium text-slate-600 mb-2">Nouvelles valeurs</p>
                <div class="bg-emerald-50 rounded-lg p-4">
                    <pre class="text-xs text-slate-700 whitespace-pre-wrap font-mono">{{ json_encode($activityLog->changes['attributes'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif
            @if(isset($activityLog->changes['old']))
            <div>
                <p class="text-xs font-medium text-slate-600 mb-2">Anciennes valeurs</p>
                <div class="bg-red-50 rounded-lg p-4">
                    <pre class="text-xs text-slate-700 whitespace-pre-wrap font-mono">{{ json_encode($activityLog->changes['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
