<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
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
        return $user->can('view payments');
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->can('view payments')
            && $this->belongsToSameAgency($user, $payment);
    }

    public function create(User $user): bool
    {
        return $user->can('create payments');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->can('edit payments')
            && $this->belongsToSameAgency($user, $payment);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->can('delete payments')
            && $this->belongsToSameAgency($user, $payment);
    }

    /**
     * Payment n'a pas de agency_id direct — on passe par le contrat associe.
     */
    private function belongsToSameAgency(User $user, Payment $payment): bool
    {
        $contractAgencyId = $payment->contract?->agency_id;

        return $user->agency_id !== null
            && $contractAgencyId !== null
            && $user->agency_id === $contractAgencyId;
    }
}
