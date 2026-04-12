<?php

namespace App\Services;

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
        return !empty($this->apiKey);
    }

    public function send(string $to, string $message): array
    {
        if (!$this->isEnabled()) {
            return ['success' => false, 'error' => 'SMS service not configured'];
        }

        $to = $this->formatPhoneNumber($to);

        try {
            switch ($this->provider) {
                case 'msg91':
                    return $this->sendViaMsg91($to, $message);
                case 'twilio':
                    return $this->sendViaTwilio($to, $message);
                case 'textlocal':
                    return $this->sendViaTextLocal($to, $message);
                default:
                    return $this->sendViaMsg91($to, $message);
            }
        } catch (\Exception $e) {
            Log::error('SMS send error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function sendViaMsg91(string $to, string $message): array
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

    private function sendViaTwilio(string $to, string $message): array
    {
        $response = Http::withBasicAuth(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        )->post('https://api.twilio.com/2010-04-01/Accounts/' . config('services.twilio.account_sid') . '/Messages.json', [
            'To' => $to,
            'From' => config('services.twilio.phone_number'),
            'Body' => $message,
        ]);

        if ($response->successful()) {
            return ['success' => true, 'id' => $response->json('sid')];
        }

        return ['success' => false, 'error' => $response->body()];
    }

    private function sendViaTextLocal(string $to, string $message): array
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
            return '91' . $phone;
        }
        
        if (strlen($phone) == 11 && $phone[0] == '0') {
            return '91' . substr($phone, 1);
        }
        
        if (!str_starts_with($phone, '91')) {
            return '91' . $phone;
        }
        
        return $phone;
    }

    public function sendOtp(string $to): array
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $message = "Your verification code is {$otp}. Valid for 10 minutes.";
        
        $result = $this->send($to, $message);
        
        if ($result['success']) {
            cache()->put('sms_otp_' . $to, [
                'otp' => $otp,
                'expires' => now()->addMinutes(10),
            ], 600);
        }
        
        return $result;
    }

    public function verifyOtp(string $phone, string $otp): bool
    {
        $cached = cache()->get('sms_otp_' . $phone);
        
        if (!$cached) {
            return false;
        }
        
        if ($cached['otp'] !== $otp) {
            return false;
        }
        
        if (now()->gt($cached['expires'])) {
            return false;
        }
        
        cache()->forget('sms_otp_' . $phone);
        return true;
    }
}
