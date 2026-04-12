<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVersion extends Model
{
    protected $table = 'document_versions';
    
    protected $fillable = [
        'document_id', 'version_number', 'file_path', 'file_name',
        'file_size', 'changes_description', 'created_by', 'is_current'
    ];

    protected function casts(): array
    {
        return [
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
}
