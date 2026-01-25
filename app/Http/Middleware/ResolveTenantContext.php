<?php

namespace App\Http\Middleware;

use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            $tenantId = $user->current_tenant_id;

            if (! $tenantId) {
                $tenantId = $user->tenants()->value('tenants.id');
            }

            if ($tenantId) {
                $tenant = $user->tenants()->whereKey($tenantId)->first();

                if ($tenant) {
                    app(TenantContext::class)->set($tenant);
                }
            }
        }

        return $next($request);
    }
}
