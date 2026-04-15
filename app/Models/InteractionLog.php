<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InteractionLog extends Model
{
    protected $fillable = [
        'user_id', 'case_id', 'created_by', 'type', 'channel', 'subject', 'body', 'recipients',
        'sent_at', 'direction', 'status', 'notes', 'call_duration', 'call_outcome',
        'phone_number', 'call_notes',
    ];

    protected function casts(): array
    {
        return [
            'recipients' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    public function getCallDurationFormattedAttribute(): string
    {
        if (! $this->call_duration) {
            return '—';
        }

        $minutes = floor($this->call_duration / 60);
        $seconds = $this->call_duration % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function scopeCalls($query)
    {
        return $query->where('type', 'call');
    }

    public function scopeByCase($query, $caseId)
    {
        return $query->where('case_id', $caseId);
    }
}
