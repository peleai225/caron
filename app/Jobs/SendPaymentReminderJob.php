<?php

namespace App\Jobs;

use App\Models\PaymentSchedule;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPaymentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre de tentatives maximum.
     */
    public int $tries = 3;

    /**
     * Délais entre les tentatives (en secondes).
     */
    public array $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public PaymentSchedule $schedule,
    ) {
        $this->onQueue('reminders');
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        $notificationService->sendPaymentReminder($this->schedule);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Échec envoi rappel de paiement', [
            'schedule_id' => $this->schedule->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
