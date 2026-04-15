<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DocumentVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'version_number',
        'version_label',
        'content',
        'changes',
        'change_description',
        'created_by',
        'change_type',
        'file_path',
        'file_size',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'file_size' => 'integer',
            'is_current' => 'boolean',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getNextVersionNumber(int $documentId): int
    {
        $lastVersion = self::where('document_id', $documentId)
            ->orderBy('version_number', 'desc')
            ->first();

        return $lastVersion ? $lastVersion->version_number + 1 : 1;
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeByChangeType($query, $changeType)
    {
        return $query->where('change_type', $changeType);
    }

    public function getFileUrlAttribute(): string
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }

        return '';
    }
}
