<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = ['name', 'case_id', 'template_id', 'created_by', 'file_path', 'file_type', 'content', 'version', 'is_generated'];

    protected function casts(): array
    {
        return [
            'is_generated' => 'boolean',
            'version' => 'integer',
        ];
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}