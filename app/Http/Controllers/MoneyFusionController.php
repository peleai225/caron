<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contract;
use App\Services\MoneyFusionService;
use App\Services\PaymentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MoneyFusionController extends Controller
{
    public function __construct(
        private MoneyFusionService $moneyFusion,
        private PaymentService     $paymentService,
        private NotificationService $notificationService,
    ) {}

    /**
     * Initier un paiement MoneyFusion depuis le formulaire d'encaissement.
     */
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'contract_id'         => 'required|exists:contracts,id',
            'payment_schedule_id' => 'nullable|exists:payment_schedules,id',
            'amount'              => 'required|numeric|min:1',
            'charges_amount'      => 'nullable|numeric|min:0',
            'penalty_amount'      => 'nullable|numeric|min:0',
            'depense_travaux'     => 'nullable|numeric|min:0',
            'commission_percent'  => 'nullable|numeric|min:0|max:100',
            'period'              => 'required|string',
            'payment_type'        => 'nullable|in:loyer,charges_locatives,factures,vente,commission',
            'phone'               => 'required|string|min:8|max:20',
            'redirect_to'         => 'nullable|string',
        ]);

        $agencyId = $this->requireAgencyId();
        $contract = Contract::where('agency_id', $agencyId)->findOrFail($validated['contract_id']);

        $clientName = trim(
            ($contract->tenant->first_name ?? '') . ' ' . ($contract->tenant->last_name ?? '')
        ) ?: $contract->tenant->phone ?? 'Client';

        // Stocker les données du paiement en session pour les récupérer après callback
        $request->session()->put('moneyfusion_pending', [
            'contract_id'         => $validated['contract_id'],
            'payment_schedule_id' => $validated['payment_schedule_id'] ?? null,
            'amount'              => $validated['amount'],
            'charges_amount'      => $validated['charges_amount'] ?? 0,
            'penalty_amount'      => $validated['penalty_amount'] ?? 0,
            'depense_travaux'     => $validated['depense_travaux'] ?? 0,
            'commission_percent'  => $validated['commission_percent'] ?? null,
            'period'              => $validated['period'],
            'payment_type'        => $validated['payment_type'] ?? 'loyer',
            'payment_method'      => 'moneyfusion',
            'redirect_to'         => $validated['redirect_to'] ?? 'rents.index',
        ]);

        $result = $this->moneyFusion->initiatePayment([
            'amount'      => $validated['amount'],
            'phone'       => $validated['phone'],
            'client_name' => $clientName,
            'contract_id' => $validated['contract_id'],
            'agency_id'   => $agencyId,
            'return_url'  => route('moneyfusion.return'),
        ]);

        if (! ($result['statut'] ?? false)) {
            return redirect()->back()
                ->with('error', $result['message'] ?? 'Erreur lors de l\'initialisation du paiement.');
        }

        // Stocker le token pour vérification au retour
        $request->session()->put('moneyfusion_token', $result['token']);

        // Rediriger vers la page de paiement MoneyFusion
        return redirect()->away($result['url']);
    }

    /**
     * Callback après retour de la page de paiement MoneyFusion.
     */
    public function return(Request $request)
    {
        $token = $request->session()->get('moneyfusion_token');
        $pending = $request->session()->get('moneyfusion_pending');

        if (! $token || ! $pending) {
            return redirect()->route('rents.index')
                ->with('error', 'Session de paiement expirée.');
        }

        // Vérifier le statut réel du paiement
        $status = $this->moneyFusion->checkPaymentStatus($token);

        if (! ($status['statut'] ?? false)) {
            return redirect()->route('rents.index')
                ->with('error', 'Impossible de vérifier le statut du paiement.');
        }

        $paymentStatus = $status['data']['statut'] ?? 'pending';
        $numeroTransaction = $status['data']['numeroTransaction'] ?? null;
        $moyen = $status['data']['moyen'] ?? null;

        if ($paymentStatus === 'paid') {
            $this->recordPayment($pending, $token, $numeroTransaction, $moyen);

            $request->session()->forget(['moneyfusion_token', 'moneyfusion_pending']);

            $redirectTo = $pending['redirect_to'] ?? 'rents.index';
            $route = in_array($redirectTo, ['dashboard', 'rents.index']) ? $redirectTo : 'rents.index';

            return redirect()->route($route)
                ->with('success', 'Paiement MoneyFusion confirmé avec succès.');
        }

        if (in_array($paymentStatus, ['failure', 'no paid'])) {
            $request->session()->forget(['moneyfusion_token', 'moneyfusion_pending']);
            return redirect()->route('rents.index')
                ->with('error', 'Le paiement a échoué ou a été annulé.');
        }

        // Statut "pending" — paiement en cours
        return redirect()->route('rents.index')
            ->with('warning', 'Paiement en cours de traitement. Il sera enregistré automatiquement à confirmation.');
    }

    /**
     * Webhook POST reçu de MoneyFusion (sans auth, vérification par token en DB).
     */
    public function webhook(Request $request)
    {
        $data = $request->all();

        Log::info('MoneyFusion webhook reçu', $data);

        $event = $data['event'] ?? null;
        $token = $data['tokenPay'] ?? null;

        if (! $token) {
            return response()->json(['ok' => false, 'message' => 'Token manquant'], 400);
        }

        // Idempotence : ignorer si déjà traité
        $already = Payment::where('reference', 'mf_' . $token)
            ->where('status', 'completed')
            ->exists();

        if ($already) {
            return response()->json(['ok' => true, 'message' => 'Déjà traité']);
        }

        if ($event === 'payin.session.completed') {
            $personalInfo = $data['personal_Info'][0] ?? [];
            $contractId = $personalInfo['contract_id'] ?? null;

            if (! $contractId) {
                Log::warning('MoneyFusion webhook: contract_id manquant', $data);
                return response()->json(['ok' => false, 'message' => 'contract_id manquant'], 422);
            }

            // Tenter de récupérer les données de session par token — fallback minimal
            $pending = [
                'contract_id'    => $contractId,
                'amount'         => $data['Montant'] ?? 0,
                'charges_amount' => 0,
                'penalty_amount' => 0,
                'depense_travaux'=> 0,
                'period'         => now()->format('Y-m'),
                'payment_type'   => 'loyer',
                'payment_method' => 'moneyfusion',
            ];

            $this->recordPayment(
                $pending,
                $token,
                $data['numeroTransaction'] ?? null,
                $data['moyen'] ?? null
            );

            Log::info('MoneyFusion paiement enregistré via webhook', ['token' => $token]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Vérifier manuellement le statut d'un paiement en attente.
     */
    public function checkStatus(Request $request)
    {
        $token = $request->get('token', $request->session()->get('moneyfusion_token'));

        if (! $token) {
            return response()->json(['error' => 'Token manquant'], 400);
        }

        $result = $this->moneyFusion->checkPaymentStatus($token);

        return response()->json($result);
    }

    private function recordPayment(array $pending, string $token, ?string $numeroTransaction, ?string $moyen): Payment
    {
        $data = array_merge($pending, [
            'payment_date'   => now()->toDateString(),
            'status'         => 'completed',
            'reference'      => 'mf_' . $token,
            'payment_method' => 'moneyfusion',
            'notes'          => implode(' | ', array_filter([
                $moyen ? 'Moyen: ' . strtoupper($moyen) : null,
                $numeroTransaction ? 'N° trans: ' . $numeroTransaction : null,
                'Token: ' . $token,
            ])),
        ]);

        $payment = $this->paymentService->recordPayment($data);
        $this->notificationService->notifyPaymentReceived($payment);

        return $payment;
    }
}
