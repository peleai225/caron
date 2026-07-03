<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.tailwind');

        $this->configureMailFromDatabase();
    }

    private function configureMailFromDatabase(): void
    {
        try {
            // Vérifier que la table settings existe avant de lire (évite crash pendant migrate)
            if (!Schema::hasTable('settings')) {
                return;
            }

            $host = Setting::get('mail_host');

            // Si aucun host configuré en base, on garde le .env tel quel
            if (!$host) {
                return;
            }

            $mailer     = Setting::get('mail_mailer', 'smtp');
            $port       = Setting::get('mail_port', 587);
            $encryption = Setting::get('mail_encryption', 'tls');
            $username   = Setting::get('mail_username');
            $password   = Setting::get('mail_password');
            $fromAddr   = Setting::get('mail_from_address', config('mail.from.address'));
            $fromName   = Setting::get('mail_from_name', config('app.name'));

            // Reconfigurer le mailer dynamiquement
            config([
                'mail.default'                     => $mailer,
                'mail.mailers.smtp.host'           => $host,
                'mail.mailers.smtp.port'           => (int) $port,
                'mail.mailers.smtp.encryption'     => $encryption,
                'mail.mailers.smtp.username'       => $username,
                'mail.mailers.smtp.password'       => $password,
                'mail.from.address'                => $fromAddr,
                'mail.from.name'                   => $fromName,
            ]);
        } catch (\Throwable $e) {
            // Ne jamais planter l'appli si la config mail échoue
        }
    }
}
