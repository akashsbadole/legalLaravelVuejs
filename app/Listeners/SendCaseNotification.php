<?php

namespace App\Listeners;

use App\Events\CaseCreated;
use App\Jobs\SendAutomatedMessage;
use Illuminate\Support\Facades\Log;

class SendCaseNotification
{
    /**
     * Handle the event.
     */
    public function handle(CaseCreated $event): void
    {
        $case = $event->case;

        // Send notification to assigned attorney
        if ($case->assigned_to) {
            SendAutomatedMessage::dispatch($case->assigned_to, 'case_assigned', [
                'case_title' => $case->title,
                'case_number' => $case->case_number,
                'client_name' => $case->client?->name,
            ]);

            Log::info('Case assignment notification queued', [
                'case_id' => $case->id,
                'assigned_to' => $case->assigned_to,
            ]);
        }

        // Send notification to client
        if ($case->client && $case->client->user) {
            SendAutomatedMessage::dispatch($case->client->user->id, 'case_created', [
                'case_title' => $case->title,
                'case_number' => $case->case_number,
                'attorney_name' => $case->assignedUser?->name,
            ]);

            Log::info('Case creation notification queued for client', [
                'case_id' => $case->id,
                'client_id' => $case->client->id,
            ]);
        }
    }
}