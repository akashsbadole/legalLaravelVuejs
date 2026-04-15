<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip 2FA checks for client users
        if ($user && $user->two_factor_enabled && ! $user->isClient() && ! session('two_factor_verified')) {
            return redirect()->route('two-factor.verify');
        }

        return $next($request);
    }
}
