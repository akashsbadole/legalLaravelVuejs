<?php

namespace App\Providers;

use App\Services\AI\AIServiceManager;
use App\Services\AI\ClaudeAIService;
use App\Services\AI\GeminiAIService;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register individual AI services
        $this->app->singleton(GeminiAIService::class, function ($app) {
            return new GeminiAIService;
        });

        $this->app->singleton(ClaudeAIService::class, function ($app) {
            return new ClaudeAIService;
        });

        // Register AI service manager
        $this->app->singleton(AIServiceManager::class, function ($app) {
            return new AIServiceManager;
        });

        // Register alias for easy access
        $this->app->alias(AIServiceManager::class, 'ai');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/ai.php' => config_path('ai.php'),
            ], 'ai-config');
        }
    }
}
