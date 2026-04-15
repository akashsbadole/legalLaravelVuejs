<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseTimelineEvent extends Model
{
    protected $table = 'case_timeline_events';

    protected $fillable = ['case_id', 'user_id', 'title', 'description', 'event_type', 'event_date', 'is_milestone'];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'is_milestone' => 'boolean',
        ];
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getEventTypes(): array
    {
        return [
            'hearing' => 'Court Hearing',
            'filing' => 'Document Filing',
            'meeting' => 'Client Meeting',
            'deadline' => 'Deadline',
            'note' => 'Internal Note',
            'status_change' => 'Case Status Change',
            'order' => 'Court Order',
            'judgment' => 'Judgment',
            'settlement' => 'Settlement',
            'appeal' => 'Appeal Filed',
            'discovery' => 'Discovery',
            'deposition' => 'Deposition',
            'mediation' => 'Mediation',
            'arbitration' => 'Arbitration',
            'client_communication' => 'Client Communication',
            'follow_up' => 'Follow-up Action',
            'task_completion' => 'Task Completed',
            'evidence_submission' => 'Evidence Submission',
            'witness_statement' => 'Witness Statement',
        ];
    }

    public function getEventTypeColor(): string
    {
        return match ($this->event_type) {
            'hearing' => 'bg-red-100 text-red-800',
            'filing' => 'bg-blue-100 text-blue-800',
            'meeting' => 'bg-green-100 text-green-800',
            'deadline' => 'bg-yellow-100 text-yellow-800',
            'status_change' => 'bg-purple-100 text-purple-800',
            'judgment', 'order' => 'bg-indigo-100 text-indigo-800',
            'settlement' => 'bg-pink-100 text-pink-800',
            'client_communication' => 'bg-cyan-100 text-cyan-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getEventTypeIcon(): string
    {
        return match ($this->event_type) {
            'hearing' => '⚖️',
            'filing' => '📄',
            'meeting' => '👥',
            'deadline' => '⏰',
            'status_change' => '🔄',
            'judgment' => '📋',
            'order' => '📜',
            'settlement' => '🤝',
            'client_communication' => '💬',
            'follow_up' => '📅',
            default => '📝',
        };
    }
}
