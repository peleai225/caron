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
            <a href="{{ route('rents.receipt', $payment) }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Quittance PDF
            </a>
            @else
            <a href="{{ route('rents.receipt', $payment) }}" class="btn-secondary text-xs">Générer la quittance</a>
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
            @php
                $receiptUrl    = route('rents.receipt', $payment);
                $tenantPhone   = preg_replace('/\s+/', '', $payment->contract?->tenant?->phone ?? '');
                $ownerPhone    = preg_replace('/\s+/', '', $payment->contract?->owner?->phone ?? '');
                $tenantName    = $payment->contract?->tenant?->full_name ?? '';
                $period        = \Carbon\Carbon::parse($payment->period . '-01')->isoFormat('MMMM YYYY');
                $waMsg         = urlencode("Bonjour {$tenantName}, voici votre quittance de loyer pour {$period} : {$receiptUrl}");
                $waMsgOwner    = urlencode("Bonjour, veuillez trouver ci-joint le reçu de loyer de {$tenantName} pour {$period} : {$receiptUrl}");
                $tenantEmail   = $payment->contract?->tenant?->email ?? '';
                $mailSubject   = urlencode("Quittance de loyer — {$period}");
                $mailBody      = urlencode("Bonjour {$tenantName},\n\nVeuillez trouver votre quittance de loyer pour {$period} à l'adresse suivante : {$receiptUrl}\n\nCordialement.");
            @endphp
            <div class="card-panel">
                <div class="card-panel-header">Quittance n° {{ $payment->receipt->receipt_number }}</div>
                <div class="card-panel-body space-y-2">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Émise le</span>
                        <span class="font-medium text-slate-900">{{ $payment->receipt->issue_date?->format('d/m/Y') ?? '—' }}</span>
                    </div>
                    {{-- Télécharger --}}
                    <a href="{{ $receiptUrl }}" class="btn-primary w-full justify-center flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Télécharger PDF
                    </a>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mt-1">Partager</p>
                    {{-- WhatsApp locataire --}}
                    @if($tenantPhone)
                    <a href="https://wa.me/{{ ltrim($tenantPhone, '+') }}?text={{ $waMsg }}"
                       target="_blank"
                       class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-white bg-[#25D366] hover:bg-[#1ebe5d] rounded-lg transition-colors">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Envoyer au locataire
                    </a>
                    @endif
                    {{-- WhatsApp propriétaire --}}
                    @if($ownerPhone)
                    <a href="https://wa.me/{{ ltrim($ownerPhone, '+') }}?text={{ $waMsgOwner }}"
                       target="_blank"
                       class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-[#25D366] bg-[#25D366]/10 hover:bg-[#25D366]/20 rounded-lg transition-colors border border-[#25D366]/30">
                        <svg class="w-4 h-4 fill-[#25D366]" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Envoyer au propriétaire
                    </a>
                    @endif
                    {{-- Email locataire --}}
                    @if($tenantEmail)
                    <a href="mailto:{{ $tenantEmail }}?subject={{ $mailSubject }}&body={{ $mailBody }}"
                       class="flex items-center gap-2 w-full px-3 py-2 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Email au locataire
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <div class="card-panel">
                <div class="card-panel-header">Actions</div>
                <div class="card-panel-body space-y-1">
                    @if($payment->contract)
                    <a href="{{ route('contracts.show', $payment->contract) }}" class="block px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 rounded-md transition-colors">Voir le contrat</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
