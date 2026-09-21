<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\PaymentSchedule;
use App\Models\Owner;
use App\Models\Property;
use App\Models\Tenant;
use App\Services\PaymentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $notificationService;

    public function __construct(PaymentService $paymentService, NotificationService $notificationService)
    {
        $this->paymentService = $paymentService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $agencyId = $this->requireAgencyId();

        $baseContract = function ($q) use ($agencyId) {
            if ($agencyId) {
                $q->where('agency_id', $agencyId);
            }
        };

        $query = Payment::with(['contract.tenant', 'contract.property', 'contract.owner'])
            ->whereHas('contract', $baseContract);

        if ($request->filled('owner_id')) {
            $query->whereHas('contract', function ($q) use ($request) {
                $q->where('owner_id', $request->owner_id);
            });
        }
        if ($request->filled('property_id')) {
            $query->whereHas('contract', function ($q) use ($request) {
                $q->where('property_id', $request->property_id);
            });
        }
        if ($request->filled('tenant_id')) {
            $query->whereHas('contract', function ($q) use ($request) {
                $q->where('tenant_id', $request->tenant_id);
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }
        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $payments = $query->latest('payment_date')->paginate(15);

        // Paiements récents (derniers encaissés, pour le bloc dédié)
        $recentPaymentsQuery = Payment::with(['contract.tenant', 'contract.property', 'contract.owner'])
            ->whereHas('contract', $baseContract)
            ->where('status', 'completed');
        if ($request->filled('owner_id')) {
            $recentPaymentsQuery->whereHas('contract', fn($q) => $q->where('owner_id', $request->owner_id));
        }
        if ($request->filled('property_id')) {
            $recentPaymentsQuery->whereHas('contract', fn($q) => $q->where('property_id', $request->property_id));
        }
        if ($request->filled('tenant_id')) {
            $recentPaymentsQuery->whereHas('contract', fn($q) => $q->where('tenant_id', $request->tenant_id));
        }
        if ($request->filled('period')) {
            $recentPaymentsQuery->where('period', $request->period);
        }
        if ($request->filled('date_from')) {
            $recentPaymentsQuery->where('payment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $recentPaymentsQuery->where('payment_date', '<=', $request->date_to);
        }
        $recentPayments = $recentPaymentsQuery->latest('payment_date')->limit(50)->get();

        // Paiements des arriérés (échéances en retard ou paiements en attente)
        $overdueSchedules = PaymentSchedule::with(['contract.tenant', 'contract.property', 'contract.owner'])
            ->whereHas('contract', $baseContract)
            ->overdue()
            ->whereDoesntHave('payment');
        if ($request->filled('owner_id')) {
            $overdueSchedules->whereHas('contract', fn($q) => $q->where('owner_id', $request->owner_id));
        }
        if ($request->filled('property_id')) {
            $overdueSchedules->whereHas('contract', fn($q) => $q->where('property_id', $request->property_id));
        }
        if ($request->filled('tenant_id')) {
            $overdueSchedules->whereHas('contract', fn($q) => $q->where('tenant_id', $request->tenant_id));
        }
        $overduePayments = $overdueSchedules->orderBy('due_date')->limit(50)->get();

        $stats = [
            'monthly_total' => Payment::whereHas('contract', $baseContract)
                ->where('period', now()->format('Y-m'))
                ->where('status', 'completed')
                ->sum('amount'),
            'overdue_amount' => PaymentSchedule::whereHas('contract', $baseContract)->overdue()->sum('amount'),
            'pending_count' => Payment::whereHas('contract', $baseContract)->where('status', 'pending')->count(),
        ];

        $owners = Owner::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->orderBy('name')->get();
        $properties = Property::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->orderBy('address')->get();
        $tenants = Tenant::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))->orderBy('first_name')->get();

        return view('rents.index', compact('payments', 'stats', 'recentPayments', 'overduePayments', 'owners', 'properties', 'tenants'));
    }

    public function searchTenants(Request $request)
    {
        $agencyId = $this->requireAgencyId();
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $contracts = Contract::when($agencyId, fn ($q) => $q->where('agency_id', $agencyId))
            ->where('status', 'active')
            ->with(['tenant', 'property'])
            ->where(function ($query) use ($q) {
                $query->whereHas('tenant', function ($tq) use ($q) {
                    $tq->where('first_name', 'like', "%{$q}%")
                       ->orWhere('last_name', 'like', "%{$q}%")
                       ->orWhere('phone', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%")
                       ->orWhere('cni_number', 'like', "%{$q}%");
                })->orWhereHas('property', function ($pq) use ($q) {
                    $pq->where('designation', 'like', "%{$q}%")
                       ->orWhere('address', 'like', "%{$q}%")
                       ->orWhere('neighborhood', 'like', "%{$q}%");
                });
            })
            ->limit(15)
            ->get();

        $results = $contracts->map(fn ($c) => [
            'contract_id'   => $c->id,
            'tenant_id'     => $c->tenant_id,
            'tenant_name'   => $c->tenant?->full_name ?? '',
            'phone'         => $c->tenant?->phone ?? '',
            'property'      => $c->property?->name ?? '',
            'address'       => $c->property?->full_address ?? '',
            'designation'   => $c->property?->designation ?? '',
            'contract_number' => $c->contract_number ?? '#' . $c->id,
            'rent_amount'   => (float) $c->rent_amount,
        ]);

        return response()->json($results);
    }

    public function create()
    {
        return view('rents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'payment_schedule_id' => 'nullable|exists:payment_schedules,id',
            'amount' => 'required|numeric|min:0',
            'penalty_amount' => 'nullable|numeric|min:0',
            'charges_amount' => 'nullable|numeric|min:0',
            'depense_travaux' => 'nullable|numeric|min:0',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'payment_date' => 'required|date',
            'period' => 'required|string',
            'payment_count' => 'nullable|integer|min:1|max:24',
            'payment_type' => 'nullable|in:loyer,charges_locatives,factures,vente,commission',
            'payment_method' => 'required|in:moneyfusion,cash,check,transfer,bank_transfer,mobile_money',
            'phone'          => 'nullable|string|max:20',
            'reference'      => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'generate_receipt' => 'nullable|boolean',
        ]);
        $validated['payment_type'] = $validated['payment_type'] ?? 'loyer';
        $paymentCount = max(1, (int) ($validated['payment_count'] ?? 1));
        $generateReceipt = $request->boolean('generate_receipt', true);
        unset($validated['payment_count'], $validated['generate_receipt']);

        // MoneyFusion — transférer vers le contrôleur MoneyFusion
        if ($validated['payment_method'] === 'moneyfusion') {
            if (empty($validated['phone'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['phone' => 'Le numéro de téléphone est requis pour MoneyFusion.']);
            }
            $request->merge(['redirect_to' => $request->input('redirect_to', 'rents.index')]);
            return app(\App\Http\Controllers\MoneyFusionController::class)->initiate($request);
        }

        $validated['status'] = 'completed';
        $basePeriod = \Carbon\Carbon::createFromFormat('Y-m', $validated['period']);
        $lastPayment = null;

        for ($i = 0; $i < $paymentCount; $i++) {
            $data = $validated;
            $data['period'] = $basePeriod->copy()->addMonths($i)->format('Y-m');

            $payment = $this->paymentService->recordPayment($data, $generateReceipt);
            $this->notificationService->notifyPaymentReceived($payment);
            $lastPayment = $payment;
        }

        $this->forgetDashboardCache($lastPayment->contract->agency_id ?? null);

        $redirectTo = $request->input('redirect_to', 'rents.index');
        $route = in_array($redirectTo, ['dashboard', 'rents.index']) ? $redirectTo : 'rents.index';

        $msg = $paymentCount > 1
            ? "{$paymentCount} paiements enregistrés avec succès."
            : 'Paiement enregistré avec succès.';

        return redirect()->route($route)->with('success', $msg);
    }

    public function show(Payment $payment)
    {
        $this->authorizeAgency(optional($payment->contract)->agency_id);

        $payment->load(['contract.tenant', 'contract.property', 'receipt', 'paymentSchedule']);

        return view('rents.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $this->authorizeAgency(optional($payment->contract)->agency_id);
        $payment->load(['contract.tenant', 'contract.property']);

        return view('rents.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $this->authorizeAgency(optional($payment->contract)->agency_id);

        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'amount' => 'required|numeric|min:0',
            'penalty_amount' => 'nullable|numeric|min:0',
            'charges_amount' => 'nullable|numeric|min:0',
            'depense_travaux' => 'nullable|numeric|min:0',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'payment_date' => 'required|date',
            'period' => 'required|string',
            'payment_type' => 'nullable|in:loyer,charges_locatives,factures,vente,commission',
            'payment_method' => 'required|in:moneyfusion,cash,check,transfer,bank_transfer,mobile_money',
            'status' => 'required|in:pending,completed,failed,refunded',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);
        $validated['payment_type'] = $validated['payment_type'] ?? 'loyer';

        $payment->update($validated);

        // Invalider le cache dashboard
        $this->forgetDashboardCache(optional($payment->contract)->agency_id);

        return redirect()->route('rents.show', $payment)
            ->with('success', 'Paiement mis à jour avec succès.');
    }

    public function destroy(Payment $payment)
    {
        $this->authorizeAgency(optional($payment->contract)->agency_id);

        $agencyId = optional($payment->contract)->agency_id;

        // Supprimer la quittance associée
        if ($payment->receipt) {
            if ($payment->receipt->pdf_path && file_exists(storage_path('app/public/receipts/' . $payment->receipt->pdf_path))) {
                unlink(storage_path('app/public/receipts/' . $payment->receipt->pdf_path));
            }
            $payment->receipt->delete();
        }

        // Supprimer la transaction associée
        if ($payment->transaction) {
            $payment->transaction->delete();
        }

        $payment->delete();

        // Invalider le cache dashboard
        $this->forgetDashboardCache($agencyId);

        return redirect()->route('rents.index')
            ->with('success', 'Paiement supprimé avec succès.');
    }

    public function downloadReceipt(Payment $payment)
    {
        $this->authorizeAgency(optional($payment->contract)->agency_id);

        $receipt = $payment->receipt;
        
        if (!$receipt || !$receipt->pdf_path || !file_exists(storage_path('app/public/receipts/' . $receipt->pdf_path))) {
            $receipt = $this->paymentService->generateReceipt($payment);
        }

        $path = storage_path('app/public/receipts/' . $receipt->pdf_path);
        
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Le fichier PDF de la quittance n\'existe pas.');
        }

        return response()->download($path);
    }
}
