<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'user_id',
        'content',
        'start_position',
        'end_position',
        'highlighted_text',
        'parent_id',
        'comment_type',
        'is_resolved',
        'resolved_at',
        'resolved_by',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_position' => 'integer',
            'end_position' => 'integer',
            'is_resolved' => 'boolean',
            'resolved_at' => 'datetime',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DocumentComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(DocumentComment::class, 'parent_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('comment_type', $type);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function resolve(User $user)
    {
        $this->is_resolved = true;
        $this->resolved_at = now();
        $this->resolved_by = $user->id;
        $this->save();
    }

    public function unresolve()
    {
        $this->is_resolved = false;
        $this->resolved_at = null;
        $this->resolved_by = null;
        $this->save();
    }
}
