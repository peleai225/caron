<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;

class ContractPolicy
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
        return $user->can('view contracts');
    }

    public function view(User $user, Contract $contract): bool
    {
        return $user->can('view contracts')
            && $this->belongsToSameAgency($user, $contract);
    }

    public function create(User $user): bool
    {
        return $user->can('create contracts');
    }

    public function update(User $user, Contract $contract): bool
    {
        return $user->can('edit contracts')
            && $this->belongsToSameAgency($user, $contract);
    }

    public function delete(User $user, Contract $contract): bool
    {
        return $user->can('delete contracts')
            && $this->belongsToSameAgency($user, $contract);
    }

    /**
     * Verifie si l'utilisateur peut signer un contrat.
     */
    public function sign(User $user, Contract $contract): bool
    {
        return $user->can('sign contracts')
            && $this->belongsToSameAgency($user, $contract);
    }

    private function belongsToSameAgency(User $user, Contract $contract): bool
    {
        return $user->agency_id !== null
            && $user->agency_id === $contract->agency_id;
    }
}
