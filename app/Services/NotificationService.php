<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Contract;
use App\Models\PaymentSchedule;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use App\Services\SmsService;
use App\Services\WhatsAppService;

class NotificationService
{
    protected SmsService $smsService;
    protected WhatsAppService $whatsAppService;

    public function __construct(SmsService $smsService, WhatsAppService $whatsAppService)
    {
        $this->smsService = $smsService;
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Crée une notification système
     */
    public function createNotification(int $userId, string $type, string $title, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Notifie les paiements en retard
     */
    public function notifyOverduePayments(): void
    {
        $overdueSchedules = PaymentSchedule::overdue()->get();
        
        foreach ($overdueSchedules as $schedule) {
            $contract = $schedule->contract;
            $users = $contract->agency->users;
            
            foreach ($users as $user) {
                $this->createNotification(
                    $user->id,
                    'payment_overdue',
                    'Paiement en retard',
                    "Le paiement du contrat {$contract->contract_number} est en retard de {$schedule->daysOverdue()} jours.",
                    ['contract_id' => $contract->id, 'schedule_id' => $schedule->id]
                );
            }
        }
    }

    /**
     * Notifie les contrats expirant bientôt
     */
    public function notifyExpiringContracts(int $days = 30): void
    {
        $expiringContracts = Contract::expiring($days)->get();
        
        foreach ($expiringContracts as $contract) {
            $users = $contract->agency->users;
            
            foreach ($users as $user) {
                $this->createNotification(
                    $user->id,
                    'contract_expiring',
                    'Contrat expirant bientôt',
                    "Le contrat {$contract->contract_number} expire dans {$contract->daysUntilExpiry()} jours.",
                    ['contract_id' => $contract->id]
                );
            }
        }
    }

    /**
     * Envoie une notification par email
     */
    public function sendEmailNotification(User $user, string $subject, string $message, string $template = 'notification'): void
    {
        try {
            Mail::to($user->email)->send(new \App\Mail\NotificationMail($subject, $message, $template));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Envoie une notification SMS
     */
    public function sendSMSNotification(string $phone, string $message): bool
    {
        return $this->smsService->sendSms($phone, $message);
    }

    /**
     * Envoie une notification WhatsApp
     */
    public function sendWhatsAppNotification(string $phone, string $message): bool
    {
        return $this->whatsAppService->sendMessage($phone, $message);
    }

    /**
     * Notifie un paiement reçu (multi-canal)
     */
    public function notifyPaymentReceived(Payment $payment): void
    {
        $contract = $payment->contract;
        $tenant = $contract->tenant;
        $agency = $contract->agency;

        // Notification système
        foreach ($agency->users as $user) {
            $this->createNotification(
                $user->id,
                'payment_received',
                'Paiement reçu',
                "Paiement de {$payment->amount} FCFA reçu pour le contrat {$contract->contract_number}",
                ['payment_id' => $payment->id]
            );
        }

        // SMS si activé
        if (\App\Models\Setting::get('sms_notifications', false, $agency->id) && $tenant->phone) {
            $this->smsService->sendPaymentConfirmation($tenant->phone, [
                'amount' => number_format($payment->amount, 0, ',', ' ') . ' FCFA',
                'reference' => $payment->reference ?? $payment->id,
            ]);
        }

        // WhatsApp si activé
        if (\App\Models\Setting::get('whatsapp_notifications', false, $agency->id) && $tenant->phone) {
            $this->whatsAppService->sendPaymentConfirmation($tenant->phone, [
                'amount' => number_format($payment->amount, 0, ',', ' ') . ' FCFA',
                'reference' => $payment->reference ?? $payment->id,
                'date' => $payment->payment_date->format('d/m/Y'),
            ]);
        }

        // Email si activé
        if (\App\Models\Setting::get('email_notifications', true, $agency->id) && $tenant->email) {
            $this->sendEmailNotification(
                $tenant->user ?? new User(['email' => $tenant->email, 'name' => $tenant->first_name . ' ' . $tenant->last_name]),
                'Confirmation de paiement',
                "Votre paiement de {$payment->amount} FCFA a été reçu avec succès.",
                'payment_confirmation'
            );
        }
    }

    /**
     * Envoie un rappel de paiement (multi-canal)
     */
    public function sendPaymentReminder(PaymentSchedule $schedule): void
    {
        $contract = $schedule->contract;
        $tenant = $contract->tenant;
        $agency = $contract->agency;

        // SMS si activé
        if (\App\Models\Setting::get('sms_notifications', false, $agency->id) && $tenant->phone) {
            $this->smsService->sendPaymentReminder($tenant->phone, [
                'amount' => number_format($schedule->amount, 0, ',', ' ') . ' FCFA',
                'due_date' => $schedule->due_date->format('d/m/Y'),
                'contract_number' => $contract->contract_number,
            ]);
        }

        // WhatsApp si activé
        if (\App\Models\Setting::get('whatsapp_notifications', false, $agency->id) && $tenant->phone) {
            $this->whatsAppService->sendPaymentReminder($tenant->phone, [
                'amount' => number_format($schedule->amount, 0, ',', ' ') . ' FCFA',
                'due_date' => $schedule->due_date->format('d/m/Y'),
                'contract_number' => $contract->contract_number,
            ]);
        }
    }
}

