<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->route('tenant');

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);

            if (!$tenant) {
                abort(404, 'Tenant not found');
            }

            // Set current tenant in app container
            app()->instance('currentTenant', $tenant);

            // Or use config
            config(['app.current_tenant' => $tenant]);
        }

        return $next($request);
    }
}