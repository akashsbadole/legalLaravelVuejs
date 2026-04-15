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
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = $request->route('tenant');

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);

            if (! $tenant) {
                abort(404, 'Tenant not found');
            }

            // Set current tenant in app container
            app()->instance('currentTenant', $tenant);
            config(['app.current_tenant' => $tenant]);
        } elseif (auth()->check() && ! config('app.current_tenant')) {
            // If no tenant from route but user is authenticated, set tenant from user
            $user = auth()->user();
            if ($user && $user->tenant_id) {
                $tenant = Tenant::find($user->tenant_id);
                if ($tenant) {
                    app()->instance('currentTenant', $tenant);
                    config(['app.current_tenant' => $tenant]);
                }
            }
        }

        return $next($request);
    }
}
