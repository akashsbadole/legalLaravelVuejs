<?php

namespace App\Services;

use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Support\Collection;

class ConflictCheckService
{
    public function checkConflicts(LegalCase $case, ?int $newParticipantId = null, ?string $newParticipantRole = null): array
    {
        $conflicts = [];
        
        $currentParticipants = $case->participants()->with('cases')->get();
        
        if ($newParticipantId && $newParticipantRole === 'client') {
            $existingClients = $currentParticipants->where('pivot.role', 'client');
            foreach ($existingClients as $client) {
                $otherCases = $client->cases()
                    ->where('id', '!=', $case->id)
                    ->where('assigned_attorney_id', $case->assigned_attorney_id)
                    ->count();
                if ($otherCases > 0) {
                    $conflicts[] = [
                        'type' => 'same_client_opposing',
                        'message' => "This client is already involved in another case with the same attorney.",
                        'severity' => 'high'
                    ];
                }
            }
        }
        
        if ($newParticipantId && $newParticipantRole === 'opposing_party') {
            $opposingParties = $currentParticipants->where('pivot.role', 'opposing_party');
            foreach ($opposingParties as $opposing) {
                if ($opposing->id === $newParticipantId) {
                    $conflicts[] = [
                        'type' => 'duplicate_party',
                        'message' => "This party is already added as opposing party.",
                        'severity' => 'high'
                    ];
                }
            }
            
            $attorneyCases = LegalCase::where('assigned_attorney_id', $case->assigned_attorney_id)
                ->where('id', '!=', $case->id)
                ->whereHas('participants', function ($q) use ($newParticipantId) {
                    $q->where('user_id', $newParticipantId)->where('role', 'client');
                })->count();
            
            if ($attorneyCases > 0) {
                $conflicts[] = [
                    'type' => 'attorney_representing_opponent',
                    'message' => "The assigned attorney currently represents this party in another case.",
                    'severity' => 'critical'
                ];
            }
        }
        
        foreach ($currentParticipants as $participant) {
            if ($participant->pivot->role === 'opposing_party') {
                $otherOpposing = $currentParticipants->where('pivot.role', 'opposing_party')
                    ->where('id', '!=', $participant->id);
                foreach ($otherOpposing as $other) {
                    $conflicts[] = [
                        'type' => 'multiple_opposing',
                        'message' => "Multiple opposing parties may create conflict.",
                        'severity' => 'low'
                    ];
                }
            }
        }
        
        return $conflicts;
    }

    public function checkNewCaseConflicts(array $participantIds, ?int $attorneyId): array
    {
        $conflicts = [];
        
        if (!$attorneyId) {
            return $conflicts;
        }
        
        $attorneyCases = LegalCase::where('assigned_attorney_id', $attorneyId)
            ->whereHas('participants', function ($q) use ($participantIds) {
                $q->whereIn('user_id', $participantIds)->where('role', 'opposing_party');
            })->count();
        
        if ($attorneyCases > 0) {
            $conflicts[] = [
                'type' => 'attorney_conflict',
                'message' => "The assigned attorney has represented opposing parties in the past.",
                'severity' => 'high'
            ];
        }
        
        return $conflicts;
    }

    public function checkClientConflicts(int $clientId, int $attorneyId): array
    {
        $conflicts = [];

        // Check if client is already represented by this attorney in active cases
        $activeCases = LegalCase::where('client_id', $clientId)
            ->where('assigned_attorney_id', $attorneyId)
            ->whereHas('status', function ($q) {
                $q->where('name', '!=', 'closed');
            })
            ->count();

        if ($activeCases > 0) {
            $conflicts[] = [
                'type' => 'existing_representation',
                'message' => "This client is already represented by the assigned attorney in active cases.",
                'severity' => 'medium'
            ];
        }

        // Check if attorney represents opposing parties in client's other cases
        $clientCases = LegalCase::where('client_id', $clientId)->pluck('id');
        $conflictingCases = LegalCase::where('assigned_attorney_id', $attorneyId)
            ->whereHas('participants', function ($q) use ($clientId) {
                $q->where('user_id', $clientId)->where('role', 'opposing_party');
            })
            ->count();

        if ($conflictingCases > 0) {
            $conflicts[] = [
                'type' => 'opposing_party_conflict',
                'message' => "The assigned attorney has represented parties opposing this client in other cases.",
                'severity' => 'high'
            ];
        }

        return $conflicts;
    }
}