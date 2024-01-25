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
        $annonces = Annonce::all();
        foreach ($annonces as $annonce) {
            if ($annonce->date_limite <  now()) {
                $annonce->statut = 0;
            } elseif ($annonce->date_limite >  now()) {
                $annonce->statut = 1;
            }
            $annonce->save();
        }
        return $next($request);
    }
}
