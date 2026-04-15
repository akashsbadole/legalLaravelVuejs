<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ChatbotDocumentCategory extends Model
{
    use HasFactory;

    protected $table = 'chatbot_document_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'category_type',
        'allowed_file_types',
        'max_file_size',
        'auto_process',
        'requires_approval',
        'document_count',
        'parent_id',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'allowed_file_types' => 'array',
            'max_file_size' => 'integer',
            'auto_process' => 'boolean',
            'requires_approval' => 'boolean',
            'document_count' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        // Update document count when documents are added/removed
        static::saved(function ($category) {
            $category->updateDocumentCount();
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChatbotDocumentCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChatbotDocumentCategory::class, 'parent_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ChatbotDocument::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('category_type', $type);
    }

    public function scopeRootCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function updateDocumentCount(): void
    {
        $this->document_count = $this->documents()->active()->count();
        $this->save();
    }

    public function getHierarchyPath(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    public function getAllowedFileTypesArray(): array
    {
        return $this->allowed_file_types ?? [];
    }

    public function allowsFileType(string $fileType): bool
    {
        $allowedTypes = $this->getAllowedFileTypesArray();

        if (empty($allowedTypes)) {
            return true; // No restrictions
        }

        return in_array(strtolower($fileType), array_map('strtolower', $allowedTypes));
    }

    public function getDocumentsCount(): int
    {
        return $this->document_count;
    }

    public function getSubcategoriesCount(): int
    {
        return $this->children()->active()->count();
    }

    public function getTotalDocumentsCount(): int
    {
        $count = $this->document_count;

        foreach ($this->children as $child) {
            $count += $child->getTotalDocumentsCount();
        }

        return $count;
    }
}
