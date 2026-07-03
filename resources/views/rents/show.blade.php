@extends('layouts.app')

@section('title', 'Details du paiement')
@section('page-title', 'Details du paiement')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Paiement #{{ $payment->id }}</h2>
            <p class="page-subtitle">
                @if($payment->contract && $payment->contract->tenant)
                    {{ $payment->contract->tenant->full_name }} —
                @endif
                @if($payment->period)
                    {{ \Carbon\Carbon::parse($payment->period . '-01')->format('F Y') }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($payment->receipt)
            <a href="{{ route('rents.receipt', $payment) }}" class="btn-primary">Telecharger quittance</a>
            @endif
            <a href="{{ route('rents.index') }}" class="btn-secondary">Retour</a>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <!-- Payment Details -->
            <div class="card-panel">
                <div class="card-panel-header">Details du paiement</div>
                <div class="card-panel-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Date de paiement</p>
                            <p class="text-sm font-medium text-slate-900">
                                @if($payment->payment_date)
                                    {{ $payment->payment_date->format('d/m/Y') }}
                                @else
                                    <span class="text-slate-400 italic">Non definie</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Periode</p>
                            <p class="text-sm font-medium text-slate-900">{{ \Carbon\Carbon::parse($payment->period . '-01')->format('F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Montant du loyer</p>
                            <p class="text-sm font-semibold text-slate-900">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</p>
                        </div>
                        @if($payment->penalty_amount > 0)
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Penalites de retard</p>
                            <p class="text-sm font-semibold text-red-600">{{ number_format($payment->penalty_amount, 0, ',', ' ') }} FCFA</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Total paye</p>
                            <p class="text-base font-bold text-slate-900">{{ number_format($payment->total_amount, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Methode de paiement</p>
                            <p class="text-sm font-medium text-slate-900 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</p>
                        </div>
                        @if($payment->reference)
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Reference</p>
                            <p class="text-sm font-medium text-slate-900">{{ $payment->reference }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Statut</p>
                            <x-status-badge :status="$payment->status" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contract -->
            @if($payment->contract)
            <div class="card-panel">
                <div class="card-panel-header">Contrat associe</div>
                <div class="card-panel-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $payment->contract->contract_number ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                @if($payment->contract->tenant) {{ $payment->contract->tenant->full_name }} @endif
                                @if($payment->contract->property) — {{ $payment->contract->property->address }} @endif
                            </p>
                        </div>
                        <a href="{{ route('contracts.show', $payment->contract) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir le contrat</a>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <p class="text-xs font-medium text-amber-800">Ce paiement n'est pas associe a un contrat.</p>
            </div>
            @endif

            <!-- Notes -->
            @if($payment->notes)
            <div class="card-panel">
                <div class="card-panel-header">Notes</div>
                <div class="card-panel-body">
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $payment->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-5">
            @if($payment->receipt)
            <div class="card-panel">
                <div class="card-panel-header">Quittance</div>
                <div class="card-panel-body space-y-3">
                    <div>
                        <p class="text-xs text-slate-500">Numero</p>
                        <p class="text-sm font-medium text-slate-900">{{ $payment->receipt->receipt_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Date d'emission</p>
                        <p class="text-sm font-medium text-slate-900">
                            @if($payment->receipt->issue_date)
                                {{ $payment->receipt->issue_date->format('d/m/Y') }}
                            @else
                                <span class="text-slate-400 italic">Non definie</span>
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('rents.receipt', $payment) }}" class="btn-primary w-full text-center block">Telecharger PDF</a>
                </div>
            </div>
            @endif

            <div class="card-panel">
                <div class="card-panel-header">Actions</div>
                <div class="card-panel-body space-y-1">
                    @if($payment->receipt)
                    <a href="{{ route('rents.receipt', $payment) }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-md transition-colors">Telecharger la quittance</a>
                    @endif
                    @if($payment->contract)
                    <a href="{{ route('contracts.show', $payment->contract) }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-md transition-colors">Voir le contrat</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
