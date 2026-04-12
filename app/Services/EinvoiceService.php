<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EinvoiceService
{
    private ?string $gstin;
    private ?string $username;
    private ?string $password;
    private string $env;

    public function __construct()
    {
        $this->gstin = config('services.gstn.gstin');
        $this->username = config('services.gstn.username');
        $this->password = config('services.gstn.password');
        $this->env = config('app.env') === 'production' ? 'prod' : 'staging';
    }

    public function isEnabled(): bool
    {
        return !empty($this->gstin) && !empty($this->username);
    }

    public function generateEinvoice(Invoice $invoice): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        try {
            $payload = $this->buildEinvoicePayload($invoice);
            
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'user_name' => $this->username,
                'user_gstin' => $this->gstin,
            ])->withBasicAuth($this->username, $this->password)
            ->post($this->getApiUrl() . '/einvoice', $payload);

            if ($response->successful()) {
                $irn = $response->json('result.irn');
                $signedInvoice = $response->json('result.signed_invoice');
                $qrCode = $response->json('result.qr_code');

                $invoice->update([
                    'irn' => $irn,
                    'signed_invoice' => $signedInvoice,
                    'qr_code' => $qrCode,
                    'einvoice_status' => 'generated',
                ]);

                return [
                    'success' => true,
                    'irn' => $irn,
                    'qr_code' => $qrCode,
                ];
            }

            Log::error('E-invoice generation failed: ' . $response->body());
            return ['success' => false, 'error' => $response->json()];
        } catch (\Exception $e) {
            Log::error('E-invoice exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function buildEinvoicePayload(Invoice $invoice): array
    {
        $tenant = Tenant::first();
        
        $lineItems = $invoice->lineItems->map(function ($item) {
            return [
                'sl_no' => '1',
                'product_name' => substr($item->description, 0, 100),
                'hsn_code' => '9982',
                'quantity' => (int)$item->quantity,
                'unit' => 'NOS',
                'unit_price' => round($item->unit_price, 2),
                'total_amount' => round($item->amount, 2),
                'taxable_amount' => round($item->amount / 1.18, 2),
                'gst_rate' => 18,
                'cgst' => round(($item->amount - $item->amount / 1.18) / 2, 2),
                'sgst' => round(($item->amount - $item->amount / 1.18) / 2, 2),
                'igst' => 0,
            ];
        });

        return [
            'version' => '1.1',
            'transaction' => [
                'type' => 'Invoice',
                'supply_type' => 'B2B',
                'document_type' => 'Tax Invoice',
            ],
            'seller' => [
                'gstin' => $this->gstin,
                'legal_name' => $tenant?->name ?? config('app.name'),
                'trade_name' => $tenant?->name ?? config('app.name'),
                'address1' => $tenant?->address ?? '',
                'location' => 'Bengaluru',
                'state_code' => '29',
                'pincode' => '560001',
            ],
            'buyer' => [
                'gstin' => $invoice->client->gstin ?? 'URP',
                'legal_name' => $invoice->client->name,
                'address1' => $invoice->client->address ?? '',
                'location' => $invoice->client->city ?? 'Unknown',
                'state_code' => $invoice->client->state_code ?? '29',
                'pincode' => $invoice->client->pincode ?? '560001',
            ],
            'items' => $lineItems,
            'total' => [
                'assessed_value' => round($invoice->subtotal, 2),
                'cgst' => round($invoice->tax_amount / 2, 2),
                'sgst' => round($invoice->tax_amount / 2, 2),
                'igst' => 0,
                'total_tax' => round($invoice->tax_amount, 2),
                'grand_total' => round($invoice->total_amount, 2),
            ],
            'invoice_id' => $invoice->invoice_number,
            'invoice_date' => $invoice->issue_date?->format('d/m/Y'),
        ];
    }

    private function getApiUrl(): string
    {
        return $this->env === 'prod' 
            ? 'https://einvoice-api.com/api/v1'
            : 'https://einvoice-api-staging.com/api/v1';
    }

    public function cancelEinvoice(Invoice $invoice, string $reason): ?array
    {
        if (!$invoice->irn) {
            return ['success' => false, 'error' => 'No IRN found'];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'user_name' => $this->username,
                'user_gstin' => $this->gstin,
            ])->withBasicAuth($this->username, $this->password)
            ->post($this->getApiUrl() . '/einvoice/cancel', [
                'irn' => $invoice->irn,
                'cancel_reason' => $reason,
                'cancel_remarks' => $reason,
            ]);

            if ($response->successful()) {
                $invoice->update(['einvoice_status' => 'cancelled']);
                return ['success' => true];
            }

            return ['success' => false, 'error' => $response->json()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function verifyEinvoice(string $irn): ?array
    {
        try {
            $response = Http::withHeaders([
                'user_name' => $this->username,
                'user_gstin' => $this->gstin,
            ])->withBasicAuth($this->username, $this->password)
            ->get($this->getApiUrl() . "/einvoice/{$irn}");

            if ($response->successful()) {
                return $response->json('result');
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
