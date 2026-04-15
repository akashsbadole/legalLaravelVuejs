<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $method = $request->method();
        $path = $request->path();
        $ip = $request->ip();

        // Skip logging for certain routes
        $skipRoutes = [
            'sanctum/csrf-cookie',
            '_debugbar',
            'api/user',
            'api/settings',
            'api/notifications',
            'api/dashboard/stats',
            'api/calendar/events',
            'admin/audit-logs', // Don't log viewing audit logs
        ];

        $shouldSkip = false;
        foreach ($skipRoutes as $skipRoute) {
            if (str_contains($path, $skipRoute)) {
                $shouldSkip = true;
                break;
            }
        }

        $response = $next($request);

        // Only log authenticated users and non-skipped routes
        if ($user && ! $shouldSkip) {
            $action = $this->determineAction($method, $path, $response->getStatusCode());
            $description = $this->generateDescription($method, $path, $request, $response);

            if ($action && $description) {
                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => $action,
                    'description' => $description,
                    'ip_address' => $ip,
                    'metadata' => json_encode([
                        'method' => $method,
                        'path' => $path,
                        'user_agent' => $request->userAgent(),
                        'status_code' => $response->getStatusCode(),
                        'query_params' => $request->query(),
                    ]),
                ]);
            }
        }

        return $response;
    }

    /**
     * Determine the action type based on HTTP method and response
     */
    private function determineAction(string $method, string $path, int $statusCode): ?string
    {
        // Only log successful operations
        if ($statusCode < 200 || $statusCode >= 300) {
            return null;
        }

        if (str_contains($path, 'login')) {
            return 'login';
        }

        if (str_contains($path, 'logout')) {
            return 'logout';
        }

        // API routes
        if (str_contains($path, 'api/')) {
            $path = str_replace('api/', '', $path);

            if ($method === 'POST' && ! str_contains($path, 'payment')) {
                return 'create';
            }

            if ($method === 'PUT' || $method === 'PATCH') {
                return 'update';
            }

            if ($method === 'DELETE') {
                return 'delete';
            }

            if ($method === 'GET' && str_contains($path, 'export')) {
                return 'export';
            }
        }

        // Web routes
        if ($method === 'POST' && ! str_contains($path, 'login')) {
            return 'create';
        }

        if (str_contains($path, 'edit') && $method === 'GET') {
            return 'view_edit';
        }

        return null;
    }

    /**
     * Generate a human-readable description of the action
     */
    private function generateDescription(string $method, string $path, Request $request, Response $response): ?string
    {
        $path = str_replace('api/', '', $path);

        // Login/Logout
        if (str_contains($path, 'login')) {
            return 'User logged into the system';
        }

        if (str_contains($path, 'logout')) {
            return 'User logged out of the system';
        }

        // API Routes
        if (str_contains($path, 'cases')) {
            if ($method === 'POST') {
                return 'Created a new case';
            }
            if ($method === 'PUT' || $method === 'PATCH') {
                return 'Updated case information';
            }
            if ($method === 'DELETE') {
                return 'Deleted a case';
            }
            if ($method === 'GET' && is_numeric(basename($path))) {
                return 'Viewed case details';
            }
        }

        if (str_contains($path, 'clients')) {
            if ($method === 'POST') {
                return 'Created a new client';
            }
            if ($method === 'PUT' || $method === 'PATCH') {
                return 'Updated client information';
            }
            if ($method === 'DELETE') {
                return 'Deleted a client';
            }
            if ($method === 'GET' && is_numeric(basename($path))) {
                return 'Viewed client details';
            }
        }

        if (str_contains($path, 'invoices')) {
            if ($method === 'POST') {
                return 'Created a new invoice';
            }
            if (str_contains($path, 'payment')) {
                return 'Processed invoice payment';
            }
            if ($method === 'PUT' || $method === 'PATCH') {
                return 'Updated invoice';
            }
            if ($method === 'DELETE') {
                return 'Deleted an invoice';
            }
        }

        if (str_contains($path, 'trust-accounts')) {
            if ($method === 'POST') {
                return 'Created a trust account transaction';
            }
            if ($method === 'PUT' || $method === 'PATCH') {
                return 'Updated trust account transaction';
            }
            if ($method === 'DELETE') {
                return 'Deleted trust account transaction';
            }
        }

        if (str_contains($path, 'documents')) {
            if ($method === 'POST') {
                return 'Uploaded or generated a document';
            }
            if ($method === 'PUT' || $method === 'PATCH') {
                return 'Updated document';
            }
            if ($method === 'DELETE') {
                return 'Deleted a document';
            }
        }

        if (str_contains($path, 'tasks')) {
            if ($method === 'POST') {
                return 'Created a new task';
            }
            if ($method === 'PUT' || $method === 'PATCH') {
                return 'Updated task information';
            }
            if ($method === 'DELETE') {
                return 'Deleted a task';
            }
        }

        if (str_contains($path, 'time-entries')) {
            if ($method === 'POST') {
                return 'Logged time entry';
            }
            if ($method === 'PUT' || $method === 'PATCH') {
                return 'Updated time entry';
            }
            if ($method === 'DELETE') {
                return 'Deleted time entry';
            }
        }

        if (str_contains($path, 'profile') || str_contains($path, 'settings')) {
            return 'Updated user profile or settings';
        }

        // Export actions
        if (str_contains($path, 'export')) {
            return 'Exported data to CSV';
        }

        return null;
    }
}
