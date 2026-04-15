<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotDocumentAccessLog extends Model
{
    use HasFactory;

    protected $table = 'chatbot_document_access_logs';

    protected $fillable = [
        'document_id',
        'user_id',
        'ip_address',
        'user_agent',
        'action',
        'access_type',
        'additional_data',
        'was_successful',
        'error_message',
        'accessed_at',
    ];

    protected function casts(): array
    {
        return [
            'additional_data' => 'array',
            'was_successful' => 'boolean',
            'accessed_at' => 'datetime',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(ChatbotDocument::class, 'document_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('was_successful', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('was_successful', false);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByAccessType($query, $type)
    {
        return $query->where('access_type', $type);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('accessed_at', '>=', now()->subHours($hours));
    }

    public function scopeByIpAddress($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    public function getActionDescriptionAttribute(): string
    {
        $descriptions = [
            'view' => 'Viewed document',
            'download' => 'Downloaded document',
            'upload' => 'Uploaded document',
            'edit' => 'Edited document',
            'delete' => 'Deleted document',
            'share' => 'Shared document',
            'search' => 'Searched document',
            'export' => 'Exported document',
            'print' => 'Printed document',
            'annotate' => 'Annotated document',
            'version_create' => 'Created new version',
        ];

        return $descriptions[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }

    public function getAccessTypeDescriptionAttribute(): string
    {
        $descriptions = [
            'web' => 'Web Browser',
            'api' => 'API Access',
            'mobile' => 'Mobile App',
            'integration' => 'Third-party Integration',
        ];

        return $descriptions[$this->access_type] ?? ucfirst($this->access_type);
    }

    public static function logAccess(
        int $documentId,
        ?User $user,
        string $action,
        string $accessType = 'web',
        array $additionalData = [],
        bool $wasSuccessful = true,
        ?string $errorMessage = null
    ): self {
        return self::create([
            'document_id' => $documentId,
            'user_id' => $user?->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'action' => $action,
            'access_type' => $accessType,
            'additional_data' => $additionalData,
            'was_successful' => $wasSuccessful,
            'error_message' => $errorMessage,
            'accessed_at' => now(),
        ]);
    }

    public static function getAccessStats(int $documentId, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $stats = self::where('document_id', $documentId)
            ->where('accessed_at', '>=', $startDate)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->pluck('count', 'action')
            ->toArray();

        $totalViews = self::where('document_id', $documentId)
            ->where('accessed_at', '>=', $startDate)
            ->where('action', 'view')
            ->count();

        $uniqueUsers = self::where('document_id', $documentId)
            ->where('accessed_at', '>=', $startDate)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        return [
            'actions' => $stats,
            'total_views' => $totalViews,
            'unique_users' => $uniqueUsers,
            'period_days' => $days,
        ];
    }

    public static function getSecurityAlerts(int $hours = 24): Collection
    {
        return self::with(['document', 'user'])
            ->where('was_successful', false)
            ->where('accessed_at', '>=', now()->subHours($hours))
            ->orderBy('accessed_at', 'desc')
            ->get();
    }

    public static function getTopAccessedDocuments(int $limit = 10, int $days = 7): Collection
    {
        return self::with('document')
            ->selectRaw('document_id, COUNT(*) as access_count')
            ->where('accessed_at', '>=', now()->subDays($days))
            ->where('action', 'view')
            ->groupBy('document_id')
            ->orderBy('access_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
