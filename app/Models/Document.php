<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'template_id',
        'case_id',
        'client_id',
        'created_by',
        'assigned_to',
        'content',
        'metadata',
        'placeholders_filled',
        'completion_percentage',
        'due_date',
        'signed_at',
        'file_path',
        'file_name',
        'file_size',
        'file_mime_type',
        'is_encrypted',
        'requires_signature',
        'is_collaborative',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'placeholders_filled' => 'array',
            'completion_percentage' => 'decimal:2',
            'due_date' => 'datetime',
            'signed_at' => 'datetime',
            'file_size' => 'integer',
            'is_encrypted' => 'boolean',
            'requires_signature' => 'boolean',
            'is_collaborative' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class, 'case_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class, 'document_id')->orderBy('version_number', 'desc');
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(DocumentCollaborator::class, 'document_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(DocumentComment::class, 'document_id');
    }

    public function annotations(): HasMany
    {
        return $this->hasMany(DocumentAnnotation::class, 'document_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>=', now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'completed');
    }

    public function getFileUrlAttribute(): string
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }

        return '';
    }

    public function updateCompletionPercentage()
    {
        // Calculate completion based on placeholders filled
        $placeholders = $this->template?->placeholders ?? [];
        $filled = $this->placeholders_filled ?? [];

        if (empty($placeholders)) {
            $this->completion_percentage = 100;
        } else {
            $filledCount = count(array_intersect_key($filled, array_flip($placeholders)));
            $this->completion_percentage = round(($filledCount / count($placeholders)) * 100, 2);
        }

        $this->save();
    }

    public function canBeAccessedBy(User $user): bool
    {
        // Check if user is the creator, assigned person, or collaborator
        if ($this->created_by === $user->id || $this->assigned_to === $user->id) {
            return true;
        }

        return $this->collaborators()->where('user_id', $user->id)->where('is_active', true)->exists();
    }
}
