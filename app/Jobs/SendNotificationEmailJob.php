<?php

namespace App\Jobs;

use App\Mail\NotificationMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNotificationEmailJob implements ShouldQueue
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
        public string $recipientEmail,
        public string $recipientName,
        public string $subject,
        public string $message,
        public string $template = 'notification',
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->recipientEmail)
            ->send(new NotificationMail($this->subject, $this->message, $this->template));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Échec envoi email notification', [
            'recipient' => $this->recipientEmail,
            'subject' => $this->subject,
            'error' => $exception->getMessage(),
        ]);
    }
}
