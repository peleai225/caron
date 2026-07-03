<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAdminRoutes
{
    /**
     * Routes administratives interdites aux propriétaires et locataires
     */
    protected $restrictedRoutes = [
        'properties',
        'tenants',
        'contracts',
        'rents',
        'owners',
        'expenses',
        'accounts',
        'transactions',
        'invoices',
        'document-templates',
        'ocr',
        'activity-logs',
        'agencies',
        'penalties',
        'etat-des-lieux',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $isOwner = $user->hasRole('proprietaire');
        $isTenant = $user->hasRole('locataire');
        $isChargeRecouvrement = $user->hasRole('charge_recouvrement');
        $isAgentImmobilier = $user->hasRole('agent_immobilier');

        $routePath = $request->path();
        $firstSegment = explode('/', $routePath)[0] ?? '';

        // Chargé de recouvrement : uniquement Loyers, Litiges, Pénalités, Factures (ROLES_BACKOFFICE)
        if ($isChargeRecouvrement) {
            $allowed = ['rents', 'litiges', 'penalties', 'invoices'];
            if (!in_array($firstSegment, $allowed)) {
                return redirect()->route('dashboard')->with('error', 'Accès refusé. Vous n\'avez pas la permission d\'accéder à cette page.');
            }
            return $next($request);
        }

        // Agent immobilier : Biens (lecture), Locataires, Contrats, États des lieux, Loyers, Propriétaires (lecture)
        if ($isAgentImmobilier) {
            $allowed = ['properties', 'tenants', 'contracts', 'etat-des-lieux', 'rents', 'owners'];
            if (!in_array($firstSegment, $allowed)) {
                return redirect()->route('dashboard')->with('error', 'Accès refusé. Vous n\'avez pas la permission d\'accéder à cette page.');
            }
            return $next($request);
        }

        // Si l'utilisateur n'est ni propriétaire ni locataire, laisser passer (super_admin, admin_agence, gestionnaire, comptable)
        if (!$isOwner && !$isTenant) {
            return $next($request);
        }

        // Vérifier si la route actuelle est restreinte

        // Exceptions : routes autorisées pour les propriétaires et locataires
        $allowedRoutes = [
            'profile',
            'notifications',
            'tenant',
            'owner',
            'accountant',
            'dashboard',
            'reports', // Les propriétaires peuvent accéder aux rapports
        ];

        // Si c'est une route autorisée, laisser passer
        if (in_array($firstSegment, $allowedRoutes) || str_starts_with($routePath, 'tenant/') || str_starts_with($routePath, 'owner/')) {
            return $next($request);
        }

        // Vérifier si c'est une route restreinte
        foreach ($this->restrictedRoutes as $restrictedRoute) {
            if ($firstSegment === $restrictedRoute || str_starts_with($routePath, $restrictedRoute)) {
                // Bloquer l'accès
                return redirect()->route($isTenant ? 'login' : 'owner.dashboard')
                    ->with('error', 'Accès refusé. Vous n\'avez pas la permission d\'accéder à cette page.');
            }
        }

        return $next($request);
    }
}
