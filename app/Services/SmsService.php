<?php

namespace App\Services;

use App\Models\InteractionLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private ?string $apiKey;

    private ?string $senderId;

    private string $provider;

    public function __construct()
    {
        $this->apiKey = config('services.sms.api_key');
        $this->senderId = config('services.sms.sender_id', 'LEGAL');
        $this->provider = config('services.sms.provider', 'msg91');
    }

    public function isEnabled(): bool
    {
        return ! empty($this->apiKey);
    }

    public function send(string $to, string $message, string $channel = 'sms'): array
    {
        if (! $this->isEnabled()) {
            return ['success' => false, 'error' => 'SMS service not configured'];
        }

        $to = $this->formatPhoneNumber($to);

        try {
            $result = match ($this->provider) {
                'msg91' => $this->sendViaMsg91($to, $message, $channel),
                'twilio' => $channel === 'whatsapp'
                    ? $this->sendViaTwilioWhatsApp($to, $message)
                    : $this->sendViaTwilio($to, $message),
                'textlocal' => $this->sendViaTextLocal($to, $message, $channel),
                default => $this->sendViaMsg91($to, $message, $channel),
            };

            // Log the interaction
            $this->logInteraction($to, $message, $channel, $result);

            return $result;
        } catch (\Exception $e) {
            Log::error('SMS/WhatsApp send error: '.$e->getMessage());

            // Log failed interaction
            $this->logInteraction($to, $message, $channel, ['success' => false, 'error' => $e->getMessage()]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendWhatsApp(string $to, string $message): array
    {
        return $this->send($to, $message, 'whatsapp');
    }

    public function sendBulkSms(array $recipients, string $message): array
    {
        $results = [];
        foreach ($recipients as $to) {
            $results[] = [
                'recipient' => $to,
                'result' => $this->send($to, $message, 'sms'),
            ];
        }

        return [
            'total' => count($recipients),
            'successful' => count(array_filter($results, fn($r) => $r['result']['success'])),
            'results' => $results,
        ];
    }

    public function sendBulkWhatsApp(array $recipients, string $message): array
    {
        $results = [];
        foreach ($recipients as $to) {
            $results[] = [
                'recipient' => $to,
                'result' => $this->send($to, $message, 'whatsapp'),
            ];
        }

        return [
            'total' => count($recipients),
            'successful' => count(array_filter($results, fn($r) => $r['result']['success'])),
            'results' => $results,
        ];
    }

    public function sendScheduledSms(string $to, string $message, \DateTime $scheduledTime): array
    {
        // For now, just send immediately. In production, queue for later sending
        return $this->send($to, $message, 'sms');
    }

    private function sendViaMsg91(string $to, string $message, string $channel = 'sms'): array
    {
        $response = Http::withHeaders([
            'authkey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.msg91.com/api/v5/sendsms', [
            'sender' => $this->senderId,
            'mobiles' => $to,
            'message' => $message,
            'route' => '4',
        ]);

        if ($response->successful()) {
            return ['success' => true, 'id' => $response->json('message_id')];
        }

        return ['success' => false, 'error' => $response->body()];
    }

    private function sendViaTwilioWhatsApp(string $to, string $message): array
    {
        // WhatsApp numbers should be in international format with whatsapp: prefix
        $to = 'whatsapp:' . $this->formatPhoneNumber($to);

        $response = Http::withBasicAuth(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        )->post('https://api.twilio.com/2010-04-01/Accounts/'.config('services.twilio.account_sid').'/Messages.json', [
            'To' => $to,
            'From' => 'whatsapp:' . config('services.twilio.whatsapp_number'),
            'Body' => $message,
        ]);

        if ($response->successful()) {
            return ['success' => true, 'id' => $response->json('sid'), 'channel' => 'whatsapp'];
        }

        return ['success' => false, 'error' => $response->body(), 'channel' => 'whatsapp'];
    }

    private function sendViaTwilio(string $to, string $message): array
    {
        $response = Http::withBasicAuth(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        )->post('https://api.twilio.com/2010-04-01/Accounts/'.config('services.twilio.account_sid').'/Messages.json', [
            'To' => $to,
            'From' => config('services.twilio.phone_number'),
            'Body' => $message,
        ]);

        if ($response->successful()) {
            return ['success' => true, 'id' => $response->json('sid')];
        }

        return ['success' => false, 'error' => $response->body()];
    }

    private function sendViaTextLocal(string $to, string $message, string $channel = 'sms'): array
    {
        $response = Http::post('https://api.textlocal.in/send/', [
            'apiKey' => $this->apiKey,
            'numbers' => $to,
            'sender' => $this->senderId,
            'message' => $message,
        ]);

        if ($response->successful()) {
            return ['success' => true, 'id' => $response->json('messages.0.id')];
        }

        return ['success' => false, 'error' => $response->body()];
    }

    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) == 10) {
            return '91'.$phone;
        }

        if (strlen($phone) == 11 && $phone[0] == '0') {
            return '91'.substr($phone, 1);
        }

        if (! str_starts_with($phone, '91')) {
            return '91'.$phone;
        }

        return $phone;
    }

    public function sendOtp(string $to): array
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $message = "Your verification code is {$otp}. Valid for 10 minutes.";

        $result = $this->send($to, $message);

        if ($result['success']) {
            cache()->put('sms_otp_'.$to, [
                'otp' => $otp,
                'expires' => now()->addMinutes(10),
            ], 600);
        }

        return $result;
    }

    public function verifyOtp(string $phone, string $otp): bool
    {
        $cached = cache()->get('sms_otp_'.$phone);

        if (! $cached) {
            return false;
        }

        if ($cached['otp'] !== $otp) {
            return false;
        }

        if (now()->gt($cached['expires'])) {
            return false;
        }

        cache()->forget('sms_otp_'.$phone);

        return true;
    }

    private function logInteraction(string $to, string $message, string $channel, array $result): void
    {
        try {
            // Find user by phone number for logging
            $user = \App\Models\User::where('phone', 'like', '%'.substr($this->formatPhoneNumber($to), -10))->first();

            InteractionLog::create([
                'user_id' => $user ? $user->id : null,
                'created_by' => auth()->id() ?? 1, // Default to first user if no auth
                'type' => $channel === 'whatsapp' ? 'whatsapp' : 'sms',
                'channel' => $channel,
                'body' => $message,
                'recipients' => [$to],
                'sent_at' => now(),
                'direction' => 'outbound',
                'status' => $result['success'] ? 'sent' : 'failed',
                'notes' => $result['error'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log SMS/WhatsApp interaction: '.$e->getMessage());
        }
    }
}
