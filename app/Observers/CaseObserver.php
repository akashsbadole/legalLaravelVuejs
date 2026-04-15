<?php

namespace App\Observers;

use App\Models\LegalCase;
use App\Models\Survey;
use App\Services\EmailService;

class CaseObserver
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function updated(LegalCase $case)
    {
        // Send feedback survey when case is marked as completed/closed
        if ($case->wasChanged('status_id') && in_array($case->status_id, [4, 5])) { // Assuming 4=closed, 5=completed
            $this->sendFeedbackSurvey($case);
        }
    }

    private function sendFeedbackSurvey(LegalCase $case)
    {
        $client = $case->client;
        if ($client && $client->email) {
            // Get or create feedback survey
            $survey = Survey::where('title', 'Case Feedback Survey')->first();
            if (! $survey) {
                $survey = Survey::create([
                    'title' => 'Case Feedback Survey',
                    'description' => 'Please share your feedback about our legal services.',
                    'questions' => [
                        [
                            'type' => 'rating',
                            'question' => 'How satisfied are you with our legal services?',
                            'options' => ['1', '2', '3', '4', '5'],
                        ],
                        [
                            'type' => 'text',
                            'question' => 'What did you like about our services?',
                        ],
                        [
                            'type' => 'text',
                            'question' => 'What could we improve?',
                        ],
                        [
                            'type' => 'yes_no',
                            'question' => 'Would you recommend us to others?',
                        ],
                    ],
                    'is_active' => true,
                ]);
            }

            // Send survey email
            $this->emailService->sendTemplate('case_feedback', [
                'email' => $client->email,
                'name' => $client->name,
            ], [
                'client_name' => $client->name,
                'case_number' => $case->case_number,
                'survey_url' => route('portal.survey', $survey->id),
            ]);
        }
    }
}
