<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Agency;
use Illuminate\Console\Command;

class UpdateUsersAgency extends Command
{
    protected $signature = 'users:assign-agency {--agency-id=}';
    protected $description = 'Assigne une agence aux utilisateurs qui n\'en ont pas';

    public function handle()
    {
        $agencyId = $this->option('agency-id');
        
        if (!$agencyId) {
            $agency = Agency::first();
            if (!$agency) {
                $this->error('Aucune agence trouvée. Veuillez créer une agence d\'abord.');
                return 1;
            }
            $agencyId = $agency->id;
        } else {
            $agency = Agency::find($agencyId);
            if (!$agency) {
                $this->error("Agence avec l'ID {$agencyId} introuvable.");
                return 1;
            }
        }

        $usersWithoutAgency = User::whereNull('agency_id')->get();
        
        if ($usersWithoutAgency->isEmpty()) {
            $this->info('Tous les utilisateurs ont déjà une agence assignée.');
            return 0;
        }

        $count = 0;
        foreach ($usersWithoutAgency as $user) {
            $user->update(['agency_id' => $agencyId]);
            $this->info("Utilisateur {$user->name} ({$user->email}) assigné à l'agence {$agency->name}");
            $count++;
        }

        $this->info("\n✅ {$count} utilisateur(s) mis à jour avec succès.");
        return 0;
    }
}
