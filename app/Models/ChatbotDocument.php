<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ChatbotDocument extends Model
{
    use HasFactory;

    protected $table = 'chatbot_documents';

    protected $fillable = [
        'title',
        'description',
        'document_type',
        'file_format',
        'original_filename',
        'file_path',
        'file_url',
        'file_size',
        'mime_type',
        'extracted_text',
        'ai_analysis',
        'metadata',
        'language',
        'category_id',
        'uploaded_by',
        'last_modified_by',
        'processing_status',
        'processing_error',
        'processed_at',
        'is_indexed',
        'is_encrypted',
        'is_public',
        'download_count',
        'view_count',
        'last_accessed_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'extracted_text' => 'array',
            'ai_analysis' => 'array',
            'metadata' => 'array',
            'file_size' => 'integer',
            'processed_at' => 'datetime',
            'last_accessed_at' => 'datetime',
            'is_indexed' => 'boolean',
            'is_encrypted' => 'boolean',
            'is_public' => 'boolean',
            'download_count' => 'integer',
            'view_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChatbotDocumentCategory::class, 'category_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function lastModifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }

    public function metadata(): HasMany
    {
        return $this->hasMany(ChatbotDocumentMetadata::class, 'document_id');
    }

    public function searchIndex(): HasMany
    {
        return $this->hasMany(ChatbotDocumentSearchIndex::class, 'document_id');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(ChatbotDocumentAccessLog::class, 'document_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeProcessed($query)
    {
        return $query->where('processing_status', 'completed');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeRecentlyAccessed($query, $days = 30)
    {
        return $query->where('last_accessed_at', '>=', now()->subDays($days));
    }

    public function getFileUrlAttribute(): string
    {
        if ($this->file_url) {
            return $this->file_url;
        }

        if ($this->file_path) {
            return Storage::url($this->file_path);
        }

        return '';
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2).' KB';
        } else {
            return $bytes.' bytes';
        }
    }

    public function getProcessingStatusBadgeAttribute(): array
    {
        return match ($this->processing_status) {
            'pending' => ['label' => 'Pending', 'color' => 'yellow'],
            'processing' => ['label' => 'Processing', 'color' => 'blue'],
            'completed' => ['label' => 'Completed', 'color' => 'green'],
            'failed' => ['label' => 'Failed', 'color' => 'red'],
            default => ['label' => 'Unknown', 'color' => 'gray']
        };
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
        $this->updateLastAccessed();
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
        $this->updateLastAccessed();
    }

    public function updateLastAccessed(): void
    {
        $this->last_accessed_at = now();
        $this->save();
    }

    public function canBeAccessedBy(User $user): bool
    {
        // Check if user is the uploader or document is public
        return $this->uploaded_by === $user->id || $this->is_public;
    }

    public function getExtractedTextContent(): ?string
    {
        if (is_array($this->extracted_text)) {
            return $this->extracted_text['content'] ?? null;
        }

        return $this->extracted_text;
    }

    public function getAiAnalysisSummary(): ?array
    {
        return $this->ai_analysis;
    }

    public function addToSearchIndex(string $content, array $options = []): void
    {
        $this->searchIndex()->updateOrCreate(
            ['document_id' => $this->id, 'is_current' => true],
            [
                'content' => $content,
                'keywords' => $options['keywords'] ?? [],
                'entities' => $options['entities'] ?? [],
                'relevance_score' => $options['relevance_score'] ?? null,
                'search_vectors' => $options['search_vectors'] ?? null,
                'indexed_at' => now(),
                'is_current' => true,
            ]
        );
    }

    public function logAccess(?User $user, string $action, array $additionalData = []): void
    {
        $this->accessLogs()->create([
            'user_id' => $user?->id,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'additional_data' => $additionalData,
            'was_successful' => true,
            'accessed_at' => now(),
        ]);
    }
}
