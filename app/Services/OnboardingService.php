<?php

namespace App\Services;

use App\Models\Onboarding;
use App\Models\User;
use App\Jobs\SendFollowUp;
use App\Models\FollowUp;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;

class OnboardingService
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function startOnboarding(User $client): Onboarding
    {
        // Create onboarding record
        $onboarding = Onboarding::create([
            'client_id' => $client->id,
            'status' => 'in_progress',
        ]);

        // Send welcome email
        $this->sendWelcomeEmail($client);

        // Mark welcome email as completed
        $onboarding->completeStep('welcome_email');

        return $onboarding;
    }

    public function completeStep(User $client, string $step): void
    {
        $onboarding = Onboarding::where('client_id', $client->id)->first();
        if ($onboarding) {
            $onboarding->completeStep($step);
            Log::info("Onboarding step completed: {$step} for client {$client->id}");
        }
    }

    private function sendWelcomeEmail(User $client): void
    {
        // Send welcome email using EmailService
        $this->emailService->sendTemplate('client_welcome', [
            'email' => $client->email,
            'name' => $client->name,
        ], [
            'client_name' => $client->name,
            'portal_url' => route('portal.dashboard'),
        ]);
    }

    public function sendDocumentRequest(User $client): void
    {
        $this->emailService->sendTemplate('document_request', [
            'email' => $client->email,
            'name' => $client->name,
        ], [
            'client_name' => $client->name,
            'required_documents' => 'Aadhaar, PAN, Address Proof, GSTIN (if applicable)',
        ]);
    }

    public function sendEngagementLetter(User $client, $documentPath): void
    {
        $this->emailService->send([
            'email' => $client->email,
            'name' => $client->name,
        ], 'Engagement Letter - Please Review and Sign', 'Please find attached our engagement letter for your review and signature.', [
            ['path' => $documentPath, 'name' => 'engagement_letter.pdf']
        ]);
    }

    public function getOnboardingStatus(User $client): ?Onboarding
    {
        return Onboarding::where('client_id', $client->id)->first();
    }

    public function getOnboardingProgress(User $client): array
    {
        $onboarding = $this->getOnboardingStatus($client);

        if (!$onboarding) {
            return [
                'completed' => 0,
                'total' => 0,
                'percentage' => 0,
                'steps' => [],
            ];
        }

        $completed = count($onboarding->completed_steps ?? []);
        $total = count($onboarding->steps);

        return [
            'completed' => $completed,
            'total' => $total,
            'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0,
            'steps' => $onboarding->steps,
            'completed_steps' => $onboarding->completed_steps ?? [],
        ];
    }
}