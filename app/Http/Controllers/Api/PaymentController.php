<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Request $request, Invoice $invoice): JsonResponse
    {
        if ($invoice->status === 'paid') {
            return response()->json(['message' => 'Invoice is already paid'], 422);
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => "Invoice #{$invoice->invoice_number}",
                    ],
                    'unit_amount' => (int) ($invoice->total_amount * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => config('app.url') . '/#/portal/invoices?success=true',
            'cancel_url' => config('app.url') . '/#/portal/invoices?cancelled=true',
            'metadata' => [
                'invoice_id' => $invoice->id,
                'tenant_id' => $invoice->tenant_id,
            ],
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $invoiceId = $session->metadata->invoice_id;

            $invoice = Invoice::find($invoiceId);
            if ($invoice) {
                $invoice->update([
                    'status' => 'paid',
                    'paid_date' => now(),
                ]);

                // Add to Audit Log
                \App\Models\AuditLog::create([
                    'user_id' => $invoice->client_id,
                    'action' => 'payment',
                    'description' => "Online payment received for Invoice #{$invoice->invoice_number}",
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
