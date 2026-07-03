@extends('layouts.app')

@section('title', 'Rapports')
@section('page-title', 'Rapports Financiers')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Rapports financiers</h2>
            <p class="page-subtitle">Période : {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.export.excel', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="btn-secondary">Exporter Excel</a>
            <a href="{{ route('reports.export.pdf', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="btn-primary">Exporter PDF</a>
        </div>
    </header>

    <div class="card-panel">
        <form method="GET" action="{{ route('reports.index') }}">
            <div class="card-panel-body">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date début</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="input-modern">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Date fin</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="input-modern">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-secondary w-full justify-center">Générer le rapport</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500 mb-1">Revenus totaux</p>
            <p class="text-xl font-bold text-slate-900">{{ number_format($report['total_revenue'] ?? 0, 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500 mb-1">Impayés</p>
            <p class="text-xl font-bold text-red-600">{{ number_format($report['total_overdue'] ?? 0, 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500 mb-1">Dépenses</p>
            <p class="text-xl font-bold text-slate-900">{{ number_format($report['total_expenses'] ?? 0, 0, ',', ' ') }} FCFA</p>
        </div>
        <div class="stat-card">
            <p class="text-xs font-medium text-slate-500 mb-1">Bénéfice net</p>
            @php $netProfit = ($report['total_revenue'] ?? 0) - ($report['total_expenses'] ?? 0); @endphp
            <p class="text-xl font-bold {{ $netProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($netProfit, 0, ',', ' ') }} FCFA</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="card-panel">
            <div class="card-panel-header">Résumé des paiements</div>
            <div class="card-panel-body space-y-3">
                <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg">
                    <span class="text-xs font-medium text-slate-700">Paiements complétés</span>
                    <span class="text-sm font-bold text-emerald-600">{{ $report['completed_payments'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg">
                    <span class="text-xs font-medium text-slate-700">Paiements en attente</span>
                    <span class="text-sm font-bold text-amber-600">{{ $report['pending_payments'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <span class="text-xs font-medium text-slate-700">Paiements en retard</span>
                    <span class="text-sm font-bold text-red-600">{{ $report['overdue_payments'] ?? 0 }}</span>
                </div>
                @php
                    $total = ($report['completed_payments'] ?? 0) + ($report['pending_payments'] ?? 0) + ($report['overdue_payments'] ?? 0);
                    $rate = $total > 0 ? round(($report['completed_payments'] ?? 0) / $total * 100) : 0;
                @endphp
                <div class="pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-slate-600">Taux de recouvrement</span>
                        <span class="text-sm font-bold text-slate-900">{{ $rate }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-primary-600 h-2 rounded-full transition-all" style="width: {{ $rate }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-panel">
            <div class="card-panel-header">Occupation des biens</div>
            <div class="card-panel-body space-y-3">
                <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-lg">
                    <span class="text-xs font-medium text-slate-700">Biens occupés</span>
                    <span class="text-sm font-bold text-emerald-600">{{ $report['occupied_properties'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <span class="text-xs font-medium text-slate-700">Biens libres</span>
                    <span class="text-sm font-bold text-slate-600">{{ $report['vacant_properties'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg">
                    <span class="text-xs font-medium text-slate-700">En maintenance</span>
                    <span class="text-sm font-bold text-amber-600">{{ $report['maintenance_properties'] ?? 0 }}</span>
                </div>
                @php
                    $totalProp = ($report['occupied_properties'] ?? 0) + ($report['vacant_properties'] ?? 0) + ($report['maintenance_properties'] ?? 0);
                    $occupancyRate = $totalProp > 0 ? round(($report['occupied_properties'] ?? 0) / $totalProp * 100) : 0;
                @endphp
                <div class="pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-slate-600">Taux d'occupation</span>
                        <span class="text-sm font-bold text-slate-900">{{ $occupancyRate }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full transition-all" style="width: {{ $occupancyRate }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
