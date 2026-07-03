@extends('layouts.app')

@section('title', 'Nouvelle pénalité')
@section('page-title', 'Nouvelle pénalité')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Nouvelle pénalité</h2>
            <p class="page-subtitle">Créer manuellement une pénalité de retard</p>
        </div>
        <a href="{{ route('penalties.index') }}" class="btn-secondary">Retour</a>
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
        <form method="POST" action="{{ route('penalties.store') }}" class="divide-y divide-slate-100">
            @csrf
            <div class="card-panel-body space-y-4">

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Échéance concernée <span class="text-red-500">*</span></label>
                    <select name="payment_schedule_id" required class="input-modern">
                        <option value="">Sélectionner une échéance</option>
                        @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}" {{ old('payment_schedule_id') == $schedule->id ? 'selected' : '' }}>
                                {{ $schedule->contract->contract_number ?? '—' }}
                                — {{ $schedule->contract->tenant?->full_name ?? '' }}
                                — Échéance {{ $schedule->due_date->format('d/m/Y') }}
                                ({{ number_format($schedule->amount, 0, ',', ' ') }} F)
                            </option>
                        @endforeach
                    </select>
                    @if($schedules->isEmpty())
                        <p class="text-xs text-slate-500 mt-1">Aucune échéance éligible (sans pénalité impayée existante).</p>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" min="0" step="1" required
                            value="{{ old('amount') }}" class="input-modern" placeholder="0">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Taux appliqué (%)</label>
                        <input type="number" name="rate" min="0" max="100" step="0.01"
                            value="{{ old('rate', 0) }}" class="input-modern" placeholder="0">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Jours de retard <span class="text-red-500">*</span></label>
                    <input type="number" name="days_late" min="1" required
                        value="{{ old('days_late') }}" class="input-modern" placeholder="ex: 15">
                </div>

            </div>
            <div class="flex items-center justify-end gap-2 px-5 py-4">
                <a href="{{ route('penalties.index') }}" class="btn-secondary">Annuler</a>
                <button type="submit" class="btn-primary">Créer la pénalité</button>
            </div>
        </form>
    </div>
</div>
@endsection
