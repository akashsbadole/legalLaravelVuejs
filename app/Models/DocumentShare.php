<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentShare extends Model
{
    protected $table = 'document_shares';

    protected $fillable = [
        'document_id', 'shared_by', 'shared_with_email', 'shared_with_name',
        'access_level', 'expires_at', 'password_protected', 'password',
        'otp_code', 'otp_expires_at', 'view_count', 'last_viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'last_viewed_at' => 'datetime',
            'password_protected' => 'boolean',
            'view_count' => 'integer',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function sharedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by');
    }

    public static function getAccessLevels(): array
    {
        return [
            'view' => 'View Only',
            'download' => 'View & Download',
            'edit' => 'Full Access',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return ! $this->isExpired() && $this->view_count < ($this->max_views ?? PHP_INT_MAX);
    }
}
