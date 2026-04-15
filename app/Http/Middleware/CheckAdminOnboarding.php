<?php

namespace App\Http\Middleware;

use App\Models\AdminOnboarding;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminOnboarding
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->role === 'admin') {
            $currentRoute = $request->route() ? $request->route()->getName() : '';
            $isOnboardingRoute = in_array($currentRoute, ['admin.onboarding', 'admin.onboarding.next', 'admin.onboarding.skip']);

            if (! $isOnboardingRoute && ! $this->hasCompletedOnboarding($user)) {
                return redirect()->route('admin.onboarding');
            }
        }

        return $next($request);
    }

    private function hasCompletedOnboarding($user): bool
    {
        $onboarding = AdminOnboarding::where('admin_id', $user->id)->first();

        return $onboarding && $onboarding->is_completed;
    }
}
