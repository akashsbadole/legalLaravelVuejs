<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\ApiRequestor;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private ?string $secretKey;
    private ?string $publishableKey;
    private bool $isTestMode;

    public function __construct()
    {
        $this->isTestMode = config('app.env') !== 'production';
        $this->secretKey = config('services.stripe.secret_key');
        $this->publishableKey = config('services.stripe.key');

        if ($this->secretKey) {
            Stripe::setApiKey($this->secretKey);
        }
    }

    public function isEnabled(): bool
    {
        return !empty($this->secretKey);
    }

    public function getPublishableKey(): ?string
    {
        return $this->publishableKey;
    }

    public function createPaymentIntent(Invoice $invoice): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => (int)($invoice->total_amount * 100),
                'currency' => 'usd',
                'metadata' => [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                ],
                'description' => "Invoice {$invoice->invoice_number}",
            ]);

            return [
                'client_secret' => $paymentIntent->client_secret,
                'id' => $paymentIntent->id,
            ];
        } catch (\Exception $e) {
            Log::error('Stripe payment intent error: ' . $e->getMessage());
            return null;
        }
    }

    public function confirmPayment(string $paymentIntentId): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        try {
            $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
            return $paymentIntent->status === 'succeeded';
        } catch (\Exception $e) {
            Log::error('Stripe confirmation error: ' . $e->getMessage());
            return false;
        }
    }

    public function processMockPayment(Invoice $invoice): bool
    {
        try {
            $invoice->update([
                'status' => 'paid',
                'paid_date' => now(),
            ]);

            activity()
                ->causedBy($invoice->client)
                ->performedOn($invoice)
                ->log('Invoice paid via mock payment');

            return true;
        } catch (\Exception $e) {
            Log::error('Mock payment error: ' . $e->getMessage());
            return false;
        }
    }
}