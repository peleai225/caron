<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use App\Models\Penalty;
use App\Models\Receipt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Génère automatiquement les échéances de paiement pour un contrat
     */
    public function generatePaymentSchedules(Contract $contract): void
    {
        $startDate = Carbon::parse($contract->start_date);
        $endDate = Carbon::parse($contract->end_date);
        $amount = $contract->rent_amount;
        
        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dueDate = $currentDate->copy()->day((int) $contract->payment_day);
            
            if ($dueDate->lt($currentDate)) {
                $dueDate->addMonth();
            }
            
            if ($dueDate->lte($endDate)) {
                PaymentSchedule::create([
                    'contract_id' => $contract->id,
                    'due_date' => $dueDate,
                    'amount' => $amount,
                    'status' => 'pending',
                ]);
            }
            
            // Passer au mois suivant selon la fréquence
            match($contract->payment_frequency) {
                'monthly' => $currentDate->addMonth(),
                'quarterly' => $currentDate->addMonths(3),
                'yearly' => $currentDate->addYear(),
                default => $currentDate->addMonth(),
            };
        }
    }

    /**
     * Calcule les pénalités de retard
     */
    public function calculatePenalties(PaymentSchedule $schedule, float $penaltyRate = 0.1): ?Penalty
    {
        if ($schedule->status !== 'pending' || !$schedule->isOverdue()) {
            return null;
        }

        $daysLate = $schedule->daysOverdue();
        $penaltyAmount = $schedule->amount * $penaltyRate * ($daysLate / 30); // 10% par mois de retard

        return Penalty::create([
            'payment_schedule_id' => $schedule->id,
            'amount' => $penaltyAmount,
            'rate' => $penaltyRate * 100,
            'days_late' => $daysLate,
            'calculated_at' => now(),
        ]);
    }

    /**
     * Enregistre un paiement
     */
    public function recordPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $payment = Payment::create($data);
            
            // Mettre à jour l'échéancier
            if ($payment->payment_schedule_id) {
                $schedule = PaymentSchedule::find($payment->payment_schedule_id);
                $schedule->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }
            
            // Générer la quittance
            $this->generateReceipt($payment);

            // Générer la facture de commission si commission définie
            $this->generateCommissionInvoice($payment);

            return $payment;
        });
    }

    /**
     * Génère une quittance PDF
     */
    public function generateReceipt(Payment $payment): Receipt
    {
        // Vérifier si une quittance existe déjà
        $receipt = Receipt::where('payment_id', $payment->id)->first();
        
        if ($receipt && $receipt->pdf_path && file_exists(storage_path('app/public/receipts/' . $receipt->pdf_path))) {
            return $receipt;
        }

        $receiptNumber = 'QUI-' . now()->format('Y') . '-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT);
        
        $receipt = Receipt::updateOrCreate(
            ['payment_id' => $payment->id],
            [
                'receipt_number' => $receiptNumber,
                'amount' => $payment->total_amount,
                'issue_date' => $payment->payment_date,
            ]
        );

        // Générer le PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('receipts.pdf', ['receipt' => $receipt->load('payment.contract.tenant', 'payment.contract.property')]);
        $filename = 'receipt_' . $receiptNumber . '.pdf';
        $path = storage_path('app/public/receipts/' . $filename);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        $receipt->update(['pdf_path' => $filename]);

        return $receipt;
    }

    /**
     * Génère une facture de commission agence après encaissement
     */
    public function generateCommissionInvoice(Payment $payment): ?Invoice
    {
        $payment->load('contract.agency');
        $contract = $payment->contract;

        if (!$contract) return null;

        // Calculer la commission
        $commissionPercent = $payment->commission_percent ?? $contract->commission_percent ?? null;
        if (!$commissionPercent || $commissionPercent <= 0) return null;

        $commissionAmount = round($payment->amount * $commissionPercent / 100, 2);

        // Numéro de facture unique
        $agencyId = $contract->agency_id;
        $year = now()->year;
        $count = Invoice::whereHas('contract', fn($q) => $q->where('agency_id', $agencyId))
            ->whereYear('created_at', $year)
            ->count() + 1;
        $invoiceNumber = 'FAC-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'contract_id'    => $contract->id,
            'payment_id'     => $payment->id,
            'invoice_number' => $invoiceNumber,
            'invoice_type'   => 'commission',
            'amount'         => $commissionAmount,
            'tax_amount'     => 0,
            'issue_date'     => now()->toDateString(),
            'due_date'       => now()->toDateString(),
            'description'    => 'Commission de gestion ' . $commissionPercent . '% sur loyer du ' . $payment->period . ' — ' . ($contract->property->address ?? ''),
            'status'         => 'paid',
        ]);

        return $invoice;
    }

    /**
     * Génère automatiquement les loyers du mois
     */
    public function generateMonthlyRents(): void
    {
        $activeContracts = Contract::where('status', 'active')
            ->where('end_date', '>=', now())
            ->get();

        foreach ($activeContracts as $contract) {
            $this->generatePaymentSchedules($contract);
        }
    }
}

