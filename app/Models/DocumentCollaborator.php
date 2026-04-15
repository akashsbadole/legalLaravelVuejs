<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentCollaborator extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'permission_level',
        'access_type',
        'expires_at',
        'can_download',
        'can_share',
        'can_delete',
        'invited_by',
        'last_accessed_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_accessed_at' => 'datetime',
            'can_download' => 'boolean',
            'can_share' => 'boolean',
            'can_delete' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeByPermissionLevel($query, $level)
    {
        return $query->where('permission_level', $level);
    }

    public function hasPermission(string $permission): bool
    {
        switch ($permission) {
            case 'read':
                return in_array($this->access_type, ['read', 'write', 'comment', 'approve']);
            case 'write':
                return in_array($this->access_type, ['write', 'approve']);
            case 'comment':
                return in_array($this->access_type, ['comment', 'write', 'approve']);
            case 'approve':
                return $this->access_type === 'approve';
            case 'download':
                return $this->can_download;
            case 'share':
                return $this->can_share;
            case 'delete':
                return $this->can_delete;
            default:
                return false;
        }
    }

    public function updateLastAccessed()
    {
        $this->last_accessed_at = now();
        $this->save();
    }
}
