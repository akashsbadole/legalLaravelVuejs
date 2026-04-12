<?php

namespace App\Services;

use App\Models\Webhook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    public static function dispatch(string $event, array $data): void
    {
        $webhooks = Webhook::where('is_active', true)
            ->whereJsonContains('events', $event)
            ->get();

        foreach ($webhooks as $webhook) {
            self::sendWebhook($webhook, $event, $data);
        }
    }

    private static function sendWebhook(Webhook $webhook, string $event, array $data): void
    {
        try {
            $payload = [
                'event' => $event,
                'timestamp' => now()->toIso8601String(),
                'data' => $data,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Webhook-Event' => $event,
                'X-Webhook-Signature' => $webhook->secret 
                    ? hash_hmac('sha256', json_encode($payload), $webhook->secret) 
                    : null,
            ])->timeout(30)->post($webhook->url, $payload);

            $webhook->update([
                'last_triggered_at' => now(),
                'failure_count' => $response->failed() ? $webhook->failure_count + 1 : 0,
            ]);

            if ($response->failed()) {
                Log::error("Webhook failed for {$webhook->url}: " . $response->body());
            }
        } catch (\Exception $e) {
            $webhook->increment('failure_count');
            Log::error("Webhook exception: " . $e->getMessage());
        }
    }

    public static function testWebhook(Webhook $webhook): bool
    {
        try {
            $response = Http::timeout(10)->post($webhook->url, [
                'event' => 'webhook.test',
                'timestamp' => now()->toIso8601String(),
                'data' => ['message' => 'Test webhook from LegalFirm'],
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
