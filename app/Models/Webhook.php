<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Webhook extends Model
{
    protected $table = 'webhooks';
    
    protected $fillable = [
        'user_id', 'url', 'events', 'secret', 'is_active', 'last_triggered_at', 'failure_count'
    ];

    protected function casts(): array
    {
        return [
            'events' => 'array',
            'is_active' => 'boolean',
            'last_triggered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getEvents(): array
    {
        return [
            'case.created' => 'Case Created',
            'case.updated' => 'Case Updated',
            'case.closed' => 'Case Closed',
            'invoice.created' => 'Invoice Created',
            'invoice.paid' => 'Invoice Paid',
            'invoice.overdue' => 'Invoice Overdue',
            'task.created' => 'Task Created',
            'task.completed' => 'Task Completed',
            'document.created' => 'Document Created',
            'lead.created' => 'Lead Created',
            'lead.converted' => 'Lead Converted',
        ];
    }

    public static function getAvailableEvents(): array
    {
        return array_keys(self::getEvents());
    }
}
