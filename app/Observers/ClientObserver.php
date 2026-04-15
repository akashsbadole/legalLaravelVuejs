<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Services\EmailService;
use App\Services\OnboardingService;

class ClientObserver
{
    protected $emailService;

    protected $onboardingService;

    public function __construct(EmailService $emailService, OnboardingService $onboardingService)
    {
        $this->emailService = $emailService;
        $this->onboardingService = $onboardingService;
    }

    public function created(Client $client)
    {
        // Log client creation
        AuditLog::logCreated('client', $client->id, $client->toArray());

        // Start onboarding process for new clients
        $user = $client->getUserAttribute();
        if ($user) {
            $this->onboardingService->startOnboarding($user);
        }
    }

    public function updated(Client $client)
    {
        // Log client data changes
        $changes = $client->getChanges();
        if (! empty($changes)) {
            AuditLog::logUpdated('client', $client->id, $client->getOriginal(), $client->toArray());
        }
    }

    public function retrieved(Client $client)
    {
        // Log client data access (only for sensitive operations)
        if (request()->isMethod('get') && str_contains(request()->path(), 'client')) {
            AuditLog::logAction('viewed_client_data', "Viewed client data for {$client->email}");
        }
    }
}
