<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
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

    public function view(User $user, Expense $expense): bool
    {
        return $user->can('view finances')
            && $this->belongsToSameAgency($user, $expense);
    }

    public function create(User $user): bool
    {
        return $user->can('manage finances');
    }

    public function update(User $user, Expense $expense): bool
    {
        return $user->can('manage finances')
            && $this->belongsToSameAgency($user, $expense);
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->can('manage finances')
            && $this->belongsToSameAgency($user, $expense);
    }

    private function belongsToSameAgency(User $user, Expense $expense): bool
    {
        return $user->agency_id !== null
            && $user->agency_id === $expense->agency_id;
    }
}
