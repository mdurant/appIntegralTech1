<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotClient
{
    /**
     * Evita que el perfil Cliente acceda al marketplace de servicios (solo usuarios/providers pueden simular pago y ver datos de contacto).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isClient()) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
