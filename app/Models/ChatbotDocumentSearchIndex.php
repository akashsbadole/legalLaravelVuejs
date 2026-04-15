<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotDocumentSearchIndex extends Model
{
    use HasFactory;

    protected $table = 'chatbot_document_search_index';

    protected $fillable = [
        'document_id',
        'content',
        'keywords',
        'entities',
        'relevance_score',
        'search_vectors',
        'indexed_at',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'entities' => 'array',
            'relevance_score' => 'decimal:2',
            'search_vectors' => 'array',
            'indexed_at' => 'datetime',
            'is_current' => 'boolean',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(ChatbotDocument::class, 'document_id');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeByRelevance($query, $minScore = null)
    {
        if ($minScore !== null) {
            return $query->where('relevance_score', '>=', $minScore);
        }

        return $query;
    }

    public function scopeRecentlyIndexed($query, $hours = 24)
    {
        return $query->where('indexed_at', '>=', now()->subHours($hours));
    }

    public function getKeywordsArray(): array
    {
        return $this->keywords ?? [];
    }

    public function getEntitiesArray(): array
    {
        return $this->entities ?? [];
    }

    public function hasKeyword(string $keyword): bool
    {
        $keywords = array_map('strtolower', $this->getKeywordsArray());

        return in_array(strtolower($keyword), $keywords);
    }

    public function hasEntity(string $entity): bool
    {
        $entities = array_map('strtolower', $this->getEntitiesArray());

        return in_array(strtolower($entity), $entities);
    }

    public function getContentSnippet(int $length = 200, ?string $query = null): string
    {
        $content = $this->content;

        if ($query) {
            // Find the query in content and get surrounding context
            $queryPos = stripos($content, $query);
            if ($queryPos !== false) {
                $start = max(0, $queryPos - $length / 2);
                $end = min(strlen($content), $queryPos + strlen($query) + $length / 2);
                $content = substr($content, $start, $end - $start);

                // Add ellipsis if truncated
                if ($start > 0) {
                    $content = '...'.$content;
                }
                if ($end < strlen($this->content)) {
                    $content = $content.'...';
                }
            }
        }

        return strlen($content) > $length ? substr($content, 0, $length).'...' : $content;
    }

    public function calculateRelevanceScore(string $query): float
    {
        $query = strtolower($query);
        $content = strtolower($this->content);

        // Simple relevance scoring based on term frequency
        $score = 0;

        // Exact phrase match gets highest score
        if (strpos($content, $query) !== false) {
            $score += 10;
        }

        // Individual word matches
        $queryWords = explode(' ', $query);
        $contentWords = str_word_count($content, 1);

        foreach ($queryWords as $word) {
            if (in_array($word, $contentWords)) {
                $score += 2;
            }
        }

        // Keyword matches
        foreach ($this->getKeywordsArray() as $keyword) {
            if (strpos($query, strtolower($keyword)) !== false) {
                $score += 3;
            }
        }

        // Entity matches
        foreach ($this->getEntitiesArray() as $entity) {
            if (strpos($query, strtolower($entity)) !== false) {
                $score += 4;
            }
        }

        return min($score, 100); // Cap at 100
    }

    public static function search(string $query, array $filters = [], int $limit = 50): Collection
    {
        $queryBuilder = self::current()
            ->with('document')
            ->whereHas('document', function ($q) {
                $q->active();
            });

        // Text search in content
        if (! empty($query)) {
            $queryBuilder->where('content', 'like', '%'.$query.'%');
        }

        // Apply filters
        if (isset($filters['category_id'])) {
            $queryBuilder->whereHas('document', function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }

        if (isset($filters['document_type'])) {
            $queryBuilder->whereHas('document', function ($q) use ($filters) {
                $q->where('document_type', $filters['document_type']);
            });
        }

        if (isset($filters['min_relevance'])) {
            $queryBuilder->where('relevance_score', '>=', $filters['min_relevance']);
        }

        return $queryBuilder->limit($limit)->get();
    }

    public static function updateForDocument(ChatbotDocument $document, string $content, array $options = []): self
    {
        // Mark existing index as not current
        self::where('document_id', $document->id)
            ->where('is_current', true)
            ->update(['is_current' => false]);

        // Create new index entry
        return self::create([
            'document_id' => $document->id,
            'content' => $content,
            'keywords' => $options['keywords'] ?? [],
            'entities' => $options['entities'] ?? [],
            'relevance_score' => $options['relevance_score'] ?? null,
            'search_vectors' => $options['search_vectors'] ?? null,
            'indexed_at' => now(),
            'is_current' => true,
        ]);
    }
}
