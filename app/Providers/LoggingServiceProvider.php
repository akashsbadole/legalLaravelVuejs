<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Throwable;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerLoggingMacros();
        $this->registerEventListeners();
    }

    /**
     * Register custom logging methods
     */
    protected function registerLoggingMacros(): void
    {
        // Create helper functions for security logging
        $this->app->singleton('securityLogger', function ($app) {
            return new class
            {
                public function log($message, array $context = [])
                {
                    Log::channel('security')->info($message, array_merge($context, [
                        'timestamp' => now()->toISOString(),
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'user_id' => auth()->id(),
                        'url' => request()->fullUrl(),
                        'method' => request()->method(),
                    ]));
                }

                public function audit($action, $entityType, $entityId, $message, array $context = [])
                {
                    $this->log("AUDIT: {$action} on {$entityType}:{$entityId} - {$message}", array_merge($context, [
                        'action' => $action,
                        'entity_type' => $entityType,
                        'entity_id' => $entityId,
                    ]));
                }

                public function errorWithContext(Throwable $exception, array $context = [])
                {
                    Log::error($exception->getMessage(), array_merge($context, [
                        'exception' => get_class($exception),
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'trace' => $exception->getTraceAsString(),
                        'timestamp' => now()->toISOString(),
                        'ip' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'user_id' => auth()->id(),
                        'url' => request()->fullUrl(),
                        'method' => request()->method(),
                    ]));
                }
            };
        });
    }

    /**
     * Register event listeners for security logging
     */
    protected function registerEventListeners(): void
    {
        Event::listen(Login::class, function (Login $event) {
            app('securityLogger')->log('User logged in', [
                'event' => 'login',
                'user_id' => $event->user->id,
                'user_email' => $event->user->email,
            ]);
        });

        Event::listen(Logout::class, function (Logout $event) {
            app('securityLogger')->log('User logged out', [
                'event' => 'logout',
                'user_id' => $event->user->id ?? null,
            ]);
        });

        Event::listen(Failed::class, function (Failed $event) {
            app('securityLogger')->log('Failed login attempt', [
                'event' => 'failed_login',
                'email' => $event->credentials['email'] ?? 'unknown',
            ]);
        });
    }
}
