<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseFollowUp extends Model
{
    protected $fillable = [
        'case_id', 'created_by', 'assigned_to', 'type', 'title', 'description',
        'scheduled_at', 'reminder_at', 'priority', 'status', 'completed_at', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'reminder_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isOverdue(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isPast() && $this->status !== 'completed';
    }

    public function isUpcoming(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture() && $this->status === 'pending';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_at', '<', now())
            ->where('status', '!=', 'completed');
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('scheduled_at', '>', now())
            ->where('scheduled_at', '<=', now()->addDays($days))
            ->where('status', 'pending');
    }
}
