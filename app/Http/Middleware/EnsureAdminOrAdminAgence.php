<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrAdminAgence
{
    /**
     * Restreint l'accès aux routes Administration (agences, logs, comptes locataires)
     * aux seuls rôles super_admin et admin_agence. Le gestionnaire est bloqué.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->hasRole('super_admin') || $user->hasRole('admin_agence')) {
            return $next($request);
        }

        abort(403, 'Accès refusé. Cette section est réservée à l\'administrateur principal ou à l\'administrateur d\'agence.');
    }
}
