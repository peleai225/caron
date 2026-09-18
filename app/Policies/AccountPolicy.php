<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;

class AccountPolicy
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
        return $user->can('view finances');
    }

    public function view(User $user, Account $account): bool
    {
        return $user->can('view finances')
            && $this->belongsToSameAgency($user, $account);
    }

    public function create(User $user): bool
    {
        return $user->can('manage finances');
    }

    public function update(User $user, Account $account): bool
    {
        return $user->can('manage finances')
            && $this->belongsToSameAgency($user, $account);
    }

    public function delete(User $user, Account $account): bool
    {
        return $user->can('manage finances')
            && $this->belongsToSameAgency($user, $account);
    }

    private function belongsToSameAgency(User $user, Account $account): bool
    {
        return $user->agency_id !== null
            && $user->agency_id === $account->agency_id;
    }
}
