@extends('layouts.app')

@section('title', 'Details du contrat')
@section('page-title', 'Details du contrat')

@section('content')
<div class="space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Contrat {{ $contract->contract_number }}</h2>
            <p class="page-subtitle">{{ $contract->tenant->full_name ?? 'Locataire non associe' }} — {{ $contract->property->address ?? 'Bien non associe' }}</p>
        </div>
        <div class="flex items-center gap-2">
            @if($contract->status === 'draft')
            <form action="{{ route('contracts.sign', $contract) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-primary">Signer</button>
            </form>
            @endif
            @if($contract->pdf_path)
            <a href="{{ route('contracts.download', $contract) }}" class="btn-secondary">PDF</a>
            @endif
            <a href="{{ route('contracts.edit', $contract) }}" class="btn-primary">Modifier</a>
            <a href="{{ route('contracts.index') }}" class="btn-secondary">Retour</a>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Main -->
        <div class="lg:col-span-2 space-y-5">
            <!-- Contract Details -->
            <div class="card-panel">
                <div class="card-panel-header">Details du contrat</div>
                <div class="card-panel-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Numero</p>
                            <p class="text-sm font-medium text-slate-900">{{ $contract->contract_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Type</p>
                            <p class="text-sm font-medium text-slate-900">{{ $contract->type_contrat ? (\App\Models\Contract::typesContrat()[$contract->type_contrat] ?? $contract->type_contrat) : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Statut</p>
                            <x-status-badge :status="$contract->status" />
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Date de debut</p>
                            <p class="text-sm font-medium text-slate-900">{{ $contract->start_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Date de fin</p>
                            <p class="text-sm font-medium text-slate-900">{{ $contract->end_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Loyer mensuel</p>
                            <p class="text-sm font-semibold text-slate-900">{{ number_format($contract->rent_amount, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Caution</p>
                            <p class="text-sm font-medium text-slate-900">{{ number_format($contract->deposit, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Frequence</p>
                            <p class="text-sm font-medium text-slate-900">
                                @if($contract->payment_frequency === 'monthly') Mensuel
                                @elseif($contract->payment_frequency === 'quarterly') Trimestriel
                                @else Annuel
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Jour de paiement</p>
                            <p class="text-sm font-medium text-slate-900">Le {{ $contract->payment_day }} de chaque mois</p>
                        </div>
                        @if($contract->signed_at)
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Signe le</p>
                            <p class="text-sm font-medium text-slate-900">{{ $contract->signed_at->format('d/m/Y a H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tenant -->
            <div class="card-panel">
                <div class="card-panel-header">Locataire</div>
                <div class="card-panel-body">
                    @if($contract->tenant)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $contract->tenant->full_name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $contract->tenant->phone }}{{ $contract->tenant->email ? ' — ' . $contract->tenant->email : '' }}</p>
                        </div>
                        <a href="{{ route('tenants.show', $contract->tenant) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir</a>
                    </div>
                    @else
                    <p class="text-xs text-slate-400">Aucun locataire associe.</p>
                    @endif
                </div>
            </div>

            <!-- Property -->
            <div class="card-panel">
                <div class="card-panel-header">Bien immobilier</div>
                <div class="card-panel-body">
                    @if($contract->property)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $contract->property->address }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $contract->property->city }} — {{ $contract->property->type }}</p>
                        </div>
                        <a href="{{ route('properties.show', $contract->property) }}" class="text-xs font-medium text-primary-600 hover:text-primary-700">Voir</a>
                    </div>
                    @else
                    <p class="text-xs text-slate-400">Aucun bien associe.</p>
                    @endif
                </div>
            </div>

            <!-- Payment Schedule -->
            @if($contract->paymentSchedules->count() > 0)
            <div class="card-panel">
                <div class="card-panel-header">Echeancier</div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Echeance</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Montant</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($contract->paymentSchedules as $schedule)
                            <tr class="hover:bg-slate-25">
                                <td class="px-4 py-2.5 text-slate-700">{{ $schedule->due_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-2.5 text-slate-900 font-medium">{{ number_format($schedule->amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-4 py-2.5"><x-status-badge :status="$schedule->status" /></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Payments History -->
            @if($contract->payments->count() > 0)
            <div class="card-panel">
                <div class="card-panel-header">Historique des paiements</div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b border-slate-100">
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Periode</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Montant</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Methode</th>
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-600 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($contract->payments as $payment)
                            <tr class="hover:bg-slate-25">
                                <td class="px-4 py-2.5 text-slate-700">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                <td class="px-4 py-2.5 text-slate-700">{{ \Carbon\Carbon::parse($payment->period . '-01')->format('F Y') }}</td>
                                <td class="px-4 py-2.5 text-slate-900 font-medium">
                                    {{ number_format($payment->total_amount, 0, ',', ' ') }} FCFA
                                    @if($payment->penalty_amount > 0)
                                        <span class="text-red-500">(+{{ number_format($payment->penalty_amount, 0, ',', ' ') }})</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-slate-700 capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                                <td class="px-4 py-2.5"><x-status-badge :status="$payment->status" /></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($contract->notes)
            <div class="card-panel">
                <div class="card-panel-header">Notes</div>
                <div class="card-panel-body">
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $contract->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-5">
            <div class="card-panel">
                <div class="card-panel-header">Statistiques</div>
                <div class="card-panel-body space-y-3">
                    <div>
                        <p class="text-xs text-slate-500">Total paye</p>
                        <p class="text-lg font-bold text-slate-900">{{ number_format($contract->payments->where('status', 'completed')->sum('amount'), 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Paiements effectues</p>
                        <p class="text-lg font-bold text-slate-900">{{ $contract->payments->where('status', 'completed')->count() }} / {{ $contract->paymentSchedules->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500">Jours restants</p>
                        <p class="text-lg font-bold text-slate-900">{{ max(0, now()->diffInDays($contract->end_date, false)) }} jours</p>
                    </div>
                </div>
            </div>

            <div class="card-panel">
                <div class="card-panel-header">Actions rapides</div>
                <div class="card-panel-body space-y-1">
                    <a href="{{ route('rents.create', ['contract_id' => $contract->id]) }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-md transition-colors">Enregistrer un paiement</a>
                    <a href="{{ route('contracts.edit', $contract) }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-md transition-colors">Modifier le contrat</a>
                    @if($contract->pdf_path)
                    <a href="{{ route('contracts.download', $contract) }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-md transition-colors">Telecharger le PDF</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
