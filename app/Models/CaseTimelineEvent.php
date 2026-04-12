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
            'hearing' => 'Hearing',
            'filing' => 'Filing',
            'meeting' => 'Meeting',
            'deadline' => 'Deadline',
            'note' => 'Note',
            'status_change' => 'Status Change',
            'order' => 'Court Order',
            'judgment' => 'Judgment',
        ];
    }
}