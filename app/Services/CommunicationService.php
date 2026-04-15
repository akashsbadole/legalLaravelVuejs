<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class CommunicationService
{
    private EmailService $emailService;
    private SmsService $smsService;

    public function __construct(EmailService $emailService, SmsService $smsService)
    {
        $this->emailService = $emailService;
        $this->smsService = $smsService;
    }

    public function sendToUser(int $userId, string $message, array $options = []): array
    {
        // Check if user has opted out of communications
        $user = User::find($userId);
        if ($user && $user->communication_opt_out) {
            return ['success' => false, 'error' => 'User has opted out of communications'];
        }

        return $this->sendToUserInternal($userId, $message, $options);
    }

    private function sendToUserInternal(int $userId, string $message, array $options = []): array
    {
        $user = User::find($userId);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found'];
        }

        $channels = $this->determineChannels($user, $options);

        $results = [];
        foreach ($channels as $channel) {
            $results[$channel] = $this->sendViaChannel($channel, $user, $message, $options);
        }

        return [
            'success' => !empty(array_filter($results, fn($r) => $r['success'])),
            'channels_attempted' => array_keys($results),
            'results' => $results,
        ];
    }

    public function sendNotification(int $userId, string $type, array $data = []): array
    {
        $templates = $this->getNotificationTemplates();

        if (!isset($templates[$type])) {
            return ['success' => false, 'error' => 'Notification type not found'];
        }

        $template = $templates[$type];
        $message = $this->renderTemplate($template['message'], $data);
        $subject = $template['subject'] ?? $type;

        return $this->sendToUser($userId, $message, [
            'subject' => $subject,
            'type' => $type,
            'data' => $data,
        ]);
    }

    public function sendBulkNotification(array $userIds, string $type, array $data = []): array
    {
        $results = [];
        foreach ($userIds as $userId) {
            $results[$userId] = $this->sendNotification($userId, $type, $data);
        }

        return [
            'total_users' => count($userIds),
            'successful' => count(array_filter($results, fn($r) => $r['success'])),
            'results' => $results,
        ];
    }

    private function determineChannels(User $user, array $options): array
    {
        // Check user preferences or fallback to defaults
        $preferredChannels = $user->communication_preferences ?? ['email'];

        // Override with explicit channel request
        if (isset($options['channels'])) {
            $preferredChannels = $options['channels'];
        }

        // Fallback logic: email primary, SMS/WhatsApp secondary
        if (empty($preferredChannels)) {
            $preferredChannels = ['email'];
        }

        // Add fallback channels if primary fails
        if (in_array('email', $preferredChannels) && !isset($options['no_fallback'])) {
            if ($user->phone && !in_array('sms', $preferredChannels)) {
                $preferredChannels[] = 'sms';
            }
        }

        return array_unique($preferredChannels);
    }

    private function sendViaChannel(string $channel, User $user, string $message, array $options): array
    {
        try {
            switch ($channel) {
                case 'email':
                    return $this->sendViaEmail($user, $message, $options);

                case 'sms':
                    return $this->sendViaSms($user, $message, $options);

                case 'whatsapp':
                    return $this->sendViaWhatsApp($user, $message, $options);

                default:
                    return ['success' => false, 'error' => 'Unsupported channel: ' . $channel];
            }
        } catch (\Exception $e) {
            Log::error("Communication failed via {$channel}: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage(), 'channel' => $channel];
        }
    }

    private function sendViaEmail(User $user, string $message, array $options): array
    {
        $subject = $options['subject'] ?? 'Notification';

        $result = $this->emailService->send(
            ['email' => $user->email, 'name' => $user->name],
            $subject,
            $message
        );

        return [
            'success' => $result,
            'channel' => 'email',
            'recipient' => $user->email,
        ];
    }

    private function sendViaSms(User $user, string $message, array $options): array
    {
        if (!$user->phone) {
            return ['success' => false, 'error' => 'No phone number available', 'channel' => 'sms'];
        }

        $result = $this->smsService->send($user->phone, $message, 'sms');

        return [
            'success' => $result['success'],
            'channel' => 'sms',
            'recipient' => $user->phone,
            'message_id' => $result['id'] ?? null,
        ];
    }

    private function sendViaWhatsApp(User $user, string $message, array $options): array
    {
        if (!$user->phone) {
            return ['success' => false, 'error' => 'No phone number available', 'channel' => 'whatsapp'];
        }

        $result = $this->smsService->sendWhatsApp($user->phone, $message);

        return [
            'success' => $result['success'],
            'channel' => 'whatsapp',
            'recipient' => $user->phone,
            'message_id' => $result['id'] ?? null,
        ];
    }

    private function getNotificationTemplates(): array
    {
        return [
            'case_assigned' => [
                'subject' => 'New Case Assigned',
                'message' => 'You have been assigned a new case: {case_title}. Please check your dashboard for details.',
            ],
            'task_due' => [
                'subject' => 'Task Due Soon',
                'message' => 'Your task "{task_title}" is due on {due_date}.',
            ],
            'payment_reminder' => [
                'subject' => 'Payment Reminder',
                'message' => 'Payment of {amount} is due for invoice {invoice_number} on {due_date}.',
            ],
            'document_request' => [
                'subject' => 'Document Request',
                'message' => 'Please submit the following documents: {documents}.',
            ],
            'court_date' => [
                'subject' => 'Court Date Reminder',
                'message' => 'Your court date for case {case_number} is on {court_date} at {court_time}.',
            ],
        ];
    }

    private function renderTemplate(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }

        return $template;
    }
}