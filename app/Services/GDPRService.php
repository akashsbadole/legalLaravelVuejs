<?php

namespace App\Services;

use App\Models\User;
use App\Models\LegalCase;
use App\Models\Invoice;
use App\Models\Document;
use Illuminate\Support\Facades\Log;

class GDPRService
{
    public static function anonymizeUser(User $user): void
    {
        $user->update([
            'name' => 'Anonymous User ' . $user->id,
            'email' => 'anonymized_' . $user->id . '@deleted.invalid',
            'phone' => null,
            'address' => null,
            'password' => bcrypt('anonymized_' . time()),
        ]);

        Log::info("User {$user->id} has been anonymized per GDPR request");
    }

    public static function deleteUserData(User $user): array
    {
        $deletedData = [
            'cases' => $user->assignedCases()->count(),
            'documents' => $user->documents()->count(),
            'time_entries' => $user->timeEntries()->count(),
            'expenses' => $user->expenses()->count(),
            'invoices_as_client' => $user->invoicesAsClient()->count(),
        ];

        $user->assignedCases()->update(['assigned_attorney_id' => null]);
        
        $user->documents()->delete();
        $user->timeEntries()->delete();
        $user->expenses()->delete();
        $user->invoicesAsClient()->delete();
        $user->cases()->detach();

        self::anonymizeUser($user);

        Log::warning("All personal data deleted for user {$user->id}", $deletedData);

        return $deletedData;
    }

    public static function exportUserData(User $user): array
    {
        return [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ],
            'cases' => $user->assignedCases()->get()->toArray(),
            'documents' => $user->documents()->get()->toArray(),
            'time_entries' => $user->timeEntries()->get()->toArray(),
            'expenses' => $user->expenses()->get()->toArray(),
            'invoices' => $user->invoicesAsClient()->get()->toArray(),
            'consents' => $user->consents()->get()->toArray(),
        ];
    }

    public static function recordConsent(User $user, string $purpose, string $action): void
    {
        $user->consents()->create([
            'purpose' => $purpose,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'granted_at' => now(),
        ]);

        Log::info("Consent recorded for user {$user->id}: {$purpose} - {$action}");
    }

    public static function hasConsent(User $user, string $purpose): bool
    {
        return $user->consents()
            ->where('purpose', $purpose)
            ->where('action', 'granted')
            ->where('granted_at', '>', now()->subYears(2))
            ->exists();
    }

    public static function processDataDeletionRequest(User $user): void
    {
        $user->update([
            'deletion_requested_at' => now(),
            'deletion_scheduled_at' => now()->addDays(30),
        ]);

        Log::info("Data deletion request created for user {$user->id}");
    }

    public static function processDataRetention(User $user): void
    {
        $cases = $user->assignedCases()
            ->where('case_status_id', function ($q) {
                $q->select('id')->from('case_statuses')->where('name', 'Closed');
            })
            ->where('filing_date', '<', now()->subYears(7))
            ->get();

        foreach ($cases as $case) {
            $case->update(['case_status_id' => null]);
        }

        Log::info("Data retention policy applied for user {$user->id}");
    }
}
