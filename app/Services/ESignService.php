<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ESignService
{
    protected $apiKey;

    protected $baseUrl = 'https://esignservice.com/api'; // Placeholder

    public function __construct()
    {
        $this->apiKey = config('services.esign.api_key');
    }

    public function requestESign(Document $document, string $aadhaarNumber): string
    {
        // Placeholder for Aadhaar eSign request
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.'/esign', [
                'document_id' => $document->id,
                'aadhaar' => $aadhaarNumber,
                'callback_url' => route('document.esign.callback', $document),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['request_id'];
            }

            Log::error('ESign API error', ['response' => $response->body()]);
            throw new \Exception('ESign request failed');
        } catch (\Exception $e) {
            Log::error('ESign exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function handleCallback(Document $document, array $data): void
    {
        if ($data['status'] === 'signed') {
            $document->update([
                'status' => 'signed',
                'signed_at' => now(),
                'esign_certificate' => $data['certificate'],
            ]);

            Log::info('Document e-signed', ['document_id' => $document->id]);
        } else {
            Log::warning('Document e-sign failed', ['document_id' => $document->id, 'reason' => $data['reason']]);
        }
    }
}
