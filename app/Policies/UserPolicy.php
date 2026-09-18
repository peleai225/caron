<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Super admin a acces total.
     */
    public function before(User $currentUser, string $ability): ?bool
    {
        if ($currentUser->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $currentUser): bool
    {
        return $currentUser->can('manage users');
    }

    public function view(User $currentUser, User $user): bool
    {
        return $currentUser->can('manage users')
            && $this->belongsToSameAgency($currentUser, $user);
    }

    public function create(User $currentUser): bool
    {
        return $currentUser->can('manage users');
    }

    public function update(User $currentUser, User $user): bool
    {
        return $currentUser->can('manage users')
            && $this->belongsToSameAgency($currentUser, $user);
    }

    public function delete(User $currentUser, User $user): bool
    {
        // Un utilisateur ne peut pas se supprimer lui-meme
        if ($currentUser->id === $user->id) {
            return false;
        }

        return $currentUser->can('manage users')
            && $this->belongsToSameAgency($currentUser, $user);
    }

    private function belongsToSameAgency(User $currentUser, User $user): bool
    {
        return $currentUser->agency_id !== null
            && $currentUser->agency_id === $user->agency_id;
    }
}
