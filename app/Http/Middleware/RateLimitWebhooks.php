<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitWebhooks
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'webhook:'.$request->ip().':'.$request->path();

        if (RateLimiter::tooManyAttempts($key, 60)) { // 60 requests per minute
            return response('Too Many Requests', 429, [
                'Retry-After' => RateLimiter::availableIn($key),
            ]);
        }

        RateLimiter::hit($key, 60); // Reset every 60 seconds

        return $next($request);
    }
}
