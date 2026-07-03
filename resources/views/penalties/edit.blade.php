@extends('layouts.app')

@section('title', 'Modifier la pénalité')
@section('page-title', 'Modifier la pénalité')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Modifier la pénalité</h2>
            <p class="page-subtitle">Correction d'une pénalité de retard</p>
        </div>
        <a href="{{ route('penalties.show', $penalty) }}" class="btn-secondary">Retour</a>
    </header>

    @if($errors->any())
    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3">
        <ul class="text-sm text-red-700 space-y-1 list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card-panel">
        {{-- Contexte (lecture seule) --}}
        <div class="card-panel-body border-b border-slate-100 pb-5 mb-5 space-y-2">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Échéance concernée</p>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-xs text-slate-400">Contrat</span>
                    <p class="font-medium text-slate-800">{{ $penalty->paymentSchedule->contract->contract_number ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400">Locataire</span>
                    <p class="font-medium text-slate-800">{{ $penalty->paymentSchedule->contract->tenant?->full_name ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400">Bien</span>
                    <p class="font-medium text-slate-800">{{ $penalty->paymentSchedule->contract->property?->title ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400">Échéance</span>
                    <p class="font-medium text-slate-800">{{ $penalty->paymentSchedule->due_date->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('penalties.update', $penalty) }}" class="divide-y divide-slate-100">
            @csrf
            @method('PUT')
            <div class="card-panel-body space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" min="0" step="1" required
                            value="{{ old('amount', $penalty->amount) }}" class="input-modern" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Taux appliqué (%)</label>
                        <input type="number" name="rate" min="0" max="100" step="0.01"
                            value="{{ old('rate', $penalty->rate) }}" class="input-modern" placeholder="0">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Jours de retard <span class="text-red-500">*</span></label>
                    <input type="number" name="days_late" min="1" required
                        value="{{ old('days_late', $penalty->days_late) }}" class="input-modern" placeholder="ex: 15">
                </div>

            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <a href="{{ route('penalties.show', $penalty) }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>
@endsection
