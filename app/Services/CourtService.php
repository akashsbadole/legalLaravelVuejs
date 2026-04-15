<?php

namespace App\Services;

use App\Models\LegalCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CourtService
{
    protected $apiKey;

    protected $baseUrl = 'https://api.ecourts.gov.in'; // Placeholder, real API

    public function __construct()
    {
        $this->apiKey = config('services.ecourts.api_key');
    }

    public function getCaseStatus(string $caseNumber): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl.'/cases/'.$caseNumber);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('eCourts API error', ['status' => $response->status(), 'body' => $response->body()]);

            return null;
        } catch (\Exception $e) {
            Log::error('eCourts API exception', ['error' => $e->getMessage()]);

            return null;
        }
    }

    public function getHearingDates(string $caseNumber): array
    {
        $status = $this->getCaseStatus($caseNumber);

        return $status ? ($status['hearings'] ?? []) : [];
    }

    public function syncCase(LegalCase $case): void
    {
        if (! $case->court_case_number) {
            return;
        }

        $status = $this->getCaseStatus($case->court_case_number);

        if ($status) {
            $case->update([
                'court_status' => $status['status'] ?? 'unknown',
                'next_hearing_date' => $status['next_hearing'] ?? null,
            ]);

            // Log sync
            Log::info('Case synced with eCourts', ['case_id' => $case->id]);
        }
    }
}
