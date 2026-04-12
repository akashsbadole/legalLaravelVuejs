<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    private ?string $keyId;
    private ?string $keySecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->keyId = config('services.razorpay.key_id');
        $this->keySecret = config('services.razorpay.key_secret');
        $this->baseUrl = config('app.env') === 'production' 
            ? 'https://api.razorpay.com/v1' 
            : 'https://api.razorpay.com/v1';
    }

    public function isEnabled(): bool
    {
        return !empty($this->keyId) && !empty($this->keySecret);
    }

    public function getKeyId(): ?string
    {
        return $this->keyId;
    }

    public function createPaymentLink(Invoice $invoice): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->post("{$this->baseUrl}/payment_links", [
                    'amount' => (int)($invoice->total_amount * 100),
                    'currency' => 'INR',
                    'accept_partial' => false,
                    'description' => "Invoice {$invoice->invoice_number}",
                    'customer' => [
                        'name' => $invoice->client->name ?? 'Client',
                        'email' => $invoice->client->email ?? '',
                        'contact' => $invoice->client->phone ?? '',
                    ],
                    'notify' => [
                        'sms' => true,
                        'email' => true,
                    ],
                    'reminder_enable' => true,
                    'notes' => [
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                    ],
                ]);

            if ($response->successful()) {
                return [
                    'payment_link_id' => $response->json('id'),
                    'payment_link_url' => $response->json('short_url'),
                    'status' => $response->json('status'),
                ];
            }

            Log::error('Razorpay payment link error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Razorpay exception: ' . $e->getMessage());
            return null;
        }
    }

    public function getPaymentLinkStatus(string $paymentLinkId): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->get("{$this->baseUrl}/payment_links/{$paymentLinkId}");

            if ($response->successful()) {
                return [
                    'status' => $response->json('status'),
                    'amount_paid' => $response->json('amount_paid') / 100,
                    'amount_due' => $response->json('amount_due') / 100,
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Razorpay status check error: ' . $e->getMessage());
            return null;
        }
    }

    public function verifyWebhook(string $payload, string $signature): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $expectedSignature = hash_hmac('sha256', $payload, $this->keySecret);
            return hash_equals($expectedSignature, $signature);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createOrder(array $orderData): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->post("{$this->baseUrl}/orders", [
                    'amount' => (int)($orderData['amount'] * 100),
                    'currency' => $orderData['currency'] ?? 'INR',
                    'receipt' => $orderData['receipt'] ?? null,
                    'notes' => $orderData['notes'] ?? [],
                ]);

            if ($response->successful()) {
                return [
                    'order_id' => $response->json('id'),
                    'amount' => $response->json('amount') / 100,
                    'status' => $response->json('status'),
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Razorpay order creation error: ' . $e->getMessage());
            return null;
        }
    }
}
