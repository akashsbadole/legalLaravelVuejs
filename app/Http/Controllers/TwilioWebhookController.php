<?php

namespace App\Http\Controllers;

use App\Models\InteractionLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TwilioWebhookController extends Controller
{
    public function handleWhatsApp(Request $request)
    {
        Log::info('WhatsApp webhook received', $request->all());

        // Validate Twilio request (in production, verify signature)
        $this->validateTwilioRequest($request);

        $messageSid = $request->input('MessageSid');
        $messageStatus = $request->input('MessageStatus');
        $to = $request->input('To');
        $from = $request->input('From');

        // Update interaction log status
        $interaction = InteractionLog::where('type', 'whatsapp')
            ->whereJsonContains('recipients', $to)
            ->where('status', 'sent')
            ->latest()
            ->first();

        if ($interaction) {
            $interaction->update([
                'status' => $this->mapTwilioStatus($messageStatus),
                'notes' => $request->input('ErrorMessage') ?: $interaction->notes,
            ]);
        }

        // Handle incoming messages
        if ($request->input('Body') && str_starts_with($from, 'whatsapp:')) {
            $this->handleIncomingWhatsAppMessage($request);
        }

        return response('OK', 200);
    }

    public function handleSms(Request $request)
    {
        Log::info('SMS webhook received', $request->all());

        $this->validateTwilioRequest($request);

        $messageSid = $request->input('MessageSid');
        $messageStatus = $request->input('MessageStatus');
        $to = $request->input('To');

        // Update interaction log status
        $interaction = InteractionLog::where('type', 'sms')
            ->whereJsonContains('recipients', $to)
            ->where('status', 'sent')
            ->latest()
            ->first();

        if ($interaction) {
            $interaction->update([
                'status' => $this->mapTwilioStatus($messageStatus),
                'notes' => $request->input('ErrorMessage') ?: $interaction->notes,
            ]);
        }

        return response('OK', 200);
    }

    private function validateTwilioRequest(Request $request): void
    {
        if (! app()->environment('production')) {
            return; // Skip validation in development
        }

        $signature = $request->header('X-Twilio-Signature');
        if (! $signature) {
            abort(403, 'Missing Twilio signature');
        }

        // Get the full URL and POST data
        $url = $request->fullUrl();
        $params = $request->post();

        // Sort parameters alphabetically
        ksort($params);

        // Create parameter string
        $paramString = '';
        foreach ($params as $key => $value) {
            $paramString .= $key.$value;
        }

        // Create the string to hash
        $validationString = $url.$paramString;

        // Compute HMAC-SHA1
        $expectedSignature = base64_encode(hash_hmac('sha1', $validationString, config('services.twilio.auth_token'), true));

        if (! hash_equals($expectedSignature, $signature)) {
            abort(403, 'Invalid Twilio signature');
        }
    }

    private function mapTwilioStatus(string $twilioStatus): string
    {
        return match ($twilioStatus) {
            'delivered' => 'delivered',
            'failed', 'undelivered' => 'failed',
            'read' => 'read',
            default => 'sent',
        };
    }

    private function handleIncomingWhatsAppMessage(Request $request): void
    {
        $from = str_replace('whatsapp:', '', $request->input('From'));
        $body = $request->input('Body');

        // Find user by phone number
        $user = User::where('phone', 'like', '%'.substr($from, -10))->first();

        if ($user) {
            InteractionLog::create([
                'user_id' => $user->id,
                'created_by' => $user->id, // Self-created for incoming
                'type' => 'whatsapp',
                'channel' => 'whatsapp',
                'body' => $body,
                'recipients' => [$request->input('From')],
                'sent_at' => now(),
                'direction' => 'inbound',
                'status' => 'delivered',
                'phone_number' => $from,
            ]);
        }
    }
}
