<?php

namespace App\Jobs;

use App\Services\CommunicationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendAutomatedMessage implements ShouldQueue
{
    use Queueable;

    public $tries = 3;

    public $backoff = [10, 30, 60]; // Retry after 10s, 30s, 60s

    public int $userId;

    public string $messageType;

    public array $data;

    public array $options;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $messageType, array $data = [], array $options = [])
    {
        $this->userId = $userId;
        $this->messageType = $messageType;
        $this->data = $data;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(CommunicationService $communicationService): void
    {
        try {
            $result = $communicationService->sendNotification(
                $this->userId,
                $this->messageType,
                $this->data
            );

            if ($result['success']) {
                Log::info('Automated message sent successfully', [
                    'user_id' => $this->userId,
                    'type' => $this->messageType,
                    'channels' => $result['channels_attempted'],
                ]);
            } else {
                Log::warning('Automated message failed to send', [
                    'user_id' => $this->userId,
                    'type' => $this->messageType,
                    'results' => $result['results'],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Automated message job failed', [
                'user_id' => $this->userId,
                'type' => $this->messageType,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendAutomatedMessage job failed permanently', [
            'user_id' => $this->userId,
            'type' => $this->messageType,
            'error' => $exception->getMessage(),
        ]);
    }
}
