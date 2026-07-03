<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Agency;
use App\Models\Owner;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class DemoAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer ou créer l'agence
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

        $this->command->info("=== Création de comptes de démonstration ===");

        // 1. Créer un propriétaire avec compte
        $this->createOwnerAccount($agency);
        
        // 2. Créer des locataires (données uniquement, sans comptes utilisateur)
        $this->createTenants($agency);

        $this->command->info("\n✅ Comptes de démonstration créés avec succès !");
        $this->command->info("\n📋 Identifiants de connexion :");
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->command->info("👤 PROPRIÉTAIRE:");
        $this->command->info("   Email: proprietaire@caron.com");
        $this->command->info("   Mot de passe: password");
        $this->command->info("   Dashboard: /owner/dashboard");
        $this->command->info("\n👤 ADMIN: admin@caron.com | DGA: dga@caron.com | GLOC: gloc@caron.com | Chargé recouvrement: char@caron.com | Agent: agentimmo@caron.com");
        $this->command->info("   Mot de passe: password");
        $this->command->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
    }

    /**
     * Crée un propriétaire avec compte utilisateur
     */
    private function createOwnerAccount(Agency $agency): void
    {
        // Créer le propriétaire
        $owner = Owner::firstOrCreate(
            ['email' => 'proprietaire@caron.com'],
            [
                'agency_id' => $agency->id,
                'name' => 'Jean Dupont',
                'phone' => '+225 07 11 22 33 44',
                'address' => 'Cocody, Abidjan',
                'identification_number' => 'CI123456789',
                'is_active' => true,
            ]
        );

        // Créer l'utilisateur propriétaire
        $ownerUser = User::updateOrCreate(
            ['email' => 'proprietaire@caron.com'],
            [
                'name' => 'Jean Dupont',
                'password' => Hash::make('password'),
                'agency_id' => $agency->id,
                'phone' => '+225 07 11 22 33 44',
            ]
        );

        // Assigner le rôle propriétaire
        $ownerRole = Role::firstOrCreate(['name' => 'proprietaire']);
        if (!$ownerUser->hasRole('proprietaire')) {
            $ownerUser->assignRole($ownerRole);
        }

        // Créer des biens pour le propriétaire
        $property1 = Property::firstOrCreate(
            [
                'agency_id' => $agency->id,
                'owner_id' => $owner->id,
                'address' => 'Cocody Angré 7ème Tranche, Abidjan',
            ],
            [
                'type' => 'maison',
                'city' => 'Abidjan',
                'neighborhood' => 'Cocody Angré',
                'monthly_rent' => 150000,
                'bedrooms' => 3,
                'bathrooms' => 2,
                'surface' => 120,
                'status' => 'occupe',
                'description' => 'Bel appartement moderne avec balcon',
                'is_active' => true,
            ]
        );

        $property2 = Property::firstOrCreate(
            [
                'agency_id' => $agency->id,
                'owner_id' => $owner->id,
                'address' => 'Yopougon Sicogi, Abidjan',
            ],
            [
                'type' => 'maison',
                'city' => 'Abidjan',
                'neighborhood' => 'Yopougon',
                'monthly_rent' => 250000,
                'bedrooms' => 4,
                'bathrooms' => 3,
                'surface' => 200,
                'status' => 'occupe',
                'description' => 'Villa spacieuse avec jardin',
                'is_active' => true,
            ]
        );

        $this->command->info("✅ Propriétaire créé: {$owner->name} ({$owner->email})");
        $this->command->info("   - 2 biens immobiliers créés");
    }

    /**
     * Crée des locataires (données uniquement, sans comptes utilisateur - comptes locataires désactivés)
     */
    private function createTenants(Agency $agency): void
    {
        // Locataire 1
        $tenant1 = Tenant::firstOrCreate(
            ['email' => 'locataire1@caron.com'],
            [
                'agency_id' => $agency->id,
                'first_name' => 'Marie',
                'last_name' => 'Kouassi',
                'phone' => '+225 07 22 33 44 55',
                'cni_number' => 'CI987654321',
                'status' => 'actif',
                'user_id' => null,
            ]
        );
        $tenant1->update(['user_id' => null]);

        // Locataire 2
        $tenant2 = Tenant::firstOrCreate(
            ['email' => 'locataire2@caron.com'],
            [
                'agency_id' => $agency->id,
                'first_name' => 'Amadou',
                'last_name' => 'Traoré',
                'phone' => '+225 07 33 44 55 66',
                'cni_number' => 'CI456789123',
                'status' => 'actif',
                'user_id' => null,
            ]
        );
        $tenant2->update(['user_id' => null]);

        // Créer des contrats et paiements pour les locataires
        $this->createContractsAndPayments($agency, $tenant1, $tenant2);

        $this->command->info("✅ Locataire 1 créé: {$tenant1->full_name} ({$tenant1->email})");
        $this->command->info("✅ Locataire 2 créé: {$tenant2->full_name} ({$tenant2->email})");
    }

    /**
     * Crée des contrats et paiements pour les locataires
     */
    private function createContractsAndPayments(Agency $agency, Tenant $tenant1, Tenant $tenant2): void
    {
        // Récupérer les propriétés
        $property1 = Property::where('address', 'Cocody Angré 7ème Tranche, Abidjan')->first();
        $property2 = Property::where('address', 'Yopougon Sicogi, Abidjan')->first();

        if (!$property1 || !$property2) {
            return;
        }

        // Contrat pour locataire 1
        $contract1 = Contract::firstOrCreate(
            [
                'agency_id' => $agency->id,
                'tenant_id' => $tenant1->id,
                'property_id' => $property1->id,
            ],
            [
                'owner_id' => $property1->owner_id,
                'contract_number' => 'CT-' . now()->format('Y') . '-' . str_pad(1, 6, '0', STR_PAD_LEFT),
                'rent_amount' => 150000,
                'deposit' => 300000,
                'start_date' => Carbon::now()->subMonths(6),
                'end_date' => Carbon::now()->addMonths(6),
                'status' => 'active',
                'payment_frequency' => 'monthly',
                'payment_day' => 5,
            ]
        );

        // Contrat pour locataire 2
        $contract2 = Contract::firstOrCreate(
            [
                'agency_id' => $agency->id,
                'tenant_id' => $tenant2->id,
                'property_id' => $property2->id,
            ],
            [
                'owner_id' => $property2->owner_id,
                'contract_number' => 'CT-' . now()->format('Y') . '-' . str_pad(2, 6, '0', STR_PAD_LEFT),
                'rent_amount' => 250000,
                'deposit' => 500000,
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(9),
                'status' => 'active',
                'payment_frequency' => 'monthly',
                'payment_day' => 5,
            ]
        );

        // Créer des paiements pour le locataire 1
        for ($i = 5; $i >= 0; $i--) {
            $paymentDate = Carbon::now()->subMonths($i);
            Payment::firstOrCreate(
                [
                    'contract_id' => $contract1->id,
                    'period' => $paymentDate->format('Y-m'),
                ],
                [
                    'amount' => 150000,
                    'payment_date' => $paymentDate,
                    'payment_method' => 'cash',
                    'status' => 'completed',
                ]
            );
        }

        // Créer un paiement en attente pour le locataire 1
        Payment::firstOrCreate(
            [
                'contract_id' => $contract1->id,
                'period' => Carbon::now()->format('Y-m'),
            ],
            [
                'amount' => 150000,
                'payment_date' => null,
                'payment_method' => null,
                'status' => 'pending',
            ]
        );

        // Créer des paiements pour le locataire 2
        for ($i = 2; $i >= 0; $i--) {
            $paymentDate = Carbon::now()->subMonths($i);
            Payment::firstOrCreate(
                [
                    'contract_id' => $contract2->id,
                    'period' => $paymentDate->format('Y-m'),
                ],
                [
                    'amount' => 250000,
                    'payment_date' => $paymentDate,
                    'payment_method' => 'cynetpay',
                    'status' => 'completed',
                ]
            );
        }

        // Créer des échéanciers pour les prochains mois
        $this->createPaymentSchedules($contract1, $contract2);

        $this->command->info("   - Contrats et paiements créés pour les locataires");
    }

    /**
     * Crée des échéanciers de paiement
     */
    private function createPaymentSchedules(Contract $contract1, Contract $contract2): void
    {
        // Échéanciers pour le contrat 1 (prochains 3 mois)
        for ($i = 1; $i <= 3; $i++) {
            PaymentSchedule::firstOrCreate(
                [
                    'contract_id' => $contract1->id,
                    'due_date' => Carbon::now()->addMonths($i)->startOfMonth()->addDays(4),
                ],
                [
                    'amount' => 150000,
                    'status' => 'pending',
                ]
            );
        }

        // Échéanciers pour le contrat 2 (prochains 3 mois)
        for ($i = 1; $i <= 3; $i++) {
            PaymentSchedule::firstOrCreate(
                [
                    'contract_id' => $contract2->id,
                    'due_date' => Carbon::now()->addMonths($i)->startOfMonth()->addDays(4),
                ],
                [
                    'amount' => 250000,
                    'status' => 'pending',
                ]
            );
        }
    }
}

