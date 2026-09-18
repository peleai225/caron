<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
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
        return $user->can('view properties');
    }

    public function view(User $user, Property $property): bool
    {
        return $user->can('view properties')
            && $this->belongsToSameAgency($user, $property);
    }

    public function create(User $user): bool
    {
        return $user->can('create properties');
    }

    public function update(User $user, Property $property): bool
    {
        return $user->can('edit properties')
            && $this->belongsToSameAgency($user, $property);
    }

    public function delete(User $user, Property $property): bool
    {
        return $user->can('delete properties')
            && $this->belongsToSameAgency($user, $property);
    }

    /**
     * Verifie que l'utilisateur appartient a la meme agence que le bien.
     */
    private function belongsToSameAgency(User $user, Property $property): bool
    {
        return $user->agency_id !== null
            && $user->agency_id === $property->agency_id;
    }
}
