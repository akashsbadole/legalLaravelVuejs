<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AIServiceManager
{
    protected array $services = [];

    protected array $serviceConfigs = [];

    public function __construct()
    {
        $this->initializeServices();
        $this->loadServiceConfigurations();
    }

    public function __call(string $method, array $arguments)
    {
        return $this->executeWithFallback($method, $arguments);
    }

    /**
     * Execute method with fallback to other services if primary fails
     */
    protected function executeWithFallback(string $method, array $arguments)
    {
        $services = $this->getAvailableServices();

        foreach ($services as $serviceName) {
            try {
                $service = $this->getService($serviceName);

                if (method_exists($service, $method)) {
                    $result = $service->$method(...$arguments);

                    // Log successful execution
                    $this->logServiceUsage($serviceName, $method, 'success');

                    return $result;
                }
            } catch (\Exception $e) {
                // Log failure and try next service
                $this->logServiceUsage($serviceName, $method, 'failed', $e->getMessage());
                Log::warning("AI Service {$serviceName} failed for method {$method}", [
                    'error' => $e->getMessage(),
                    'service' => $serviceName,
                ]);
            }
        }

        // All services failed
        throw new \Exception("All AI services are unavailable for method: {$method}");
    }

    /**
     * Execute method on specific service
     */
    public function executeOnService(string $serviceName, string $method, array $arguments = [])
    {
        $service = $this->getService($serviceName);

        if (! method_exists($service, $method)) {
            throw new \Exception("Method {$method} not available on service {$serviceName}");
        }

        try {
            $result = $service->$method(...$arguments);
            $this->logServiceUsage($serviceName, $method, 'success');

            return $result;
        } catch (\Exception $e) {
            $this->logServiceUsage($serviceName, $method, 'failed', $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get service instance
     */
    public function getService(string $serviceName): AIServiceInterface
    {
        if (! isset($this->services[$serviceName])) {
            throw new \Exception("AI service '{$serviceName}' not found");
        }

        return $this->services[$serviceName];
    }

    /**
     * Get all available services
     */
    public function getAvailableServices(): array
    {
        $available = [];

        foreach ($this->services as $name => $service) {
            if ($service->isAvailable()) {
                $available[] = $name;
            }
        }

        // Sort by priority (defined in config)
        usort($available, function ($a, $b) {
            $priorityA = $this->serviceConfigs[$a]['priority'] ?? 0;
            $priorityB = $this->serviceConfigs[$b]['priority'] ?? 0;

            return $priorityB <=> $priorityA; // Higher priority first
        });

        return $available;
    }

    /**
     * Get service statistics
     */
    public function getServiceStats(): array
    {
        $stats = [];

        foreach ($this->services as $name => $service) {
            $stats[$name] = [
                'name' => $service->getServiceName(),
                'available' => $service->isAvailable(),
                'usage' => $service->getUsageStats(),
                'priority' => $this->serviceConfigs[$name]['priority'] ?? 0,
            ];
        }

        return $stats;
    }

    /**
     * Initialize AI services
     */
    protected function initializeServices(): void
    {
        $this->services = [
            'gemini' => new GeminiAIService,
            'claude' => new ClaudeAIService,
        ];
    }

    /**
     * Load service configurations
     */
    protected function loadServiceConfigurations(): void
    {
        $this->serviceConfigs = [
            'gemini' => [
                'priority' => 2, // Higher priority
                'timeout' => 30,
                'retry_attempts' => 2,
                'rate_limit' => 1000, // requests per hour
            ],
            'claude' => [
                'priority' => 1,
                'timeout' => 45,
                'retry_attempts' => 2,
                'rate_limit' => 500,
            ],
        ];
    }

    /**
     * Log service usage
     */
    protected function logServiceUsage(string $serviceName, string $method, string $status, ?string $error = null): void
    {
        $logData = [
            'service' => $serviceName,
            'method' => $method,
            'status' => $status,
            'timestamp' => now(),
            'user_id' => auth()->id(),
        ];

        if ($error) {
            $logData['error'] = $error;
        }

        // Cache usage stats for dashboard
        $cacheKey = "ai_usage_{$serviceName}";
        $usage = Cache::get($cacheKey, ['requests' => 0, 'errors' => 0]);

        $usage['requests']++;
        if ($status === 'failed') {
            $usage['errors']++;
        }

        Cache::put($cacheKey, $usage, now()->addHours(24));

        // Log to database or file
        Log::info('AI Service Usage', $logData);
    }

    /**
     * Check if service is within rate limits
     */
    protected function checkRateLimit(string $serviceName): bool
    {
        $config = $this->serviceConfigs[$serviceName] ?? [];
        $limit = $config['rate_limit'] ?? 1000;

        $cacheKey = "ai_ratelimit_{$serviceName}";
        $requests = Cache::get($cacheKey, 0);

        return $requests < $limit;
    }

    /**
     * Increment rate limit counter
     */
    protected function incrementRateLimit(string $serviceName): void
    {
        $cacheKey = "ai_ratelimit_{$serviceName}";
        $requests = Cache::get($cacheKey, 0);
        Cache::put($cacheKey, $requests + 1, now()->addHour());
    }

    /**
     * Get optimal service for specific task
     */
    public function getOptimalServiceForTask(string $taskType): string
    {
        $taskMappings = [
            'analysis' => 'claude', // Claude is better for detailed analysis
            'summarization' => 'gemini', // Gemini is faster for summaries
            'generation' => 'claude', // Claude is better for creative generation
            'comparison' => 'claude', // Claude handles complex comparisons better
            'translation' => 'gemini', // Gemini has better multilingual support
            'compliance' => 'claude', // Claude is more thorough for compliance
            'extraction' => 'gemini', // Gemini is good for structured extraction
        ];

        $preferredService = $taskMappings[$taskType] ?? 'gemini';

        // Check if preferred service is available
        if (in_array($preferredService, $this->getAvailableServices())) {
            return $preferredService;
        }

        // Fallback to any available service
        $available = $this->getAvailableServices();

        return $available[0] ?? 'gemini';
    }
}
