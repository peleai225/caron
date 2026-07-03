@extends('layouts.app')

@section('title', 'Dashboard Propriétaire')
@section('page-title', 'Dashboard Propriétaire')

@section('content')
<div class="space-y-5">
    @if(!$owner)
        <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3">
            <p class="text-xs font-semibold text-amber-800 mb-1">Aucun compte propriétaire trouvé</p>
            <p class="text-xs text-amber-700">Contactez votre agence pour associer votre compte utilisateur à un compte propriétaire.</p>
        </div>
    @else
        <header class="page-header-block">
            <div>
                <h2 class="page-title-main">Bienvenue, {{ $owner->name }}</h2>
                <p class="page-subtitle">Gérez vos biens immobiliers et suivez vos revenus</p>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card">
                <p class="text-xs font-medium text-slate-500 mb-1">Total Biens</p>
                <p class="text-xl font-bold text-slate-900">{{ $stats['total_properties'] ?? 0 }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Biens immobiliers</p>
            </div>
            <div class="stat-card">
                <p class="text-xs font-medium text-slate-500 mb-1">Contrats Actifs</p>
                <p class="text-xl font-bold text-emerald-600">{{ $stats['active_contracts'] ?? 0 }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">En cours</p>
            </div>
            <div class="stat-card">
                <p class="text-xs font-medium text-slate-500 mb-1">Revenus du Mois</p>
                <p class="text-xl font-bold text-slate-900">{{ number_format($stats['monthly_revenue'] ?? 0, 0, ',', ' ') }} FCFA</p>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ now()->format('F Y') }}</p>
            </div>
            <div class="stat-card">
                <p class="text-xs font-medium text-slate-500 mb-1">Paiements en Attente</p>
                <p class="text-xl font-bold text-amber-600">{{ number_format($stats['pending_payments'] ?? 0, 0, ',', ' ') }} FCFA</p>
                <p class="text-[11px] text-slate-400 mt-0.5">À recevoir</p>
            </div>
        </div>

        <div class="card-panel">
            <div class="border-b border-slate-100">
                <nav class="flex -mb-px overflow-x-auto" id="dashboard-tabs" role="tablist">
                    <button class="tab-button active px-5 py-3 text-xs font-semibold text-primary-700 border-b-2 border-primary-600 transition-all whitespace-nowrap" data-tab="overview">Vue d'ensemble</button>
                    <button class="tab-button px-5 py-3 text-xs font-semibold text-slate-500 border-b-2 border-transparent hover:text-primary-700 hover:border-primary-600 transition-all whitespace-nowrap" data-tab="properties">Mes Biens</button>
                    <button class="tab-button px-5 py-3 text-xs font-semibold text-slate-500 border-b-2 border-transparent hover:text-primary-700 hover:border-primary-600 transition-all whitespace-nowrap" data-tab="contracts">Contrats</button>
                    <button class="tab-button px-5 py-3 text-xs font-semibold text-slate-500 border-b-2 border-transparent hover:text-primary-700 hover:border-primary-600 transition-all whitespace-nowrap" data-tab="payments">Paiements</button>
                    <button class="tab-button px-5 py-3 text-xs font-semibold text-slate-500 border-b-2 border-transparent hover:text-primary-700 hover:border-primary-600 transition-all whitespace-nowrap" data-tab="expenses">Dépenses</button>
                    <button class="tab-button px-5 py-3 text-xs font-semibold text-slate-500 border-b-2 border-transparent hover:text-primary-700 hover:border-primary-600 transition-all whitespace-nowrap" data-tab="etat-des-lieux">États des lieux</button>
                </nav>
            </div>

            <div class="card-panel-body">
                <div id="tab-overview" class="tab-content active">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">Paiements Récents</h3>
                            @if($recentPayments->count() > 0)
                            <div class="space-y-2">
                                @foreach($recentPayments as $payment)
                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                                    <div>
                                        <p class="text-xs font-medium text-slate-900">{{ $payment->contract->property->name ?? 'N/A' }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $payment->contract->tenant->full_name ?? 'N/A' }} — {{ $payment->payment_date->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs font-bold text-emerald-600">{{ number_format($payment->amount + ($payment->charges_amount ?? 0), 0, ',', ' ') }} FCFA</p>
                                        <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Payé</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-8">
                                <p class="text-xs text-slate-500">Aucun paiement récent</p>
                            </div>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-slate-900 mb-3">Paiements à Venir</h3>
                            @if($upcomingPayments->count() > 0)
                            <div class="space-y-2">
                                @foreach($upcomingPayments as $schedule)
                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                                    <div>
                                        <p class="text-xs font-medium text-slate-900">{{ $schedule->contract->property->name ?? 'N/A' }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $schedule->contract->tenant->full_name ?? 'N/A' }} — Échéance: {{ $schedule->due_date->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs font-bold text-amber-600">{{ number_format($schedule->amount, 0, ',', ' ') }} FCFA</p>
                                        <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">En attente</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-8">
                                <p class="text-xs text-slate-500">Aucun paiement à venir</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($overduePayments->count() > 0)
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-xs font-semibold text-red-800 mb-2">Paiements en Retard ({{ $overduePayments->count() }})</p>
                        <div class="space-y-1">
                            @foreach($overduePayments->take(5) as $overdue)
                            <p class="text-xs text-red-700">
                                {{ $overdue->contract->property->name ?? 'N/A' }} — {{ number_format($overdue->amount, 0, ',', ' ') }} FCFA ({{ $overdue->due_date->diffForHumans() }})
                            </p>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div id="tab-properties" class="tab-content hidden">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Mes Biens Immobiliers</h3>
                    @if($properties->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($properties as $property)
                        <div class="p-4 bg-slate-50 rounded-lg">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-900">{{ $property->name }}</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $property->address }}</p>
                                </div>
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">{{ $property->type }}</span>
                            </div>
                            <div class="space-y-1 text-[11px]">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Loyer mensuel:</span>
                                    <span class="font-medium text-slate-900">{{ number_format($property->monthly_rent ?? 0, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Contrats actifs:</span>
                                    <span class="font-medium text-slate-900">{{ $property->contracts->where('status', 'active')->count() }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-xs text-slate-500">Aucun bien immobilier</p>
                    </div>
                    @endif
                </div>

                <div id="tab-contracts" class="tab-content hidden">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Mes Contrats</h3>
                    @if($activeContracts->count() > 0)
                    <div class="space-y-3">
                        @foreach($activeContracts as $contract)
                        <div class="p-4 bg-slate-50 rounded-lg">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-900">{{ $contract->property->name ?? 'N/A' }}</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Locataire: {{ $contract->tenant->full_name ?? 'N/A' }}</p>
                                </div>
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Actif</span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-[11px]">
                                <div>
                                    <p class="text-slate-500">Loyer mensuel</p>
                                    <p class="font-medium text-slate-900">{{ number_format($contract->rent_amount, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <p class="text-slate-500">Date début</p>
                                    <p class="font-medium text-slate-900">{{ $contract->start_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-500">Date fin</p>
                                    <p class="font-medium text-slate-900">{{ $contract->end_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-500">Paiements</p>
                                    <p class="font-medium text-slate-900">{{ $contract->payments->where('status', 'completed')->count() }}/{{ $contract->payments->count() }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-xs text-slate-500">Aucun contrat actif</p>
                    </div>
                    @endif
                </div>

                <div id="tab-payments" class="tab-content hidden">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Historique des Paiements</h3>
                    @if($recentPayments->count() > 0 || $pendingPayments->count() > 0)
                    <div class="space-y-2">
                        @foreach($pendingPayments->take(10) as $payment)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                            <div>
                                <p class="text-xs font-medium text-slate-900">{{ $payment->contract->property->name ?? 'N/A' }}</p>
                                <p class="text-[11px] text-slate-500">{{ $payment->contract->tenant->full_name ?? 'N/A' }} — {{ $payment->payment_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-amber-600">{{ number_format($payment->amount + ($payment->charges_amount ?? 0), 0, ',', ' ') }} FCFA</p>
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">En attente</span>
                            </div>
                        </div>
                        @endforeach
                        @foreach($recentPayments as $payment)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                            <div>
                                <p class="text-xs font-medium text-slate-900">{{ $payment->contract->property->name ?? 'N/A' }}</p>
                                <p class="text-[11px] text-slate-500">{{ $payment->contract->tenant->full_name ?? 'N/A' }} — Payé le: {{ $payment->payment_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-emerald-600">{{ number_format($payment->amount + ($payment->charges_amount ?? 0), 0, ',', ' ') }} FCFA</p>
                                <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Payé</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-xs text-slate-500">Aucun paiement</p>
                    </div>
                    @endif
                </div>

                <div id="tab-expenses" class="tab-content hidden">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">Dépenses liées à vos biens</h3>
                    @if(isset($expenses) && $expenses->count() > 0)
                    <div class="space-y-2">
                        @foreach($expenses as $expense)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                            <div>
                                <p class="text-xs font-medium text-slate-900">{{ $expense->property->name ?? 'Bien #' . $expense->property_id }}</p>
                                <p class="text-[11px] text-slate-500">{{ $expense->type ?? 'Dépense' }} — {{ $expense->expense_date->format('d/m/Y') }}</p>
                            </div>
                            <p class="text-xs font-bold text-red-600">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-xs text-slate-500">Aucune dépense enregistrée</p>
                    </div>
                    @endif
                </div>

                <div id="tab-etat-des-lieux" class="tab-content hidden">
                    <h3 class="text-sm font-semibold text-slate-900 mb-3">États des lieux</h3>
                    @if(isset($etatDesLieux) && $etatDesLieux->count() > 0)
                    <div class="space-y-2">
                        @foreach($etatDesLieux as $edl)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                            <div>
                                <p class="text-xs font-medium text-slate-900">{{ $edl->property->name ?? $edl->property->address }}</p>
                                <p class="text-[11px] text-slate-500">
                                    <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded ring-1 ring-inset {{ $edl->type === 'entree' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20' }}">
                                        {{ $edl->type === 'entree' ? 'Entrée' : 'Sortie' }}
                                    </span>
                                    — {{ $edl->date->format('d/m/Y') }}
                                </p>
                                @if($edl->contract?->tenant)
                                <p class="text-[11px] text-slate-400 mt-0.5">Locataire : {{ $edl->contract->tenant->full_name }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-xs text-slate-500">Aucun état des lieux</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'text-primary-700', 'border-primary-600');
                btn.classList.add('text-slate-500', 'border-transparent');
            });
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.classList.add('hidden');
            });
            this.classList.add('active', 'text-primary-700', 'border-primary-600');
            this.classList.remove('text-slate-500', 'border-transparent');
            document.getElementById(`tab-${targetTab}`).classList.add('active');
            document.getElementById(`tab-${targetTab}`).classList.remove('hidden');
        });
    });
});
</script>
@endsection
