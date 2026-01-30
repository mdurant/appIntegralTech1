<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiAccess
{
    /**
     * Ensure the authenticated user has API access (subscription tier is not Independent).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasApiAccess()) {
            return response()->json([
                'message' => __('El acceso a la API requiere un plan Pro o Enterprise.'),
            ], 403);
        }

        return $next($request);
    }
}
