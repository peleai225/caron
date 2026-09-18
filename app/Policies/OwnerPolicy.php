<?php

namespace App\Policies;

use App\Models\Owner;
use App\Models\User;

class OwnerPolicy
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
        return $user->can('view owners');
    }

    public function view(User $user, Owner $owner): bool
    {
        return $user->can('view owners')
            && $this->belongsToSameAgency($user, $owner);
    }

    public function create(User $user): bool
    {
        return $user->can('create owners');
    }

    public function update(User $user, Owner $owner): bool
    {
        return $user->can('edit owners')
            && $this->belongsToSameAgency($user, $owner);
    }

    public function delete(User $user, Owner $owner): bool
    {
        return $user->can('delete owners')
            && $this->belongsToSameAgency($user, $owner);
    }

    private function belongsToSameAgency(User $user, Owner $owner): bool
    {
        return $user->agency_id !== null
            && $user->agency_id === $owner->agency_id;
    }
}
