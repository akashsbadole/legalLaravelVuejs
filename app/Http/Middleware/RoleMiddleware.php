<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $userRole = $request->user()->role;

        $roles = [
            'admin' => ['admin'],
            'attorney' => ['admin', 'attorney'],
            'paralegal' => ['admin', 'attorney', 'paralegal'],
            'client' => ['admin', 'attorney', 'paralegal', 'client'],
            'billing_manager' => ['admin', 'billing_manager'],
        ];

        $allowedRoles = $roles[$role] ?? [$role];

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}