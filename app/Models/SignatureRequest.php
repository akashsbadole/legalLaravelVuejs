<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignatureRequest extends Model
{
    protected $table = 'signature_requests';

    protected $fillable = [
        'document_id', 'requested_by', 'signer_email', 'signer_name',
        'signer_phone', 'status', 'signed_at', 'signature_data',
        'ip_address', 'signed_document_path', 'esign_provider',
    ];

    protected function casts(): array
    {
        return [
            'signed_at' => 'datetime',
            'signature_data' => 'array',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'sent' => 'Sent',
            'viewed' => 'Viewed',
            'signed' => 'Signed',
            'declined' => 'Declined',
            'expired' => 'Expired',
        ];
    }

    public static function getProviders(): array
    {
        return [
            'esign' => 'eSign (India)',
            'dsc' => 'Digital Signature Certificate',
            'demo' => 'Demo Signature',
        ];
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'sent']);
    }

    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }
}
