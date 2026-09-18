<?php

namespace App\Policies;

use App\Models\Tenant;
use App\Models\User;

class TenantPolicy
{
    /**
     * Super admin a acces total.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view tenants');
    }

    public function view(User $user, Tenant $tenant): bool
    {
        return $user->can('view tenants')
            && $this->belongsToSameAgency($user, $tenant);
    }

    public function create(User $user): bool
    {
        return $user->can('create tenants');
    }

    public function update(User $user, Tenant $tenant): bool
    {
        return $user->can('edit tenants')
            && $this->belongsToSameAgency($user, $tenant);
    }

    public function delete(User $user, Tenant $tenant): bool
    {
        return $user->can('delete tenants')
            && $this->belongsToSameAgency($user, $tenant);
    }

    private function belongsToSameAgency(User $user, Tenant $tenant): bool
    {
        return $user->agency_id !== null
            && $user->agency_id === $tenant->agency_id;
    }
}
