<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConstitutionArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_number',
        'part',
        'title',
        'content',
        'explanation',
        'category',
        'amendments',
        'related_articles',
        'is_active',
    ];

    protected $casts = [
        'amendments' => 'array',
        'related_articles' => 'array',
        'is_active' => 'boolean',
    ];

    public function references(): HasMany
    {
        return $this->hasMany(LegalReference::class, 'source_id')->where('source_type', 'constitution');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPart($query, $part)
    {
        return $query->where('part', $part);
    }
}
