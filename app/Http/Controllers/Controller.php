<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function getAgencyId(): ?int
    {
        return auth()->user()?->agency_id;
    }

    /**
     * Retourne l'agency_id de l'utilisateur, ou null pour le super_admin.
     * Interrompt avec 403 si un utilisateur non-super_admin n'a pas d'agence.
     */
    protected function requireAgencyId(): ?int
    {
        $user = auth()->user();

        if ($user?->hasRole('super_admin')) {
            return $user->agency_id;
        }

        $agencyId = $this->getAgencyId();

        if (!$agencyId) {
            abort(403, 'Vous devez être associé à une agence pour accéder à cette fonctionnalité.');
        }

        return $agencyId;
    }

    /**
     * Vérifie qu'un enregistrement appartient à l'agence courante.
     * Le super_admin (agency_id = null) a accès à tout.
     */
    protected function authorizeAgency(?int $modelAgencyId): void
    {
        $agencyId = $this->getAgencyId();
        if ($agencyId !== null && $modelAgencyId !== $agencyId) {
            abort(403);
        }
    }
}
