<?php

namespace App\Policies;

use App\Models\Litige;
use App\Models\User;

class LitigePolicy
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

    /**
     * Litige n'a pas de permission Spatie dediee.
     * L'acces est accorde a tout utilisateur authentifie rattache a une agence.
     */
    public function viewAny(User $user): bool
    {
        return $user->agency_id !== null;
    }

    public function view(User $user, Litige $litige): bool
    {
        return $this->belongsToSameAgency($user, $litige);
    }

    public function create(User $user): bool
    {
        return $user->agency_id !== null;
    }

    public function update(User $user, Litige $litige): bool
    {
        return $this->belongsToSameAgency($user, $litige);
    }

    public function delete(User $user, Litige $litige): bool
    {
        return $this->belongsToSameAgency($user, $litige);
    }

    private function belongsToSameAgency(User $user, Litige $litige): bool
    {
        return $user->agency_id !== null
            && $user->agency_id === $litige->agency_id;
    }
}
