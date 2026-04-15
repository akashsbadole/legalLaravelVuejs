<?php

namespace App\Services;

use App\Models\LegalCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CalendarSyncService
{
    private string $provider;

    private ?string $apiKey;

    private ?string $clientId;

    private ?string $clientSecret;

    public function __construct()
    {
        $this->provider = config('services.calendar.provider', 'google');
        $this->apiKey = config('services.calendar.api_key');
        $this->clientId = config('services.calendar.client_id');
        $this->clientSecret = config('services.calendar.client_secret');
    }

    public function isEnabled(): bool
    {
        return ! empty($this->apiKey) || ! empty($this->clientId);
    }

    public function syncCaseDeadlines(LegalCase $case): array
    {
        $events = [];

        if ($case->filing_date) {
            $events[] = $this->createEvent(
                'Case Filed: '.$case->case_number,
                $case->filing_date,
                'Case was filed in court',
                'court_date'
            );
        }

        if (! empty($case->hearing_date)) {
            $events[] = $this->createEvent(
                'Hearing: '.$case->case_number,
                $case->hearing_date,
                'Court hearing scheduled',
                'hearing'
            );
        }

        if (! empty($case->next_hearing_date)) {
            $events[] = $this->createEvent(
                'Next Hearing: '.$case->case_number,
                $case->next_hearing_date,
                'Upcoming court hearing',
                'hearing'
            );
        }

        foreach ($case->tasks()->whereNotNull('due_date')->get() as $task) {
            $events[] = $this->createEvent(
                'Task Due: '.$task->title,
                $task->due_date,
                $task->description,
                'task',
                $task->priority
            );
        }

        if ($this->isEnabled()) {
            foreach ($events as $event) {
                $this->createCalendarEvent($event);
            }
        }

        return $events;
    }

    public function createCalendarEvent(array $event): array|string|null
    {
        if (! $this->isEnabled()) {
            return null;
        }

        try {
            switch ($this->provider) {
                case 'google':
                    return $this->createGoogleEvent($event);
                case 'outlook':
                    return $this->createOutlookEvent($event);
                case 'ical':
                    return $this->createIcsEvent($event);
                default:
                    return null;
            }
        } catch (\Exception $e) {
            Log::error('Calendar sync error: '.$e->getMessage());

            return null;
        }
    }

    private function createGoogleEvent(array $event): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getGoogleToken(),
            'Content-Type' => 'application/json',
        ])->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', [
            'summary' => $event['title'],
            'description' => $event['description'],
            'start' => [
                'dateTime' => Carbon::parse($event['date'])->toIso8601String(),
                'timeZone' => 'Asia/Kolkata',
            ],
            'end' => [
                'dateTime' => Carbon::parse($event['date'])->addHour()->toIso8601String(),
                'timeZone' => 'Asia/Kolkata',
            ],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60],
                    ['method' => 'popup', 'minutes' => 60],
                ],
            ],
        ]);

        return $response->json();
    }

    private function createOutlookEvent(array $event): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->getOutlookToken(),
            'Content-Type' => 'application/json',
        ])->post('https://graph.microsoft.com/v1.0/me/events', [
            'subject' => $event['title'],
            'body' => ['contentType' => 'HTML', 'content' => $event['description']],
            'start' => [
                'dateTime' => Carbon::parse($event['date'])->toIso8601String(),
                'timeZone' => 'India Standard Time',
            ],
            'end' => [
                'dateTime' => Carbon::parse($event['date'])->addHour()->toIso8601String(),
                'timeZone' => 'India Standard Time',
            ],
        ]);

        return $response->json();
    }

    private function createIcsEvent(array $event): string
    {
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//LegalFirm//Calendar//EN\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= 'UID:'.uniqid()."@legalfirm.com\r\n";
        $ics .= 'DTSTAMP:'.now()->format('YmdHis')."\r\n";
        $ics .= 'DTSTART:'.Carbon::parse($event['date'])->format('YmdHis')."\r\n";
        $ics .= 'SUMMARY:'.$event['title']."\r\n";
        $ics .= 'DESCRIPTION:'.$event['description']."\r\n";
        $ics .= "END:VEVENT\r\n";
        $ics .= "END:VCALENDAR\r\n";

        return $ics;
    }

    private function createEvent(string $title, $date, string $description, string $type, string $priority = 'medium'): array
    {
        return [
            'title' => $title,
            'date' => $date,
            'description' => $description,
            'type' => $type,
            'priority' => $priority,
        ];
    }

    private function getGoogleToken(): string
    {
        return $this->apiKey;
    }

    private function getOutlookToken(): string
    {
        return $this->apiKey;
    }

    public function fetchCourtHearings(string $caseNumber): ?array
    {
        try {
            $response = Http::get('https://ecourts-services.nic.in/api/cases', [
                'case_number' => $caseNumber,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Court API error: '.$e->getMessage());

            return null;
        }
    }

    public function generateIcsFile(LegalCase $case): string
    {
        $events = $this->syncCaseDeadlines($case);
        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "PRODID:-//LegalFirm//Case Calendar//EN\r\n";

        foreach ($events as $event) {
            $icsContent .= "BEGIN:VEVENT\r\n";
            $icsContent .= 'UID:'.uniqid()."@legalfirm.com\r\n";
            $icsContent .= 'DTSTAMP:'.now()->format('YmdHis')."\r\n";
            $icsContent .= 'DTSTART;VALUE=DATE:'.Carbon::parse($event['date'])->format('Ymd')."\r\n";
            $icsContent .= 'SUMMARY:'.$event['title']."\r\n";
            $icsContent .= 'DESCRIPTION:'.$event['description']."\r\n";
            $icsContent .= "END:VEVENT\r\n";
        }

        $icsContent .= "END:VCALENDAR\r\n";

        return $icsContent;
    }
}
