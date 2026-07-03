<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountantRole
{
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

        // Vérifier si l'utilisateur a le rôle comptable
        if (!$user->hasRole('comptable')) {
            // Rediriger vers le dashboard principal avec un message d'erreur
            return redirect()->route('dashboard')
                ->with('error', 'Accès refusé. Vous devez être un comptable pour accéder à cette page. Contactez votre administrateur pour obtenir les droits d\'accès.');
        }

        return $next($request);
    }
}
