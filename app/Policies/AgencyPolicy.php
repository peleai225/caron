<?php

namespace App\Policies;

use App\Models\Agency;
use App\Models\User;

class AgencyPolicy
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
        return $user->can('manage agencies');
    }

    /**
     * Un utilisateur peut voir sa propre agence,
     * ou toutes les agences s'il a la permission 'manage agencies'.
     */
    public function view(User $user, Agency $agency): bool
    {
        if ($user->can('manage agencies')) {
            return true;
        }

        return $user->agency_id !== null
            && $user->agency_id === $agency->id;
    }

    public function create(User $user): bool
    {
        return $user->can('manage agencies');
    }

    public function update(User $user, Agency $agency): bool
    {
        return $user->can('manage agencies');
    }

    public function delete(User $user, Agency $agency): bool
    {
        return $user->can('manage agencies');
    }
}
