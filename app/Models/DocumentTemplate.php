<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentTemplate extends Model
{
    protected $fillable = ['name', 'description', 'category_id', 'content', 'placeholder_format', 'version', 'is_active', 'created_by'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'version' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'template_id');
    }

    public function getPlaceholders(): array
    {
        preg_match_all('/{{\s*(\w+)\s*}}/', $this->content, $matches);
        return $matches[1] ?? [];
    }
}