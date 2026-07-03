<?php

namespace App\Http\Controllers;

use App\Models\Penalty;
use App\Models\Contract;
use App\Models\PaymentSchedule;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    /**
     * Affiche la liste des pénalités
     */
    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        
        $query = Penalty::with(['paymentSchedule.contract.tenant', 'paymentSchedule.contract.property'])
            ->when($agencyId, fn ($q) => $q->whereHas('paymentSchedule.contract', fn ($cq) => $cq->where('agency_id', $agencyId)));

        if ($request->filled('status')) {
            if ($request->status === 'paid') {
                $query->whereNotNull('paid_at');
            } elseif ($request->status === 'unpaid') {
                $query->whereNull('paid_at');
            }
        }

        if ($request->filled('contract_id')) {
            $query->whereHas('paymentSchedule', function($q) use ($request) {
                $q->where('contract_id', $request->contract_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $penalties = $query->latest()->paginate(20);

        $contracts = Contract::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))
            ->where('status', 'active')
            ->get();

        $schedules = PaymentSchedule::with(['contract.tenant', 'contract.property'])
            ->when($agencyId, fn ($q) => $q->whereHas('contract', fn ($cq) => $cq->where('agency_id', $agencyId)))
            ->whereDoesntHave('penalties', fn ($q) => $q->whereNull('paid_at'))
            ->orderByDesc('due_date')
            ->get();

        $stats = [
            'total'  => Penalty::when($agencyId, fn ($q) => $q->whereHas('paymentSchedule.contract', fn ($cq) => $cq->where('agency_id', $agencyId)))->count(),
            'paid'   => Penalty::when($agencyId, fn ($q) => $q->whereHas('paymentSchedule.contract', fn ($cq) => $cq->where('agency_id', $agencyId)))->whereNotNull('paid_at')->sum('amount'),
            'unpaid' => Penalty::when($agencyId, fn ($q) => $q->whereHas('paymentSchedule.contract', fn ($cq) => $cq->where('agency_id', $agencyId)))->whereNull('paid_at')->sum('amount'),
        ];

        return view('penalties.index', compact('penalties', 'contracts', 'schedules', 'stats'));
    }

    /**
     * Affiche les détails d'une pénalité
     */
    public function show(Penalty $penalty)
    {
        $agencyId = $this->requireAgencyId();
        abort_if(
            optional(optional($penalty->paymentSchedule)->contract)->agency_id !== $agencyId,
            403
        );

        $penalty->load(['paymentSchedule.contract.tenant', 'paymentSchedule.contract.property', 'payment']);

        return view('penalties.show', compact('penalty'));
    }

    /**
     * Marque une pénalité comme payée
     */
    public function markAsPaid(Penalty $penalty)
    {
        $agencyId = $this->requireAgencyId();
        abort_if(
            optional(optional($penalty->paymentSchedule)->contract)->agency_id !== $agencyId,
            403
        );

        $penalty->update(['paid_at' => now()]);

        return redirect()->route('penalties.index')
            ->with('success', 'Pénalité marquée comme payée.');
    }

    /**
     * Formulaire de création manuelle d'une pénalité
     */
    public function create()
    {
        $agencyId = $this->requireAgencyId();

        $schedules = PaymentSchedule::with(['contract.tenant', 'contract.property'])
            ->when($agencyId, fn ($q) => $q->whereHas('contract', fn ($cq) => $cq->where('agency_id', $agencyId)))
            ->whereDoesntHave('penalties', fn ($q) => $q->whereNull('paid_at'))
            ->orderByDesc('due_date')
            ->get();

        return view('penalties.create', compact('schedules'));
    }

    /**
     * Enregistre une pénalité créée manuellement
     */
    public function store(Request $request)
    {
        $agencyId = $this->requireAgencyId();

        $validated = $request->validate([
            'payment_schedule_id' => 'required|exists:payment_schedules,id',
            'amount'              => 'required|numeric|min:0',
            'rate'                => 'nullable|numeric|min:0|max:100',
            'days_late'           => 'required|integer|min:1',
        ]);

        $schedule = PaymentSchedule::with('contract')
            ->when($agencyId, fn ($q) => $q->whereHas('contract', fn ($cq) => $cq->where('agency_id', $agencyId)))
            ->findOrFail($validated['payment_schedule_id']);

        Penalty::create([
            'payment_schedule_id' => $schedule->id,
            'amount'              => $validated['amount'],
            'rate'                => $validated['rate'] ?? 0,
            'days_late'           => $validated['days_late'],
            'calculated_at'       => now(),
        ]);

        return redirect()->route('penalties.index')
            ->with('success', 'Pénalité créée avec succès.');
    }

    public function edit(Penalty $penalty)
    {
        $agencyId = $this->requireAgencyId();
        abort_if(
            optional(optional($penalty->paymentSchedule)->contract)->agency_id !== $agencyId,
            403
        );
        abort_if($penalty->paid_at !== null, 403, 'Impossible de modifier une pénalité déjà payée.');

        $penalty->load(['paymentSchedule.contract.tenant', 'paymentSchedule.contract.property']);

        return view('penalties.edit', compact('penalty'));
    }

    public function update(Request $request, Penalty $penalty)
    {
        $agencyId = $this->requireAgencyId();
        abort_if(
            optional(optional($penalty->paymentSchedule)->contract)->agency_id !== $agencyId,
            403
        );
        abort_if($penalty->paid_at !== null, 403, 'Impossible de modifier une pénalité déjà payée.');

        $validated = $request->validate([
            'amount'    => 'required|numeric|min:0',
            'rate'      => 'nullable|numeric|min:0|max:100',
            'days_late' => 'required|integer|min:1',
        ]);

        $penalty->update($validated);

        return redirect()->route('penalties.show', $penalty)
            ->with('success', 'Pénalité mise à jour avec succès.');
    }

    /**
     * Supprime une pénalité non payée
     */
    public function destroy(Penalty $penalty)
    {
        $agencyId = $this->requireAgencyId();
        abort_if(
            optional(optional($penalty->paymentSchedule)->contract)->agency_id !== $agencyId,
            403
        );
        abort_if($penalty->paid_at !== null, 403, 'Impossible de supprimer une pénalité déjà payée.');

        $penalty->delete();

        return redirect()->route('penalties.index')
            ->with('success', 'Pénalité supprimée.');
    }
}

