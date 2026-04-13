<?php

namespace App\Jobs;

use App\Models\FollowUp;
use App\Models\InteractionLog;
use App\Services\EmailService;
use App\Services\SMSService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendFollowUp implements ShouldQueue
{
    use Queueable;

    protected $followUp;

    /**
     * Create a new job instance.
     */
    public function __construct(FollowUp $followUp)
    {
        $this->followUp = $followUp;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = $this->followUp->user;

            switch ($this->followUp->type) {
                case 'email':
                    $emailService = app(EmailService::class);
                    $result = $emailService->send(
                        ['email' => $user->email, 'name' => $user->name],
                        $this->followUp->subject,
                        $this->followUp->message
                    );
                    if ($result) {
                        InteractionLog::create([
                            'user_id' => $user->id,
                            'created_by' => $this->followUp->created_by,
                            'type' => 'email',
                            'subject' => $this->followUp->subject,
                            'body' => $this->followUp->message,
                            'recipients' => [$user->email],
                            'sent_at' => now(),
                            'direction' => 'outbound',
                            'status' => 'sent',
                        ]);
                    }
                    break;

                case 'sms':
                    $smsService = app(SMSService::class);
                    $result = $smsService->send($user->phone, $this->followUp->message);
                    if ($result['success']) {
                        InteractionLog::create([
                            'user_id' => $user->id,
                            'created_by' => $this->followUp->created_by,
                            'type' => 'sms',
                            'body' => $this->followUp->message,
                            'recipients' => [$user->phone],
                            'sent_at' => now(),
                            'direction' => 'outbound',
                            'status' => 'sent',
                        ]);
                    }
                    break;

                case 'call':
                    // For call, just log that a call should be made
                    Log::info('Follow-up call scheduled', [
                        'user_id' => $user->id,
                        'phone' => $user->phone,
                        'message' => $this->followUp->message
                    ]);
                    break;
            }

            $this->followUp->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

        } catch (\Exception $e) {
            $this->followUp->update(['status' => 'failed']);
            Log::error('Follow-up failed', [
                'follow_up_id' => $this->followUp->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
