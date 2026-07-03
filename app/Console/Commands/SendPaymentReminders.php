<?php

namespace App\Console\Commands;

use App\Models\PaymentSchedule;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendPaymentReminders extends Command
{
    protected $signature   = 'reminders:send {--days=3 : Nombre de jours avant échéance}';
    protected $description = 'Envoie des rappels pour les loyers à venir et les retards';

    public function __construct(protected NotificationService $notificationService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $days = (int) $this->option('days');

        // 1. Rappels J-N (loyers qui arrivent à échéance)
        $upcoming = PaymentSchedule::with(['contract.tenant', 'contract.agency'])
            ->where('status', 'pending')
            ->whereBetween('due_date', [now()->startOfDay(), now()->addDays($days)->endOfDay()])
            ->get();

        foreach ($upcoming as $schedule) {
            try {
                $this->notificationService->sendPaymentReminder($schedule);
            } catch (\Throwable $e) {
                $this->error("Échéance {$schedule->id} : " . $e->getMessage());
            }
        }

        $this->info("Rappels J-{$days} : {$upcoming->count()} envoyé(s).");

        // 2. Relances retards (échéances dépassées non payées)
        $overdue = PaymentSchedule::with(['contract.tenant', 'contract.property', 'contract.agency'])
            ->overdue()
            ->get();

        foreach ($overdue as $schedule) {
            try {
                $this->notificationService->sendPaymentReminder($schedule);
            } catch (\Throwable $e) {
                $this->error("Retard échéance {$schedule->id} : " . $e->getMessage());
            }
        }

        $this->info("Relances retard : {$overdue->count()} traité(s).");

        return self::SUCCESS;
    }
}
