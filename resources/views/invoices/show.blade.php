@extends('layouts.app')

@section('title', 'Détails de la facture')
@section('page-title', 'Détails de la facture')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <header class="page-header-block">
        <div>
            <h2 class="page-title-main">Facture {{ $invoice->invoice_number }}</h2>
            <p class="page-subtitle">Détails de la facture</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('invoices.download', $invoice) }}" class="btn-primary">Télécharger PDF</a>
            <a href="{{ route('invoices.index') }}" class="btn-secondary">Retour</a>
        </div>
    </header>

    <div class="card-panel">
        <div class="card-panel-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Numéro de facture</p>
                    <p class="text-sm font-semibold text-slate-900">{{ $invoice->invoice_number }}</p>
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Statut</p>
                    <x-status-badge :status="$invoice->status" />
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Date d'émission</p>
                    <p class="text-sm font-medium text-slate-900">{{ $invoice->issue_date->format('d/m/Y') }}</p>
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Date d'échéance</p>
                    <p class="text-sm font-medium text-slate-900">{{ $invoice->due_date->format('d/m/Y') }}</p>
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Montant HT</p>
                    <p class="text-sm font-medium text-slate-900">{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</p>
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-0.5">TVA</p>
                    <p class="text-sm font-medium text-slate-900">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} FCFA</p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-xs text-slate-500 mb-0.5">Montant TTC</p>
                    <p class="text-2xl font-bold text-slate-900">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Contrat associé</p>
                <div class="bg-slate-50 rounded-lg p-4 space-y-1.5">
                    <p class="text-sm text-slate-700">
                        <span class="text-slate-500">Contrat:</span>
                        <a href="{{ route('contracts.show', $invoice->contract) }}" class="text-primary-600 hover:text-primary-700 font-medium">
                            {{ $invoice->contract->contract_number }}
                        </a>
                    </p>
                    <p class="text-sm text-slate-700">
                        <span class="text-slate-500">Locataire:</span> {{ $invoice->contract->tenant->first_name }} {{ $invoice->contract->tenant->last_name }}
                    </p>
                    <p class="text-sm text-slate-700">
                        <span class="text-slate-500">Bien:</span> {{ $invoice->contract->property->address }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
