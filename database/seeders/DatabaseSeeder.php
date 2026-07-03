<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Agency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer les rôles et permissions
        $this->call(RoleSeeder::class);

        // Importer les templates de documents système
        $this->call(DocumentTemplateSeeder::class);

        // Créer une agence de démonstration
        $agency = Agency::firstOrCreate(
            ['email' => 'contact@caron.com'],
            [
                'name' => 'Agence Principale',
                'phone' => '+225 07 12 34 56 78',
                'address' => 'Abidjan, Côte d\'Ivoire',
                'city' => 'Abidjan',
                'country' => 'CI',
                'is_active' => true,
            ]
        );

        // Créer un utilisateur Super Admin (agency_id = null → accès global toutes agences)
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@caron.com'],
            [
                'name' => 'Super Administrateur',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'agency_id' => null,
            ]
        );
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        // Créer un utilisateur DG Agence (DGA)
        $adminAgency = User::updateOrCreate(
            ['email' => 'dga@caron.com'],
            [
                'name' => 'DG Agence',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'agency_id' => $agency->id,
            ]
        );
        if (!$adminAgency->hasRole('admin_agence')) {
            $adminAgency->assignRole('admin_agence');
        }

        // Créer un utilisateur Gestionnaire locatif (GLOC)
        $gestionnaire = User::updateOrCreate(
            ['email' => 'gloc@caron.com'],
            [
                'name' => 'Gestionnaire locatif',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'agency_id' => $agency->id,
            ]
        );
        if (!$gestionnaire->hasRole('gestionnaire')) {
            $gestionnaire->assignRole('gestionnaire');
        }

        // Créer un utilisateur Comptable
        $comptable = User::updateOrCreate(
            ['email' => 'comptable@caron.com'],
            [
                'name' => 'Comptable',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'agency_id' => $agency->id,
            ]
        );
        if (!$comptable->hasRole('comptable')) {
            $comptable->assignRole('comptable');
        }

        // Chargé de recouvrement (char@caron.com)
        $chargeRecouvrement = User::updateOrCreate(
            ['email' => 'char@caron.com'],
            [
                'name' => 'Chargé de recouvrement',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'agency_id' => $agency->id,
            ]
        );
        if (!$chargeRecouvrement->hasRole('charge_recouvrement')) {
            $chargeRecouvrement->assignRole('charge_recouvrement');
        }

        // Agent immobilier (agentimmo@caron.com)
        $agentImmobilier = User::updateOrCreate(
            ['email' => 'agentimmo@caron.com'],
            [
                'name' => 'Agent immobilier',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'agency_id' => $agency->id,
            ]
        );
        if (!$agentImmobilier->hasRole('agent_immobilier')) {
            $agentImmobilier->assignRole('agent_immobilier');
        }

        // Créer des comptes de démonstration (propriétaires)
        $this->call(DemoAccountsSeeder::class);
    }
}
