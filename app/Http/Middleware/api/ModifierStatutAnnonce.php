<?php

namespace App\Http\Middleware\api;

use Closure;
use App\Models\Annonce;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModifierStatutAnnonce
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Récupérer les annonces dont la date limite est dépassée
        $annoncesExpirees = Annonce::where('date_limite', '<', now())->get();

        // Mettre à jour le statut de chaque annonce à 0
        foreach ($annoncesExpirees as $annonce) {
            $annonce->statut = 0;
            $annonce->save();
        }
        return $next($request);
    }
}
