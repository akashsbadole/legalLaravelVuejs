<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotDocumentMetadata extends Model
{
    use HasFactory;

    protected $table = 'chatbot_document_metadata';

    protected $fillable = [
        'document_id',
        'key',
        'value',
        'value_type',
        'is_system',
        'is_searchable',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_searchable' => 'boolean',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(ChatbotDocument::class, 'document_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeUserDefined($query)
    {
        return $query->where('is_system', false);
    }

    public function scopeSearchable($query)
    {
        return $query->where('is_searchable', true);
    }

    public function getFormattedValueAttribute(): string
    {
        switch ($this->value_type) {
            case 'boolean':
                return $this->value ? 'Yes' : 'No';
            case 'date':
                return date('Y-m-d', strtotime($this->value));
            case 'array':
            case 'object':
                $decoded = json_decode($this->value, true);

                return is_array($decoded) ? implode(', ', $decoded) : $this->value;
            default:
                return $this->value;
        }
    }

    public function getParsedValue()
    {
        switch ($this->value_type) {
            case 'boolean':
                return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($this->value) ? (float) $this->value : $this->value;
            case 'date':
                return strtotime($this->value) ? date('Y-m-d H:i:s', strtotime($this->value)) : $this->value;
            case 'array':
            case 'object':
                $decoded = json_decode($this->value, true);

                return $decoded !== null ? $decoded : $this->value;
            default:
                return $this->value;
        }
    }

    public static function setForDocument(int $documentId, string $key, $value, string $valueType = 'string', bool $isSystem = false, ?User $user = null): self
    {
        return self::updateOrCreate(
            [
                'document_id' => $documentId,
                'key' => $key,
            ],
            [
                'value' => is_array($value) || is_object($value) ? json_encode($value) : (string) $value,
                'value_type' => $valueType,
                'is_system' => $isSystem,
                'is_searchable' => ! $isSystem,
                'created_by' => $user?->id,
            ]
        );
    }

    public static function getForDocument(int $documentId, string $key)
    {
        $metadata = self::where('document_id', $documentId)
            ->where('key', $key)
            ->first();

        return $metadata ? $metadata->getParsedValue() : null;
    }

    public static function getAllForDocument(int $documentId, bool $searchableOnly = false): array
    {
        $query = self::where('document_id', $documentId);

        if ($searchableOnly) {
            $query->where('is_searchable', true);
        }

        $metadata = $query->get();

        $result = [];
        foreach ($metadata as $item) {
            $result[$item->key] = $item->getParsedValue();
        }

        return $result;
    }
}
