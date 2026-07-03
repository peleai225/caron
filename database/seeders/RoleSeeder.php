<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les permissions
        $permissions = [
            // Properties
            'view properties',
            'create properties',
            'edit properties',
            'delete properties',
            
            // Tenants
            'view tenants',
            'create tenants',
            'edit tenants',
            'delete tenants',
            
            // Contracts
            'view contracts',
            'create contracts',
            'edit contracts',
            'delete contracts',
            'sign contracts',
            
            // Payments
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',
            
            // Reports
            'view reports',
            'export reports',
            
            // Owners
            'view owners',
            'create owners',
            'edit owners',
            'delete owners',
            
            // Financial
            'view finances',
            'manage finances',
            
            // Admin
            'manage users',
            'manage agencies',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Créer les rôles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        $adminAgency = Role::firstOrCreate(['name' => 'admin_agence']);
        $adminAgency->givePermissionTo([
            'view properties', 'create properties', 'edit properties', 'delete properties',
            'view tenants', 'create tenants', 'edit tenants', 'delete tenants',
            'view contracts', 'create contracts', 'edit contracts', 'delete contracts', 'sign contracts',
            'view payments', 'create payments', 'edit payments', 'delete payments',
            'view reports', 'export reports',
            'view owners', 'create owners', 'edit owners', 'delete owners',
            'view finances', 'manage finances',
            'manage users',
        ]);

        $gestionnaire = Role::firstOrCreate(['name' => 'gestionnaire']);
        $gestionnaire->givePermissionTo([
            'view properties', 'create properties', 'edit properties',
            'view tenants', 'create tenants', 'edit tenants',
            'view contracts', 'create contracts', 'edit contracts',
            'view payments', 'create payments', 'edit payments',
            'view reports',
            'view owners', 'create owners', 'edit owners',
        ]);

        $proprietaire = Role::firstOrCreate(['name' => 'proprietaire']);
        $proprietaire->givePermissionTo([
            'view properties',
            'view contracts',
            'view payments',
            'view reports',
        ]);

        $chargeRecouvrement = Role::firstOrCreate(['name' => 'charge_recouvrement']);
        $chargeRecouvrement->givePermissionTo([
            'view tenants', 'edit tenants',
            'view contracts', 'view payments', 'create payments', 'edit payments',
            'view owners',
            'view reports', 'export reports',
        ]);

        $agentImmobilier = Role::firstOrCreate(['name' => 'agent_immobilier']);
        $agentImmobilier->syncPermissions([
            'view properties', 'edit properties',
            'view tenants', 'create tenants', 'edit tenants',
            'view contracts', 'create contracts', 'edit contracts', 'sign contracts',
            'view payments', 'create payments',
            'view owners',
        ]);

        $comptable = Role::firstOrCreate(['name' => 'comptable']);
        $comptable->givePermissionTo([
            'view payments',
            'view reports', 'export reports',
            'view finances', 'manage finances',
        ]);
    }
}

