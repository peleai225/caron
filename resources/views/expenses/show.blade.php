@extends('layouts.app')

@section('title', 'Details de la depense')
@section('page-title', 'Details de la depense')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">{{ $expense->type }}</h2>
            <p class="page-subtitle">Depense du {{ $expense->expense_date->format('d/m/Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('expenses.edit', $expense) }}" class="btn-primary">Modifier</a>
            <a href="{{ route('expenses.index') }}" class="btn-secondary">Retour</a>
        </div>
    </header>

    <div class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Type</p>
                    <p class="text-sm font-semibold text-slate-900">{{ $expense->type }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Montant</p>
                    <p class="text-sm font-semibold text-red-600">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Date</p>
                    <p class="text-sm font-medium text-slate-900">{{ $expense->expense_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Bien immobilier</p>
                    @if($expense->property)
                        <a href="{{ route('properties.show', $expense->property) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">{{ $expense->property->address }}</a>
                    @else
                        <p class="text-sm text-slate-400">Aucun bien associe</p>
                    @endif
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-slate-500 mb-0.5">Description</p>
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $expense->description }}</p>
                </div>
                @if($expense->receipt_path)
                <div class="md:col-span-2">
                    <p class="text-xs text-slate-500 mb-1.5">Justificatif</p>
                    <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-medium text-primary-600 hover:text-primary-700 px-3 py-1.5 bg-primary-50 rounded-md">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Voir le justificatif
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
