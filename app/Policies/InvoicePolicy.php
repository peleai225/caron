<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
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

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->can('view finances')
            && $this->belongsToSameAgency($user, $invoice);
    }

    public function create(User $user): bool
    {
        return $user->can('manage finances');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->can('manage finances')
            && $this->belongsToSameAgency($user, $invoice);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->can('manage finances')
            && $this->belongsToSameAgency($user, $invoice);
    }

    /**
     * Invoice n'a pas de agency_id direct — on passe par le contrat associe.
     */
    private function belongsToSameAgency(User $user, Invoice $invoice): bool
    {
        $contractAgencyId = $invoice->contract?->agency_id;

        return $user->agency_id !== null
            && $contractAgencyId !== null
            && $user->agency_id === $contractAgencyId;
    }
}
